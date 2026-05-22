<?php

namespace App\Models\Concerns;

use App\Models\Scopes\TenantScope;
use App\Models\Tenant;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Drop this trait on any model that is owned by a tenant. It:
 *
 *   1. Applies the global TenantScope so reads are auto-filtered.
 *   2. Stamps `tenant_id` on create from the current TenantContext.
 *   3. Exposes the tenant() relationship.
 *
 * Override getTenantColumn() if a table uses a non-default FK name.
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($model) {
            $context = app(TenantContext::class);

            if ($context->has() && empty($model->{$model->getTenantColumn()})) {
                $model->{$model->getTenantColumn()} = $context->id();
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function getTenantColumn(): string
    {
        return 'tenant_id';
    }

    /** Escape hatch: query without the tenant filter (use sparingly). */
    public static function withoutTenancy(): \Illuminate\Database\Eloquent\Builder
    {
        return static::withoutGlobalScope(TenantScope::class);
    }
}
