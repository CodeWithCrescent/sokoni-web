<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\VendorController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::apiResource('categories', CategoryController::class);

// Route::apiResource('vendors', VendorController::class);

// // Products
// Route::get('/products', [ProductController::class, 'index']);
// Route::get('/products/{product}', [ProductController::class, 'show']);

// // Vendor-specific API routes (protected by policies)
// Route::middleware('can:create,App\Models\Product')->post('/products', [ProductController::class, 'store']);

// Route::middleware('can:update,product')->group(function () {
//     Route::put('/products/{product}', [ProductController::class, 'update']);
//     Route::patch('/products/{product}', [ProductController::class, 'update']);
// });

// Route::middleware('can:delete,product')->delete('/products/{product}', [ProductController::class, 'destroy']);

Route::apiResource('products', ProductController::class);
Route::post('products/bulk-delete', [ProductController::class, 'bulkDelete']);
Route::patch('products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured']);
Route::patch('products/{product}/update-stock', [ProductController::class, 'updateStock']);