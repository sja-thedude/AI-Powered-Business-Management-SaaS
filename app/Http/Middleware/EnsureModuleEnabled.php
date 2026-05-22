<?php

namespace App\Http\Middleware;

use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Route guard: blocks access to a module the tenant's plan doesn't include.
 * Usage: ->middleware('module:inventory')
 */
class EnsureModuleEnabled
{
    public function __construct(protected TenantContext $context)
    {
    }

    public function handle(Request $request, Closure $next, string $moduleKey): Response
    {
        $tenant = $this->context->get();

        if (! $tenant || ! $tenant->canUseModule($moduleKey)) {
            if ($request->expectsJson()) {
                abort(402, "Your plan does not include the {$moduleKey} module.");
            }

            return redirect()->route('billing.index')
                ->with('error', 'Upgrade your plan to unlock this module.');
        }

        return $next($request);
    }
}
