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

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('product-categories', function () {
            return Inertia::render('admin/product-categories/index');
        })->name('product-categories.index');

        Route::get('units', function () {
            return Inertia::render('admin/units/index');
        })->name('units.index');

        Route::get('products', function () {
            return Inertia::render('admin/products/index');
        })->name('products.index');

        Route::get('markets', function () {
            return Inertia::render('admin/markets/index');
        })->name('markets.index');

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
