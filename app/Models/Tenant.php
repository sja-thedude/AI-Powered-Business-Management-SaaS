<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

/**
 * The tenant (workspace/company) is the billing entity and the isolation
 * boundary for all business data. It is Cashier "Billable", so subscription
 * helpers ($tenant->subscribed(), ->onTrial(), ->newSubscription()) operate
 * at the workspace level.
 */
class Tenant extends Model
{
    use HasFactory, Notifiable, SoftDeletes, Billable;

    protected $fillable = [
        'name', 'slug', 'domain', 'plan', 'timezone', 'currency',
        'logo_path', 'settings', 'limits', 'is_active', 'onboarded_at',
    ];

    protected function casts(): array
    {
        return [
            'settings'      => 'array',
            'limits'        => 'array',
            'is_active'     => 'boolean',
            'onboarded_at'  => 'datetime',
            'trial_ends_at' => 'datetime',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function owner(): ?User
    {
        return $this->users()->whereHas('roles', fn ($q) => $q->where('name', 'Owner'))->first();
    }

    /* ----------------------------------------------------------------
     | Plan & module gating
     | ---------------------------------------------------------------- */

    public function planConfig(): array
    {
        return config("billing.plans.{$this->plan}", config('billing.plans.starter'));
    }

    public function tierLevel(?string $tier = null): int
    {
        $order = config('billing.tier_order');

        return (int) array_search($tier ?? $this->plan, $order, true);
    }

    /** Does this tenant's current plan unlock the given module key? */
    public function canUseModule(string $moduleKey): bool
    {
        $module = config("modules.{$moduleKey}");

        if (! $module) {
            return false;
        }

        return $this->tierLevel() >= $this->tierLevel($module['min_plan']);
    }

    public function limit(string $key, mixed $default = 0): mixed
    {
        return data_get($this->planConfig(), "limits.{$key}", $default);
    }

    /** Billing is active if subscribed OR still inside the generic trial. */
    public function hasActiveBilling(): bool
    {
        return $this->subscribed() || $this->onTrial() || $this->onGenericTrial();
    }
}
