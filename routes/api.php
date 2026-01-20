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

        // Users
        Route::apiResource('users', \App\Http\Controllers\Api\V1\UserController::class);
        Route::post('users/{user:slug}/restore', [\App\Http\Controllers\Api\V1\UserController::class, 'restore']);

        // Roles
        Route::apiResource('roles', \App\Http\Controllers\Api\V1\RoleController::class);
        Route::post('roles/{role:slug}/restore', [\App\Http\Controllers\Api\V1\RoleController::class, 'restore'])
            ->withTrashed();

        // Permissions
        Route::get('permissions', [\App\Http\Controllers\Api\V1\PermissionController::class, 'index']);
        Route::get('permissions/grouped', [\App\Http\Controllers\Api\V1\PermissionController::class, 'grouped']);

        // Product Categories
        Route::apiResource('product-categories', \App\Http\Controllers\Api\V1\ProductCategoryController::class);
        Route::post('product-categories/{product_category:slug}/restore', [\App\Http\Controllers\Api\V1\ProductCategoryController::class, 'restore'])
            ->withTrashed();

        // Units
        Route::apiResource('units', \App\Http\Controllers\Api\V1\UnitController::class);
        Route::post('units/{unit:slug}/restore', [\App\Http\Controllers\Api\V1\UnitController::class, 'restore'])
            ->withTrashed();

        // Products
        Route::apiResource('products', \App\Http\Controllers\Api\V1\ProductController::class);
        Route::post('products/{product:slug}/restore', [\App\Http\Controllers\Api\V1\ProductController::class, 'restore'])
            ->withTrashed();
        Route::post('products/{product:slug}/photos', [\App\Http\Controllers\Api\V1\ProductController::class, 'uploadPhoto']);
        Route::delete('products/{product:slug}/photos/{photo}', [\App\Http\Controllers\Api\V1\ProductController::class, 'deletePhoto']);

        // Upload
        Route::post('upload/image', [\App\Http\Controllers\Api\V1\UploadController::class, 'uploadImage']);
        Route::post('upload/file', [\App\Http\Controllers\Api\V1\UploadController::class, 'uploadFile']);

        // Market Categories
        Route::apiResource('market-categories', \App\Http\Controllers\Api\V1\MarketCategoryController::class);
        Route::post('market-categories/{market_category:slug}/restore', [\App\Http\Controllers\Api\V1\MarketCategoryController::class, 'restore'])
            ->withTrashed();

        // Markets
        Route::apiResource('markets', \App\Http\Controllers\Api\V1\MarketController::class);
        Route::post('markets/{market:slug}/restore', [\App\Http\Controllers\Api\V1\MarketController::class, 'restore'])
            ->withTrashed();

        // Market Products (Pricing)
        Route::apiResource('market-products', \App\Http\Controllers\Api\V1\MarketProductController::class);
        Route::post('market-products/{market_product}/restore', [\App\Http\Controllers\Api\V1\MarketProductController::class, 'restore'])
            ->withTrashed();

        // Audit Logs
        Route::get('audit-logs', [\App\Http\Controllers\Api\V1\AuditLogController::class, 'index']);
        Route::get('audit-logs/{audit_log}', [\App\Http\Controllers\Api\V1\AuditLogController::class, 'show']);

        // Orders
        Route::get('orders/my-orders', [\App\Http\Controllers\Api\V1\OrderController::class, 'myOrders']);
        Route::apiResource('orders', \App\Http\Controllers\Api\V1\OrderController::class)->only(['index', 'store', 'show']);
        Route::put('orders/{order:order_number}/status', [\App\Http\Controllers\Api\V1\OrderController::class, 'updateStatus']);
        Route::post('orders/{order:order_number}/assign-collector', [\App\Http\Controllers\Api\V1\OrderController::class, 'assignCollector']);
        Route::post('orders/{order:order_number}/assign-driver', [\App\Http\Controllers\Api\V1\OrderController::class, 'assignDriver']);

        // Payments
        Route::post('payments/initiate', [\App\Http\Controllers\Api\V1\PaymentController::class, 'initiate']);
        Route::get('payments/{payment}', [\App\Http\Controllers\Api\V1\PaymentController::class, 'show']);
        Route::post('payments/{payment}/confirm', [\App\Http\Controllers\Api\V1\PaymentController::class, 'confirmCashPayment']);
        Route::get('payments/order/{orderNumber}', [\App\Http\Controllers\Api\V1\PaymentController::class, 'forOrder']);

        // Cart
        Route::get('cart', [\App\Http\Controllers\Api\V1\CartController::class, 'index']);
        Route::post('cart/add', [\App\Http\Controllers\Api\V1\CartController::class, 'addItem']);
        Route::put('cart/items/{itemId}', [\App\Http\Controllers\Api\V1\CartController::class, 'updateItem']);
        Route::delete('cart/items/{itemId}', [\App\Http\Controllers\Api\V1\CartController::class, 'removeItem']);
        Route::delete('cart/{cartId}', [\App\Http\Controllers\Api\V1\CartController::class, 'clear']);

        // Addresses
        Route::get('addresses', [\App\Http\Controllers\Api\V1\AddressController::class, 'index']);
        Route::post('addresses', [\App\Http\Controllers\Api\V1\AddressController::class, 'store']);
        Route::put('addresses/{id}', [\App\Http\Controllers\Api\V1\AddressController::class, 'update']);
        Route::delete('addresses/{id}', [\App\Http\Controllers\Api\V1\AddressController::class, 'destroy']);
        Route::post('addresses/{id}/default', [\App\Http\Controllers\Api\V1\AddressController::class, 'setDefault']);

        // Notifications
        Route::get('notifications', [\App\Http\Controllers\Api\V1\NotificationController::class, 'index']);
        Route::get('notifications/unread-count', [\App\Http\Controllers\Api\V1\NotificationController::class, 'unreadCount']);
        Route::post('notifications/{id}/read', [\App\Http\Controllers\Api\V1\NotificationController::class, 'markAsRead']);
        Route::post('notifications/read-all', [\App\Http\Controllers\Api\V1\NotificationController::class, 'markAllAsRead']);
        Route::delete('notifications/{id}', [\App\Http\Controllers\Api\V1\NotificationController::class, 'destroy']);

        // Analytics (Admin)
        Route::prefix('analytics')->group(function () {
            Route::get('dashboard', [\App\Http\Controllers\Api\V1\AnalyticsController::class, 'dashboard']);
            Route::get('revenue', [\App\Http\Controllers\Api\V1\AnalyticsController::class, 'revenue']);
            Route::get('top-products', [\App\Http\Controllers\Api\V1\AnalyticsController::class, 'topProducts']);
            Route::get('top-markets', [\App\Http\Controllers\Api\V1\AnalyticsController::class, 'topMarkets']);
            Route::get('orders-trend', [\App\Http\Controllers\Api\V1\AnalyticsController::class, 'ordersTrend']);

            // Staff Analytics
            Route::get('staff/collectors', [\App\Http\Controllers\Api\V1\StaffAnalyticsController::class, 'collectors']);
            Route::get('staff/drivers', [\App\Http\Controllers\Api\V1\StaffAnalyticsController::class, 'drivers']);
            Route::get('staff/leaderboard', [\App\Http\Controllers\Api\V1\StaffAnalyticsController::class, 'leaderboard']);
            Route::get('staff/{userId}/orders', [\App\Http\Controllers\Api\V1\StaffAnalyticsController::class, 'staffOrders']);
        });
    });

    // Payment callback (public)
    Route::post('payments/callback', [\App\Http\Controllers\Api\V1\PaymentController::class, 'callback']);

        // Public browsing API for customers
        Route::prefix('browse')->group(function () {
        Route::get('products', [\App\Http\Controllers\Api\V1\BrowseController::class, 'products']);
        Route::get('products/{product:slug}', [\App\Http\Controllers\Api\V1\BrowseController::class, 'product']);
        Route::get('markets', [\App\Http\Controllers\Api\V1\BrowseController::class, 'markets']);
        Route::get('markets/{market:slug}', [\App\Http\Controllers\Api\V1\BrowseController::class, 'market']);
        Route::get('markets/{market:slug}/products', [\App\Http\Controllers\Api\V1\BrowseController::class, 'marketProducts']);
        Route::get('categories', [\App\Http\Controllers\Api\V1\BrowseController::class, 'categories']);
    });
});
