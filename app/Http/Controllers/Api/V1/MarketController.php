<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\StoreMarketRequest;
use App\Http\Requests\Api\V1\UpdateMarketRequest;
use App\Http\Resources\V1\MarketResource;
use App\Models\Market;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarketController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/markets",
     *     tags={"Markets"},
     *     summary="List all markets",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="category_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="is_active", in="query", @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="with_trashed", in="query", @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=15)),
     *     @OA\Response(response=200, description="List of markets")
     * )
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Market::class);

        $query = Market::query()->with('category')->withCount('products');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->boolean('with_trashed') && $request->user()->hasPermission('markets.restore')) {
            $query->withTrashed();
        }

        $markets = $query->orderBy('name')->paginate($request->integer('per_page', 15));

        return $this->paginatedResponse($markets, MarketResource);
    }

    /**
     * @OA\Post(
     *     path="/markets",
     *     tags={"Markets"},
     *     summary="Create a new market",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"category_id", "name"},
     *         @OA\Property(property="category_id", type="integer"),
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="slug", type="string"),
     *         @OA\Property(property="description", type="string"),
     *         @OA\Property(property="address", type="string"),
     *         @OA\Property(property="latitude", type="number"),
     *         @OA\Property(property="longitude", type="number"),
     *         @OA\Property(property="min_order_amount", type="number"),
     *         @OA\Property(property="phone", type="string"),
     *         @OA\Property(property="email", type="string"),
     *         @OA\Property(property="operating_hours", type="object"),
     *         @OA\Property(property="is_active", type="boolean")
     *     )),
     *     @OA\Response(response=201, description="Market created")
     * )
     */
    public function store(StoreMarketRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('markets', 'public');
        }

        if ($request->hasFile('cover_photo')) {
            $data['cover_photo'] = $request->file('cover_photo')->store('markets/covers', 'public');
        }

        $market = Market::create($data);

        return $this->successResponse(
            new MarketResource($market->load('category')),
            'Market created successfully',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/markets/{id}",
     *     tags={"Markets"},
     *     summary="Get a market",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Market details")
     * )
     */
    public function show(Market $market)
    {
        $this->authorize('view', $market);

        return $this->successResponse(
            new MarketResource($market->load('category')->loadCount('products'))
        );
    }

    /**
     * @OA\Put(
     *     path="/markets/{id}",
     *     tags={"Markets"},
     *     summary="Update a market",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="category_id", type="integer"),
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="slug", type="string"),
     *         @OA\Property(property="description", type="string"),
     *         @OA\Property(property="address", type="string"),
     *         @OA\Property(property="latitude", type="number"),
     *         @OA\Property(property="longitude", type="number"),
     *         @OA\Property(property="min_order_amount", type="number"),
     *         @OA\Property(property="phone", type="string"),
     *         @OA\Property(property="email", type="string"),
     *         @OA\Property(property="operating_hours", type="object"),
     *         @OA\Property(property="is_active", type="boolean")
     *     )),
     *     @OA\Response(response=200, description="Market updated")
     * )
     */
    public function update(UpdateMarketRequest $request, Market $market)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($market->photo) {
                Storage::disk('public')->delete($market->photo);
            }
            $data['photo'] = $request->file('photo')->store('markets', 'public');
        }

        if ($request->hasFile('cover_photo')) {
            if ($market->cover_photo) {
                Storage::disk('public')->delete($market->cover_photo);
            }
            $data['cover_photo'] = $request->file('cover_photo')->store('markets/covers', 'public');
        }

        $market->update($data);

        return $this->successResponse(
            new MarketResource($market->load('category')),
            'Market updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *     path="/markets/{id}",
     *     tags={"Markets"},
     *     summary="Delete a market",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Market deleted")
     * )
     */
    public function destroy(Market $market)
    {
        $this->authorize('delete', $market);

        $market->delete();

        return $this->successResponse(null, 'Market deleted successfully');
    }

    /**
     * @OA\Post(
     *     path="/markets/{id}/restore",
     *     tags={"Markets"},
     *     summary="Restore a deleted market",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Market restored")
     * )
     */
    public function restore(Market $market)
    {
        $this->authorize('restore', $market);

        $market->restore();

        return $this->successResponse(
            new MarketResource($market->load('category')),
            'Market restored successfully'
        );
    }
}
