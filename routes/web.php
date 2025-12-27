<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

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
    });
});

require __DIR__.'/settings.php';
