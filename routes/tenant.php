<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Tenant\BrandSettingController;
use App\Http\Controllers\Api\V1\Tenant\CategoryController;
use App\Http\Controllers\Api\V1\Tenant\TenantAuthController;
use App\Http\Middleware\EnsureTenantIsActive;
use App\Http\Middleware\InitializeTenancyByHeaderOrDomain;
use App\Http\Middleware\PreventAccessFromCentralDomainsExceptInitialized;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'web',
    InitializeTenancyByHeaderOrDomain::class,
    PreventAccessFromCentralDomainsExceptInitialized::class,
    EnsureTenantIsActive::class,
])->group(function () {
    Route::get('/', function () {
        return 'This is your multi-tenant application. The id of the current tenant is '.tenant('id');
    });
});

Route::middleware([
    'api',
    InitializeTenancyByHeaderOrDomain::class,
    PreventAccessFromCentralDomainsExceptInitialized::class,
    EnsureTenantIsActive::class,
])->prefix('api/v1')->group(function () {
    // Tenant Auth Routes
    Route::prefix('auth')->group(function () {
        Route::post('login', [TenantAuthController::class, 'login']);

        Route::middleware('auth:tenant_api')->group(function () {
            Route::get('me', [TenantAuthController::class, 'me']);
            Route::post('logout', [TenantAuthController::class, 'logout']);
        });
    });

    // Public Brand Settings GET route
    Route::get('brand-settings', [BrandSettingController::class, 'show']);

    // Authenticated Brand Settings PUT route
    Route::middleware('auth:tenant_api')->group(function () {
        Route::put('brand-settings', [BrandSettingController::class, 'update']);
    });

    Route::apiResource('categories', CategoryController::class);
});
