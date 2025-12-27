<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\MarketProductResource;
use App\Http\Resources\V1\MarketResource;
use App\Http\Resources\V1\ProductCategoryResource;
use App\Http\Resources\V1\ProductResource;
use App\Models\Market;
use App\Models\MarketProduct;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class BrowseController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/browse/products",
     *     tags={"Browse"},
     *     summary="Browse all active products",
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="category_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=15)),
     *     @OA\Response(response=200, description="List of products")
     * )
     */
    public function products(Request $request)
    {
        $query = Product::query()
            ->active()
            ->withActiveCategory()
            ->with(['category', 'unit', 'primaryPhoto']);

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->orderBy('name')->paginate($request->integer('per_page', 15));

        return $this->paginatedResponse($products, ProductResource);
    }

    /**
     * @OA\Get(
     *     path="/browse/products/{slug}",
     *     tags={"Browse"},
     *     summary="Get a single product by slug",
     *     @OA\Parameter(name="slug", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Product details")
     * )
     */
    public function product(Product $product)
    {
        if (!$product->is_active) {
            return $this->errorResponse('Product not found', 404);
        }

        return $this->successResponse(
            new ProductResource($product->load(['category', 'unit', 'photos']))
        );
    }

    /**
     * @OA\Get(
     *     path="/browse/markets",
     *     tags={"Browse"},
     *     summary="Browse all active markets",
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="category_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=15)),
     *     @OA\Response(response=200, description="List of markets")
     * )
     */
    public function markets(Request $request)
    {
        $query = Market::query()
            ->active()
            ->withActiveCategory()
            ->with('category')
            ->withCount('products');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $markets = $query->orderBy('name')->paginate($request->integer('per_page', 15));

        return $this->paginatedResponse($markets, MarketResource);
    }

    /**
     * @OA\Get(
     *     path="/browse/markets/{slug}",
     *     tags={"Browse"},
     *     summary="Get a single market by slug",
     *     @OA\Parameter(name="slug", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Market details")
     * )
     */
    public function market(Market $market)
    {
        if (!$market->is_active) {
            return $this->errorResponse('Market not found', 404);
        }

        return $this->successResponse(
            new MarketResource($market->load('category')->loadCount('products'))
        );
    }

    /**
     * @OA\Get(
     *     path="/browse/markets/{slug}/products",
     *     tags={"Browse"},
     *     summary="Get products available in a market",
     *     @OA\Parameter(name="slug", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=15)),
     *     @OA\Response(response=200, description="List of market products")
     * )
     */
    public function marketProducts(Request $request, Market $market)
    {
        if (!$market->is_active) {
            return $this->errorResponse('Market not found', 404);
        }

        $query = MarketProduct::query()
            ->where('market_id', $market->id)
            ->available()
            ->with(['product.unit', 'product.primaryPhoto', 'bulkPrices']);

        if ($request->filled('search')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            });
        }

        $products = $query->paginate($request->integer('per_page', 15));

        return $this->paginatedResponse($products, MarketProductResource);
    }

    /**
     * @OA\Get(
     *     path="/browse/categories",
     *     tags={"Browse"},
     *     summary="Get all active product categories",
     *     @OA\Response(response=200, description="List of categories")
     * )
     */
    public function categories()
    {
        $categories = ProductCategory::active()
            ->ordered()
            ->withCount('products')
            ->get();

        return $this->successResponse(ProductCategoryResource::collection($categories));
    }
}
