<?php

namespace App\Http\Middleware;

use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Billing wall. Lets the request through while the tenant is subscribed or on
 * trial; otherwise bounces to the billing page. Apply to module routes that
 * should be paywalled (the dashboard/billing/settings stay accessible).
 */
class EnsureTenantSubscribed
{
    public function __construct(protected TenantContext $context)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->context->get();

        if ($tenant && ! $tenant->hasActiveBilling()) {
            if ($request->expectsJson()) {
                abort(402, 'Subscription required.');
            }

            return redirect()->route('billing.index')
                ->with('error', 'Your trial has ended — choose a plan to continue.');
        }

        return $next($request);
    }
}
