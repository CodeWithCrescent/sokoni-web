<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\StoreProductCategoryRequest;
use App\Http\Requests\Api\V1\UpdateProductCategoryRequest;
use App\Http\Resources\V1\ProductCategoryResource;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductCategoryController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/product-categories",
     *     tags={"Product Categories"},
     *     summary="List all product categories",
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
        $this->authorize('viewAny', ProductCategory::class);

        $query = ProductCategory::query()->withCount('products');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->boolean('with_trashed') && $request->user()->hasPermission('product-categories.restore')) {
            $query->withTrashed();
        }

        $categories = $query->ordered()->paginate($request->integer('per_page', 15));

        return $this->paginatedResponse($categories, ProductCategoryResource);
    }

    /**
     * @OA\Post(
     *     path="/product-categories",
     *     tags={"Product Categories"},
     *     summary="Create a new product category",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="sort_order", type="integer"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Category created")
     * )
     */
    public function store(StoreProductCategoryRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('product-categories', 'public');
        }

        $category = ProductCategory::create($data);

        return $this->successResponse(
            new ProductCategoryResource($category),
            'Product category created successfully',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/product-categories/{id}",
     *     tags={"Product Categories"},
     *     summary="Get a product category",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Category details")
     * )
     */
    public function show(ProductCategory $productCategory)
    {
        $this->authorize('view', $productCategory);

        return $this->successResponse(
            new ProductCategoryResource($productCategory->loadCount('products'))
        );
    }

    /**
     * @OA\Put(
     *     path="/product-categories/{id}",
     *     tags={"Product Categories"},
     *     summary="Update a product category",
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
    public function update(UpdateProductCategoryRequest $request, ProductCategory $productCategory)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($productCategory->image) {
                Storage::disk('public')->delete($productCategory->image);
            }
            $data['image'] = $request->file('image')->store('product-categories', 'public');
        }

        $productCategory->update($data);

        return $this->successResponse(
            new ProductCategoryResource($productCategory),
            'Product category updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *     path="/product-categories/{id}",
     *     tags={"Product Categories"},
     *     summary="Delete a product category",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Category deleted")
     * )
     */
    public function destroy(ProductCategory $productCategory)
    {
        $this->authorize('delete', $productCategory);

        $productCategory->delete();

        return $this->successResponse(null, 'Product category deleted successfully');
    }

    /**
     * @OA\Post(
     *     path="/product-categories/{id}/restore",
     *     tags={"Product Categories"},
     *     summary="Restore a deleted product category",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Category restored")
     * )
     */
    public function restore(ProductCategory $productCategory)
    {
        $this->authorize('restore', $productCategory);

        $productCategory->restore();

        return $this->successResponse(
            new ProductCategoryResource($productCategory),
            'Product category restored successfully'
        );
    }
}
