<?php

use App\Http\Controllers\Payroll\PayrollController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant', 'subscribed', 'module:payroll'])
    ->prefix('payroll')
    ->name('payroll.')
    ->group(function () {
        Route::get('/', [PayrollController::class, 'index'])->name('index');
    });
