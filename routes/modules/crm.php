<?php

use App\Http\Controllers\Crm\ActivityController;
use App\Http\Controllers\Crm\ClientController;
use App\Http\Controllers\Crm\CrmDashboardController;
use App\Http\Controllers\Crm\DealController;
use App\Http\Controllers\Crm\LeadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CRM Module Routes
|--------------------------------------------------------------------------
| Auto-loaded by bootstrap/app.php. Guarded by auth + tenant context +
| the `module:crm` plan gate + a subscription wall.
*/

Route::middleware(['auth', 'tenant', 'subscribed', 'module:crm'])
    ->prefix('crm')
    ->name('crm.')
    ->group(function () {
        Route::get('/', CrmDashboardController::class)->name('dashboard');

        // Leads
        Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
        Route::get('leads/create', [LeadController::class, 'create'])->name('leads.create');
        Route::post('leads', [LeadController::class, 'store'])->name('leads.store');
        Route::get('leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
        Route::get('leads/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit');
        Route::put('leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
        Route::delete('leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');
        Route::post('leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');

        // Clients
        Route::resource('clients', ClientController::class)->except(['show']);
        Route::get('clients/{client}', [ClientController::class, 'show'])->name('clients.show');

        // Deals (Kanban board)
        Route::get('deals', [DealController::class, 'board'])->name('deals.board');
        Route::post('deals', [DealController::class, 'store'])->name('deals.store');
        Route::put('deals/{deal}', [DealController::class, 'update'])->name('deals.update');
        Route::patch('deals/{deal}/move', [DealController::class, 'move'])->name('deals.move');
        Route::post('deals/{deal}/won', [DealController::class, 'won'])->name('deals.won');
        Route::post('deals/{deal}/lost', [DealController::class, 'lost'])->name('deals.lost');
        Route::delete('deals/{deal}', [DealController::class, 'destroy'])->name('deals.destroy');

        // Activities (communication history)
        Route::post('activities', [ActivityController::class, 'store'])->name('activities.store');
        Route::delete('activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');
    });
