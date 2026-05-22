<?php

use App\Http\Controllers\Invoicing\InvoicingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant', 'subscribed', 'module:invoicing'])
    ->prefix('invoicing')
    ->name('invoicing.')
    ->group(function () {
        Route::get('/', [InvoicingController::class, 'index'])->name('index');
    });
