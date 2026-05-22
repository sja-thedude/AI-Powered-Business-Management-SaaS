<?php

namespace App\Providers;

use App\Models\Tenant;
use App\Services\AI\AiManager;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Single source of truth for "who is the current tenant" across the
        // request lifecycle, the global TenantScope, and queued jobs.
        $this->app->singleton(TenantContext::class);

        // Swappable AI gateway (null driver offline, Anthropic in production).
        $this->app->singleton(AiManager::class);
    }

    public function boot(): void
    {
        // The tenant (workspace) is the Cashier billable entity.
        Cashier::useCustomerModel(Tenant::class);
        Cashier::calculateTaxes();

        // Catch mass-assignment typos in development without enabling
        // lazy-load prevention (which would require eager-loading everywhere).
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());

        // The workspace Owner implicitly has every ability within their tenant;
        // the platform super-admin (no tenant) can do anything anywhere.
        Gate::before(function ($user, $ability) {
            if ($user->isPlatformAdmin()) {
                return true;
            }

            return $user->hasRole('Owner') ? true : null;
        });
    }
}
