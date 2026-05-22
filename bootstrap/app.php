<?php

use App\Http\Middleware\EnsureModuleEnabled;
use App\Http\Middleware\EnsureTenantSubscribed;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\IdentifyTenant;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function () {
            // Per-module route files are auto-loaded so new modules drop in
            // their own routes/modules/<module>.php without touching core.
            foreach (glob(base_path('routes/modules/*.php')) as $moduleRoutes) {
                Illuminate\Support\Facades\Route::middleware('web')
                    ->group($moduleRoutes);
            }
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Inertia shares auth/tenant/nav on every web response.
        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Route-level aliases. `tenant` runs *after* auth so the tenant can be
        // resolved from the authenticated user (web session or Sanctum token).
        $middleware->alias([
            'tenant'     => IdentifyTenant::class,
            'module'     => EnsureModuleEnabled::class,
            'subscribed' => EnsureTenantSubscribed::class,
            'role'       => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response) {
            return $response;
        });
    })->create();
