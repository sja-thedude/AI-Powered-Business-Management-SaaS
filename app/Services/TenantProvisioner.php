<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use App\Support\Permissions;
use App\Support\TenantContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Owns the "create a new workspace" transaction: the tenant record, its
 * per-tenant roles, the trial window, and the first Owner user. Called from
 * registration and from seeders. Everything runs inside a DB transaction so a
 * half-provisioned tenant can never exist.
 */
class TenantProvisioner
{
    public function __construct(
        protected TenantContext $context,
        protected PermissionRegistrar $registrar,
    ) {
    }

    public function provision(array $tenantData, array $ownerData): User
    {
        return DB::transaction(function () use ($tenantData, $ownerData) {
            $tenant = Tenant::create([
                'name'     => $tenantData['name'],
                'slug'     => $this->uniqueSlug($tenantData['name']),
                'plan'     => $tenantData['plan'] ?? 'starter',
                'timezone' => $tenantData['timezone'] ?? 'UTC',
                'currency' => $tenantData['currency'] ?? 'USD',
            ]);

            // Generic Cashier trial — billing is active without a card for N days.
            $tenant->trial_ends_at = now()->addDays(config('billing.trial_days'));
            $tenant->onboarded_at = now();
            $tenant->save();

            // Scope subsequent role/user creation to this tenant.
            $this->context->set($tenant);
            $this->registrar->setPermissionsTeamId($tenant->id);

            $this->createRoles($tenant);

            $owner = User::create([
                'tenant_id' => $tenant->id,
                'name'      => $ownerData['name'],
                'email'     => $ownerData['email'],
                'password'  => $ownerData['password'], // hashed by the cast
                'position'  => 'Owner',
            ]);

            $owner->assignRole('Owner');

            return $owner;
        });
    }

    /** Create the default per-tenant roles and attach their permissions. */
    protected function createRoles(Tenant $tenant): void
    {
        // Permissions are global (no tenant_id column); only roles are per-tenant.
        $catalog = Permission::pluck('id', 'name');

        foreach (Permissions::roleMatrix() as $roleName => $abilities) {
            $role = Role::firstOrCreate([
                'name'       => $roleName,
                'guard_name' => 'web',
                'tenant_id'  => $tenant->id,
            ]);

            if ($abilities === ['*']) {
                continue; // Owner bypasses checks via Gate::before.
            }

            $ids = collect($abilities)
                ->map(fn ($name) => $catalog[$name] ?? null)
                ->filter()
                ->all();

            $role->syncPermissions(Permission::whereIn('id', $ids)->get());
        }
    }

    protected function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'workspace';
        $slug = $base;
        $i = 1;

        while (Tenant::where('slug', $slug)->exists()) {
            $slug = "{$base}-".(++$i);
        }

        return $slug;
    }
}
