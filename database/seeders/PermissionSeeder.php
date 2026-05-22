<?php

namespace Database\Seeders;

use App\Support\Permissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeds the global permission catalog (tenant_id = null). Roles are created
 * per-tenant by the TenantProvisioner and reference these shared permissions.
 */
class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Permissions are global, not bound to any tenant team.
        app(PermissionRegistrar::class)->setPermissionsTeamId(null);

        foreach (Permissions::all() as $name) {
            Permission::firstOrCreate([
                'name'       => $name,
                'guard_name' => 'web',
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
