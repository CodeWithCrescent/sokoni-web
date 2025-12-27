<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

// Public Storefront Routes
Route::get('/', function () {
    return Inertia::render('storefront/home');
})->name('home');

Route::get('/shop', function () {
    return Inertia::render('storefront/shop');
})->name('shop');

Route::get('/cart', function () {
    return Inertia::render('storefront/cart');
})->name('cart');

Route::get('/markets', function () {
    return Inertia::render('storefront/markets');
})->name('storefront.markets');

Route::get('/welcome', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    // Customer Portal Routes
    Route::get('my-orders', function () {
        return Inertia::render('customer/orders/index');
    })->name('customer.orders');

    Route::get('my-orders/{orderId}', function ($orderId) {
        return Inertia::render('customer/orders/show', ['orderId' => (int) $orderId]);
    })->name('customer.orders.show');

    Route::get('checkout', function () {
        return Inertia::render('customer/checkout');
    })->name('checkout');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('product-categories', function () {
            return Inertia::render('admin/product-categories/index');
        })->name('product-categories.index');

        Route::get('product-categories/create', function () {
            return Inertia::render('admin/product-categories/form');
        })->name('product-categories.create');

        Route::get('product-categories/{categoryId}/edit', function ($categoryId) {
            return Inertia::render('admin/product-categories/form', ['categoryId' => (int) $categoryId]);
        })->name('product-categories.edit');

        Route::get('units', function () {
            return Inertia::render('admin/units/index');
        })->name('units.index');

        Route::get('units/create', function () {
            return Inertia::render('admin/units/form');
        })->name('units.create');

        Route::get('units/{unitId}/edit', function ($unitId) {
            return Inertia::render('admin/units/form', ['unitId' => (int) $unitId]);
        })->name('units.edit');

        Route::get('products', function () {
            return Inertia::render('admin/products/index');
        })->name('products.index');

        Route::get('products/create', function () {
            return Inertia::render('admin/products/form');
        })->name('products.create');

        Route::get('products/{productId}/edit', function ($productId) {
            return Inertia::render('admin/products/form', ['productId' => (int) $productId]);
        })->name('products.edit');

        Route::get('markets', function () {
            return Inertia::render('admin/markets/index');
        })->name('markets.index');

        Route::get('markets/create', function () {
            return Inertia::render('admin/markets/form');
        })->name('markets.create');

        Route::get('markets/{marketId}', [App\Http\Controllers\Admin\MarketController::class, 'show'])->name('markets.show');

        Route::get('markets/{marketId}/edit', function ($marketId) {
            return Inertia::render('admin/markets/form', ['marketId' => (int) $marketId]);
        })->name('markets.edit');

        Route::get('orders', function () {
            return Inertia::render('admin/orders/index');
        })->name('orders.index');

        Route::get('users', function () {
            return Inertia::render('admin/users/index');
        })->name('users.index');

        Route::get('users/create', function () {
            return Inertia::render('admin/users/form');
        })->name('users.create');

        Route::get('users/{userId}/edit', function ($userId) {
            return Inertia::render('admin/users/form', ['userId' => (int) $userId]);
        })->name('users.edit');

        Route::get('roles', function () {
            return Inertia::render('admin/roles/index');
        })->name('roles.index');
    });
});

require __DIR__.'/settings.php';
