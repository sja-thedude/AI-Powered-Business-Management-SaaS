<?php

namespace App\Models\Scopes;

use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Global scope that constrains every query on a tenant-owned model to the
 * currently bound tenant. This is the backbone of row-level multi-tenancy:
 * application code never has to remember to add `where tenant_id = ...`.
 *
 * When no tenant is bound (console, super-admin), the scope is a no-op so
 * platform-level tooling can still see across tenants deliberately.
 */
class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $tenant = app(TenantContext::class);

        if ($tenant->has()) {
            $builder->where($model->getTable().'.'.$model->getTenantColumn(), $tenant->id());
        }
    }
}
