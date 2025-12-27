<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/auth/login', [\App\Http\Controllers\Api\V1\AuthController::class, 'login']);
    Route::post('/auth/register', [\App\Http\Controllers\Api\V1\AuthController::class, 'register']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::post('/auth/logout', [\App\Http\Controllers\Api\V1\AuthController::class, 'logout']);
        Route::get('/auth/user', [\App\Http\Controllers\Api\V1\AuthController::class, 'user']);

        // Roles
        Route::apiResource('roles', \App\Http\Controllers\Api\V1\RoleController::class);
        Route::post('roles/{role}/restore', [\App\Http\Controllers\Api\V1\RoleController::class, 'restore'])
            ->withTrashed();

        // Permissions
        Route::get('permissions', [\App\Http\Controllers\Api\V1\PermissionController::class, 'index']);
        Route::get('permissions/grouped', [\App\Http\Controllers\Api\V1\PermissionController::class, 'grouped']);

        // Product Categories
        Route::apiResource('product-categories', \App\Http\Controllers\Api\V1\ProductCategoryController::class);
        Route::post('product-categories/{product_category}/restore', [\App\Http\Controllers\Api\V1\ProductCategoryController::class, 'restore'])
            ->withTrashed();

        // Units
        Route::apiResource('units', \App\Http\Controllers\Api\V1\UnitController::class);
        Route::post('units/{unit}/restore', [\App\Http\Controllers\Api\V1\UnitController::class, 'restore'])
            ->withTrashed();

        // Products
        Route::apiResource('products', \App\Http\Controllers\Api\V1\ProductController::class);
        Route::post('products/{product}/restore', [\App\Http\Controllers\Api\V1\ProductController::class, 'restore'])
            ->withTrashed();
        Route::post('products/{product}/photos', [\App\Http\Controllers\Api\V1\ProductController::class, 'uploadPhoto']);
        Route::delete('products/{product}/photos/{photo}', [\App\Http\Controllers\Api\V1\ProductController::class, 'deletePhoto']);

        // Market Categories
        Route::apiResource('market-categories', \App\Http\Controllers\Api\V1\MarketCategoryController::class);
        Route::post('market-categories/{market_category}/restore', [\App\Http\Controllers\Api\V1\MarketCategoryController::class, 'restore'])
            ->withTrashed();

        // Markets
        Route::apiResource('markets', \App\Http\Controllers\Api\V1\MarketController::class);
        Route::post('markets/{market}/restore', [\App\Http\Controllers\Api\V1\MarketController::class, 'restore'])
            ->withTrashed();

        // Market Products (Pricing)
        Route::apiResource('market-products', \App\Http\Controllers\Api\V1\MarketProductController::class);
        Route::post('market-products/{market_product}/restore', [\App\Http\Controllers\Api\V1\MarketProductController::class, 'restore'])
            ->withTrashed();

        // Audit Logs
        Route::get('audit-logs', [\App\Http\Controllers\Api\V1\AuditLogController::class, 'index']);
        Route::get('audit-logs/{audit_log}', [\App\Http\Controllers\Api\V1\AuditLogController::class, 'show']);
    });

    // Public browsing API for customers
    Route::prefix('browse')->group(function () {
        Route::get('products', [\App\Http\Controllers\Api\V1\BrowseController::class, 'products']);
        Route::get('products/{product}', [\App\Http\Controllers\Api\V1\BrowseController::class, 'product']);
        Route::get('markets', [\App\Http\Controllers\Api\V1\BrowseController::class, 'markets']);
        Route::get('markets/{market}', [\App\Http\Controllers\Api\V1\BrowseController::class, 'market']);
        Route::get('markets/{market}/products', [\App\Http\Controllers\Api\V1\BrowseController::class, 'marketProducts']);
        Route::get('categories', [\App\Http\Controllers\Api\V1\BrowseController::class, 'categories']);
    });
});
