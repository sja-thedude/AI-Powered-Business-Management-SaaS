<?php

namespace App\Http\Middleware;

use App\Support\Modules;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Props shared with every Inertia response. Keep this lean — it ships on
     * every page load. Tenant, the authenticated user (with permissions), the
     * plan-aware module nav, and flash messages live here.
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $tenant = app(TenantContext::class)->get();

        return [
            ...parent::share($request),

            'auth' => [
                'user' => $user ? [
                    'id'          => $user->id,
                    'name'        => $user->name,
                    'email'       => $user->email,
                    'initials'    => $user->initials(),
                    'position'    => $user->position,
                    'avatar'      => $user->avatar_path,
                    'roles'       => $user->getRoleNames(),
                    'permissions' => $user->getAllPermissions()->pluck('name'),
                ] : null,
            ],

            'tenant' => $tenant ? [
                'id'       => $tenant->id,
                'name'     => $tenant->name,
                'slug'     => $tenant->slug,
                'plan'     => $tenant->plan,
                'logo'     => $tenant->logo_path,
                'currency' => $tenant->currency,
                'on_trial' => $tenant->hasActiveBilling() && ! $tenant->subscribed(),
                'subscribed' => $tenant->subscribed(),
            ] : null,

            // Plan-aware navigation: only modules the tenant can access.
            'nav' => $tenant ? Modules::navFor($tenant) : [],

            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],

            'ziggy' => fn () => [
                'location' => $request->url(),
            ],
        ];
    }
}
