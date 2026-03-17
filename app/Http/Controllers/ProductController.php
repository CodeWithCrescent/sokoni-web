<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Category;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'vendor.user']);
        
        // If user is a vendor, only show their products
        if (Auth::user()->role->name === 'vendor') {
            $query->where('vendor_id', Auth::user()->vendor->id);
        }
        
        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        // Filter by vendor (admin only)
        if ($request->filled('vendor') && Auth::user()->role->name === 'admin') {
            $query->where('vendor_id', $request->vendor);
        }
        
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        
        // Search by name or description
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        
        // Sorting
        if ($request->filled('sort_by')) {
            $sortDirection = $request->get('sort_dir', 'asc');
            $query->orderBy($request->sort_by, $sortDirection);
        } else {
            $query->latest();
        }
        
        $products = $query->paginate($request->get('per_page', 15));
        
        // Check if request expects JSON (API route)
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'data' => $products
            ]);
        }
        
        // For web interface
        $categories = Category::all();
        $vendors = Vendor::all();
        
        return view('products.index', compact('products', 'categories', 'vendors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        $categories = Category::all();
        $vendors = Vendor::all();
        
        return view('products.create', compact('categories', 'vendors'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProductRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();
        
        // If user is vendor, automatically set vendor_id
        if (Auth::user()->role->name === 'vendor') {
            $validated['vendor_id'] = Auth::user()->vendor->id;
        }
        
        $product = Product::create($validated);
        
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'message' => 'Product created successfully',
                'data' => $product->load(['vendor', 'category'])
            ], Response::HTTP_CREATED);
        }
        
        return redirect()->route('products.index')
            ->with('success', 'Product created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function show(Request $request, Product $product)
    {
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'data' => $product->load(['vendor', 'category'])
            ]);
        }
        
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Product $product
     * @return \Illuminate\View\View
     */
    public function edit(Product $product): View
    {
        // Check vendor ownership
        if (Auth::user()->role->name === 'vendor' && $product->vendor_id !== Auth::user()->vendor->id) {
            abort(403, 'You do not have permission to edit this product.');
        }
        
        $categories = Category::all();
        $vendors = Vendor::all();
        
        return view('products.edit', compact('product', 'categories', 'vendors'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProductRequest $request
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        // Check vendor ownership
        if (Auth::user()->role->name === 'vendor' && $product->vendor_id !== Auth::user()->vendor->id) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have permission to update this product.'
                ], Response::HTTP_FORBIDDEN);
            }
            return redirect()->route('products.index')
                ->with('error', 'You do not have permission to update this product.');
        }
        
        $validated = $request->validated();
        $product->update($validated);
        
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'message' => 'Product updated successfully',
                'data' => $product->fresh(['vendor', 'category'])
            ]);
        }
        
        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, Product $product)
    {
        // Check vendor ownership
        if (Auth::user()->role->name === 'vendor' && $product->vendor_id !== Auth::user()->vendor->id) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have permission to delete this product.'
                ], Response::HTTP_FORBIDDEN);
            }
            
            return back()->with('error', 'Cannot delete product with existing orders');
        }
        
        $product->delete();
        
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'message' => 'Product deleted successfully'
            ]);
        }
        
        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully');
    }

    /**
     * Bulk delete multiple products.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id'
        ]);

        $productIds = $request->ids;
        
        // Check if any products have order details
        $productsWithOrders = Product::whereIn('id', $productIds)
            ->whereHas('orderDetails')
            ->pluck('name')
            ->toArray();
            
        if (count($productsWithOrders) > 0) {
            $errorMessage = 'Cannot delete products: ' . implode(', ', $productsWithOrders) . ' due to existing orders';
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => $errorMessage
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            return back()->with('error', $errorMessage);
        }
        
        // Delete products without orders
        Product::whereIn('id', $productIds)->delete();
        
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'message' => count($productIds) . ' products deleted successfully'
            ]);
        }
        
        return redirect()->route('products.index')
            ->with('success', count($productIds) . ' products deleted successfully');
    }

    /**
     * Toggle featured status for a product.
     *
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function toggleFeatured(Request $request, Product $product)
    {
        $product->is_featured = !$product->is_featured;
        $product->save();
        
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'message' => 'Product featured status updated',
                'data' => [
                    'is_featured' => $product->is_featured
                ]
            ]);
        }
        
        $status = $product->is_featured ? 'featured' : 'unfeatured';
        return back()->with('success', "Product {$status} successfully");
    }

    /**
     * Update product stock quantity.
     *
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'stock' => 'required|integer|min:0'
        ]);
        
        $product->stock = $request->stock;
        $product->save();
        
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'message' => 'Stock updated successfully',
                'data' => [
                    'stock' => $product->stock
                ]
            ]);
        }
        
        return back()->with('success', 'Stock updated successfully');
    }
}