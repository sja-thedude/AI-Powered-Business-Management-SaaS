<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TenantProvisioner;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register', [
            'plans' => config('billing.plans'),
        ]);
    }

    /**
     * Sign up creates a brand-new workspace (tenant), its roles, a 14-day
     * trial, and the first Owner — then logs them in.
     */
    public function store(Request $request, TenantProvisioner $provisioner): RedirectResponse
    {
        $validated = $request->validate([
            'company'  => ['required', 'string', 'max:255'],
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'plan'     => ['nullable', 'string', 'in:starter,growth,scale'],
        ]);

        $owner = $provisioner->provision(
            tenantData: ['name' => $validated['company'], 'plan' => $validated['plan'] ?? 'starter'],
            ownerData: [
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => $validated['password'],
            ],
        );

        event(new Registered($owner));
        Auth::login($owner);

        return redirect()->intended(route('dashboard'));
    }
}
