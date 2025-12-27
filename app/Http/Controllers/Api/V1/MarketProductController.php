<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\StoreMarketProductRequest;
use App\Http\Requests\Api\V1\UpdateMarketProductRequest;
use App\Http\Resources\V1\MarketProductResource;
use App\Models\MarketProduct;
use Illuminate\Http\Request;

class MarketProductController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/market-products",
     *     tags={"Market Products"},
     *     summary="List all market products",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="market_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="product_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="is_available", in="query", @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="with_trashed", in="query", @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=15)),
     *     @OA\Response(response=200, description="List of market products")
     * )
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', MarketProduct::class);

        $query = MarketProduct::query()->with(['market', 'product.unit', 'bulkPrices']);

        if ($request->filled('market_id')) {
            $query->where('market_id', $request->market_id);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('is_available')) {
            $query->where('is_available', $request->boolean('is_available'));
        }

        if ($request->boolean('with_trashed') && $request->user()->hasPermission('market-products.restore')) {
            $query->withTrashed();
        }

        $marketProducts = $query->paginate($request->integer('per_page', 15));

        return $this->paginatedResponse($marketProducts, MarketProductResource::class);
    }

    /**
     * @OA\Post(
     *     path="/market-products",
     *     tags={"Market Products"},
     *     summary="Create a new market product",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"market_id", "product_id", "price"},
     *         @OA\Property(property="market_id", type="integer"),
     *         @OA\Property(property="product_id", type="integer"),
     *         @OA\Property(property="price", type="number"),
     *         @OA\Property(property="stock", type="integer"),
     *         @OA\Property(property="moq", type="integer"),
     *         @OA\Property(property="is_available", type="boolean"),
     *         @OA\Property(property="bulk_prices", type="array", @OA\Items(
     *             @OA\Property(property="min_qty", type="integer"),
     *             @OA\Property(property="max_qty", type="integer"),
     *             @OA\Property(property="price", type="number")
     *         ))
     *     )),
     *     @OA\Response(response=201, description="Market product created")
     * )
     */
    public function store(StoreMarketProductRequest $request)
    {
        $data = $request->validated();
        $bulkPrices = $data['bulk_prices'] ?? [];
        unset($data['bulk_prices']);

        $marketProduct = MarketProduct::create($data);

        foreach ($bulkPrices as $bulkPrice) {
            $marketProduct->bulkPrices()->create($bulkPrice);
        }

        return $this->successResponse(
            new MarketProductResource($marketProduct->load(['market', 'product.unit', 'bulkPrices'])),
            'Market product created successfully',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/market-products/{id}",
     *     tags={"Market Products"},
     *     summary="Get a market product",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Market product details")
     * )
     */
    public function show(MarketProduct $marketProduct)
    {
        $this->authorize('view', $marketProduct);

        return $this->successResponse(
            new MarketProductResource($marketProduct->load(['market', 'product.unit', 'bulkPrices']))
        );
    }

    /**
     * @OA\Put(
     *     path="/market-products/{id}",
     *     tags={"Market Products"},
     *     summary="Update a market product",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="price", type="number"),
     *         @OA\Property(property="stock", type="integer"),
     *         @OA\Property(property="moq", type="integer"),
     *         @OA\Property(property="is_available", type="boolean"),
     *         @OA\Property(property="bulk_prices", type="array", @OA\Items(
     *             @OA\Property(property="min_qty", type="integer"),
     *             @OA\Property(property="max_qty", type="integer"),
     *             @OA\Property(property="price", type="number")
     *         ))
     *     )),
     *     @OA\Response(response=200, description="Market product updated")
     * )
     */
    public function update(UpdateMarketProductRequest $request, MarketProduct $marketProduct)
    {
        $data = $request->validated();
        $bulkPrices = $data['bulk_prices'] ?? null;
        unset($data['bulk_prices']);

        $marketProduct->update($data);

        if ($bulkPrices !== null) {
            $marketProduct->bulkPrices()->delete();
            foreach ($bulkPrices as $bulkPrice) {
                $marketProduct->bulkPrices()->create($bulkPrice);
            }
        }

        return $this->successResponse(
            new MarketProductResource($marketProduct->load(['market', 'product.unit', 'bulkPrices'])),
            'Market product updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *     path="/market-products/{id}",
     *     tags={"Market Products"},
     *     summary="Delete a market product",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Market product deleted")
     * )
     */
    public function destroy(MarketProduct $marketProduct)
    {
        $this->authorize('delete', $marketProduct);

        $marketProduct->delete();

        return $this->successResponse(null, 'Market product deleted successfully');
    }

    /**
     * @OA\Post(
     *     path="/market-products/{id}/restore",
     *     tags={"Market Products"},
     *     summary="Restore a deleted market product",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Market product restored")
     * )
     */
    public function restore(MarketProduct $marketProduct)
    {
        $this->authorize('restore', $marketProduct);

        $marketProduct->restore();

        return $this->successResponse(
            new MarketProductResource($marketProduct->load(['market', 'product.unit', 'bulkPrices'])),
            'Market product restored successfully'
        );
    }
}
