<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryPersonnelController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware('auth')->group(function () {
    // Dashboard - Available to all authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('verified')->name('dashboard');
    
    // Profile - Available to all authenticated users
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Products - Admin and Vendor access
    Route::middleware('role:admin,vendor')->group(function () {
        Route::resource('products', ProductController::class);
        Route::post('products/bulk-delete', [ProductController::class, 'bulkDelete'])->name('products.bulk-delete');
        Route::patch('products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
        Route::patch('products/{product}/update-stock', [ProductController::class, 'updateStock'])->name('products.update-stock');
    });
    
    // Categories - Admin only
    Route::middleware('role:admin')->group(function () {
        Route::resource('categories', CategoryController::class)->except(['index', 'show']);
    });
    
    // Category viewing - All authenticated users
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    
    // Orders - All authenticated users can view, create
    Route::resource('orders', OrderController::class);
    
    // Order status update - Admin and Delivery only
    Route::middleware('role:admin,delivery')->group(function () {
        Route::patch('orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    });
    
    // Customers - Admin only
    Route::middleware('role:admin')->group(function () {
        Route::resource('customers', CustomerController::class);
    });
    
    // Vendors - Admin only
    Route::middleware('role:admin')->group(function () {
        Route::resource('vendors', VendorController::class);
    });
    
    // Delivery Personnel - Admin only
    Route::middleware('role:admin')->group(function () {
        Route::resource('delivery-personnel', DeliveryPersonnelController::class);
    });
});

require __DIR__ . '/auth.php';
