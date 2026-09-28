<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::post('central-login', 'login');
        });
    });

    Route::prefix('admin')->middleware('auth:api')->group(function () {
        Route::post('tenants/{tenant}/reactivate', [TenantController::class, 'reactivate']);
        Route::apiResource('tenants', TenantController::class);
    });
});
