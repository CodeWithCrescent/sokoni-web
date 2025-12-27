<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\StoreMarketCategoryRequest;
use App\Http\Requests\Api\V1\UpdateMarketCategoryRequest;
use App\Http\Resources\V1\MarketCategoryResource;
use App\Models\MarketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarketCategoryController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/market-categories",
     *     tags={"Market Categories"},
     *     summary="List all market categories",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="is_active", in="query", @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="with_trashed", in="query", @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=15)),
     *     @OA\Response(response=200, description="List of categories")
     * )
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', MarketCategory::class);

        $query = MarketCategory::query()->withCount('markets');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->boolean('with_trashed') && $request->user()->hasPermission('market-categories.restore')) {
            $query->withTrashed();
        }

        $categories = $query->ordered()->paginate($request->integer('per_page', 15));

        return $this->paginatedResponse($categories, MarketCategoryResource);
    }

    /**
     * @OA\Post(
     *     path="/market-categories",
     *     tags={"Market Categories"},
     *     summary="Create a new market category",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"name"},
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="slug", type="string"),
     *         @OA\Property(property="description", type="string"),
     *         @OA\Property(property="sort_order", type="integer"),
     *         @OA\Property(property="is_active", type="boolean")
     *     )),
     *     @OA\Response(response=201, description="Category created")
     * )
     */
    public function store(StoreMarketCategoryRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('market-categories', 'public');
        }

        $category = MarketCategory::create($data);

        return $this->successResponse(
            new MarketCategoryResource($category),
            'Market category created successfully',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/market-categories/{id}",
     *     tags={"Market Categories"},
     *     summary="Get a market category",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Category details")
     * )
     */
    public function show(MarketCategory $marketCategory)
    {
        $this->authorize('view', $marketCategory);

        return $this->successResponse(
            new MarketCategoryResource($marketCategory->loadCount('markets'))
        );
    }

    /**
     * @OA\Put(
     *     path="/market-categories/{id}",
     *     tags={"Market Categories"},
     *     summary="Update a market category",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="slug", type="string"),
     *         @OA\Property(property="description", type="string"),
     *         @OA\Property(property="sort_order", type="integer"),
     *         @OA\Property(property="is_active", type="boolean")
     *     )),
     *     @OA\Response(response=200, description="Category updated")
     * )
     */
    public function update(UpdateMarketCategoryRequest $request, MarketCategory $marketCategory)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($marketCategory->image) {
                Storage::disk('public')->delete($marketCategory->image);
            }
            $data['image'] = $request->file('image')->store('market-categories', 'public');
        }

        $marketCategory->update($data);

        return $this->successResponse(
            new MarketCategoryResource($marketCategory),
            'Market category updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *     path="/market-categories/{id}",
     *     tags={"Market Categories"},
     *     summary="Delete a market category",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Category deleted")
     * )
     */
    public function destroy(MarketCategory $marketCategory)
    {
        $this->authorize('delete', $marketCategory);

        $marketCategory->delete();

        return $this->successResponse(null, 'Market category deleted successfully');
    }

    /**
     * @OA\Post(
     *     path="/market-categories/{id}/restore",
     *     tags={"Market Categories"},
     *     summary="Restore a deleted market category",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Category restored")
     * )
     */
    public function restore(MarketCategory $marketCategory)
    {
        $this->authorize('restore', $marketCategory);

        $marketCategory->restore();

        return $this->successResponse(
            new MarketCategoryResource($marketCategory),
            'Market category restored successfully'
        );
    }
}
