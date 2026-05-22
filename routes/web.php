<?php

use App\Http\Controllers\Billing\SubscriptionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes (Inertia + Vue)
|--------------------------------------------------------------------------
| Per-module routes live in routes/modules/*.php and are auto-loaded in
| bootstrap/app.php. This file holds the shell: marketing, auth, dashboard,
| billing and workspace settings.
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : Inertia::render('Welcome', [
            'plans' => config('billing.plans'),
            'modules' => \App\Support\Modules::all(),
        ]);
})->name('home');

require __DIR__.'/auth.php';

Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Billing & subscriptions (always reachable, even when unpaid).
    Route::prefix('billing')->name('billing.')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index'])->name('index');
        Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');
        Route::post('/swap', [SubscriptionController::class, 'swap'])->name('swap');
        Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
        Route::post('/resume', [SubscriptionController::class, 'resume'])->name('resume');
        Route::get('/portal', [SubscriptionController::class, 'portal'])->name('portal');
    });

    // Workspace settings & team management.
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::put('/workspace', [SettingsController::class, 'updateWorkspace'])->name('workspace');
        Route::get('/team', [SettingsController::class, 'team'])->name('team');
        Route::get('/audit', [SettingsController::class, 'audit'])
            ->middleware('permission:audit.view')->name('audit');
    });
});
