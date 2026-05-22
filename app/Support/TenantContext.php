<?php

namespace App\Support;

use App\Models\Tenant;

/**
 * Holds the tenant for the current request/job lifecycle. Registered as a
 * singleton in the container so the global TenantScope and the
 * BelongsToTenant trait can resolve "who am I scoped to?" without threading
 * the tenant through every call.
 *
 * Set by the IdentifyTenant middleware (web/API) or explicitly inside queued
 * jobs via TenantContext::set(). When no tenant is bound (e.g. the central
 * super-admin area or console), scoping is simply not applied.
 */
class TenantContext
{
    protected ?Tenant $tenant = null;

    public function set(?Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function get(): ?Tenant
    {
        return $this->tenant;
    }

    public function id(): ?int
    {
        return $this->tenant?->id;
    }

    public function has(): bool
    {
        return $this->tenant !== null;
    }

    public function forget(): void
    {
        $this->tenant = null;
    }

    /** Run a callback as a given tenant, restoring the previous one after. */
    public function run(Tenant $tenant, callable $callback): mixed
    {
        $previous = $this->tenant;
        $this->tenant = $tenant;

        try {
            return $callback();
        } finally {
            $this->tenant = $previous;
        }
    }
}
