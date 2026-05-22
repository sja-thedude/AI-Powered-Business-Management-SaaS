<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\Crm\ClientController;
use App\Http\Controllers\Api\V1\Crm\DealController;
use App\Http\Controllers\Api\V1\Crm\LeadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API v1 (token-authenticated via Sanctum)
|--------------------------------------------------------------------------
| The "API-first" surface for integrations and the mobile app. Token auth via
| Sanctum, then `tenant` resolves the workspace from the token's owner so all
| queries are tenant-scoped exactly like the web app.
*/

Route::prefix('v1')->group(function () {
    // Public: exchange credentials for a personal access token.
    Route::post('auth/token', [AuthController::class, 'issueToken']);

    Route::middleware(['auth:sanctum', 'tenant'])->group(function () {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'revokeToken']);

        // CRM resources (plan-gated like the web routes).
        Route::middleware('module:crm')->prefix('crm')->group(function () {
            Route::apiResource('leads', LeadController::class);
            Route::apiResource('clients', ClientController::class);
            Route::apiResource('deals', DealController::class);
        });
    });
});
