<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

/**
 * Tenant subscription management via Laravel Cashier (Stripe). The billable
 * entity is the Tenant, so every action operates on the current workspace.
 * Checkout uses Stripe Checkout (hosted) to stay PCI-light.
 */
class SubscriptionController extends Controller
{
    public function __construct(protected TenantContext $context)
    {
    }

    public function index()
    {
        $tenant = $this->context->get();
        $subscription = $tenant->subscription('default');

        return Inertia::render('Billing/Index', [
            'plans' => config('billing.plans'),
            'current' => [
                'plan'        => $tenant->plan,
                'on_trial'    => $tenant->onGenericTrial() || $tenant->onTrial(),
                'trial_ends'  => $tenant->trial_ends_at,
                'subscribed'  => $tenant->subscribed(),
                'on_grace'    => $subscription?->onGracePeriod() ?? false,
                'cancelled'   => $subscription?->canceled() ?? false,
                'ends_at'     => $subscription?->ends_at,
                'renews_at'   => $subscription?->asStripeSubscription()?->current_period_end ?? null,
            ],
        ]);
    }

    public function subscribe(Request $request)
    {
        Gate::authorize('billing.manage');

        $data = $request->validate([
            'plan' => ['required', 'string', 'in:starter,growth,scale'],
        ]);

        $tenant = $this->context->get();
        $priceId = config("billing.plans.{$data['plan']}.stripe_price");

        $checkout = $tenant
            ->newSubscription('default', $priceId)
            ->trialUntil($tenant->trial_ends_at ?? now()->addDay())
            ->checkout([
                'success_url' => route('billing.index').'?status=success',
                'cancel_url'  => route('billing.index').'?status=cancelled',
                'metadata'    => ['tenant_id' => $tenant->id, 'plan' => $data['plan']],
            ]);

        // Reflect the selected tier immediately for module gating; the webhook
        // confirms the source of truth once Stripe finalizes payment.
        $tenant->update(['plan' => $data['plan']]);

        return Inertia::location($checkout->url);
    }

    public function swap(Request $request)
    {
        Gate::authorize('billing.manage');

        $data = $request->validate([
            'plan' => ['required', 'string', 'in:starter,growth,scale'],
        ]);

        $tenant = $this->context->get();
        $priceId = config("billing.plans.{$data['plan']}.stripe_price");

        $tenant->subscription('default')->swap($priceId);
        $tenant->update(['plan' => $data['plan']]);

        return back()->with('success', 'Your plan has been updated.');
    }

    public function cancel()
    {
        Gate::authorize('billing.manage');

        $this->context->get()->subscription('default')->cancel();

        return back()->with('success', 'Subscription cancelled — access continues until period end.');
    }

    public function resume()
    {
        Gate::authorize('billing.manage');

        $this->context->get()->subscription('default')->resume();

        return back()->with('success', 'Subscription resumed.');
    }

    public function portal(Request $request)
    {
        Gate::authorize('billing.manage');

        return Inertia::location(
            $this->context->get()->billingPortalUrl(route('billing.index'))
        );
    }
}
