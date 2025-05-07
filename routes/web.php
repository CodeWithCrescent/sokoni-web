<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Common routes
// Route::get('/products', [PageController::class, 'productsView'])->name('products.index');
// Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// // Vendor-specific routes
// Route::middleware('can:create,App\Models\Product')->group(function () {
//     Route::post('/products', [ProductController::class, 'store'])->name('products.create');
// });

// Route::middleware('can:update,product')->group(function () {
//     Route::put('/products/{product}', [ProductController::class, 'update']);
//     Route::patch('/products/{product}', [ProductController::class, 'update']);
// });

// Route::middleware('can:delete,product')->group(function () {
//     Route::delete('/products/{product}', [ProductController::class, 'destroy']);
// });

Route::resource('products', ProductController::class);
Route::post('products/bulk-delete', [ProductController::class, 'bulkDelete'])->name('products.bulk-delete');
Route::patch('products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
Route::patch('products/{product}/update-stock', [ProductController::class, 'updateStock'])->name('products.update-stock');

require __DIR__ . '/auth.php';
