<?php

use App\Http\Controllers\Inventory\InventoryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant', 'subscribed', 'module:inventory'])
    ->prefix('inventory')
    ->name('inventory.')
    ->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
    });
