<?php

use App\Http\Controllers\Hr\HrController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant', 'subscribed', 'module:hr'])
    ->prefix('hr')
    ->name('hr.')
    ->group(function () {
        Route::get('/', [HrController::class, 'index'])->name('index');
    });
