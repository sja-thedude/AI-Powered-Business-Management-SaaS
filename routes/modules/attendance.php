<?php

use App\Http\Controllers\Attendance\AttendanceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant', 'subscribed', 'module:attendance'])
    ->prefix('attendance')
    ->name('attendance.')
    ->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
    });
