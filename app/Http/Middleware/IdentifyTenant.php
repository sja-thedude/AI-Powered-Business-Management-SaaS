<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;

/**
 * Binds the current tenant for the request. Resolution order:
 *
 *   1. The authenticated user's tenant (the common case for app routes).
 *   2. Subdomain / custom domain (acme.novabiz.ai), useful for the public
 *      client portal and pre-login marketing pages.
 *
 * Once resolved, it also points spatie/permission's "team" at the tenant so
 * role checks are correctly isolated.
 */
class IdentifyTenant
{
    public function __construct(protected TenantContext $context)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->resolveFromUser($request) ?? $this->resolveFromHost($request);

        if ($tenant) {
            if (! $tenant->is_active) {
                abort(403, 'This workspace has been suspended.');
            }

            $this->context->set($tenant);
            app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
            config(['app.timezone' => $tenant->timezone]);

            // spatie/permission scopes roles by the current team. Drop any
            // role/permission relations cached under a previous team so they
            // re-resolve against this tenant.
            if ($user = $request->user()) {
                $user->unsetRelation('roles')->unsetRelation('permissions');
            }
        }

        return $next($request);
    }

    protected function resolveFromUser(Request $request): ?Tenant
    {
        $user = $request->user();

        return $user && $user->tenant_id
            ? Tenant::find($user->tenant_id)
            : null;
    }

    protected function resolveFromHost(Request $request): ?Tenant
    {
        $host = $request->getHost();
        $central = config('app.central_domain');

        // Custom domain match first.
        if ($tenant = Tenant::where('domain', $host)->first()) {
            return $tenant;
        }

        // Then subdomain of the central domain: "acme" in acme.novabiz.ai
        if ($central && str_ends_with($host, '.'.$central)) {
            $slug = str_replace('.'.$central, '', $host);

            return Tenant::where('slug', $slug)->first();
        }

        return null;
    }
}
