<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\StoreProductRequest;
use App\Http\Requests\Api\V1\UpdateProductRequest;
use App\Http\Resources\V1\ProductResource;
use App\Models\Product;
use App\Models\ProductPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/products",
     *     tags={"Products"},
     *     summary="List all products",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="category_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="is_active", in="query", @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="with_trashed", in="query", @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=15)),
     *     @OA\Response(response=200, description="List of products")
     * )
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Product::class);

        $query = Product::query()
            ->with(['category', 'unit', 'primaryPhoto'])
            ->withCount('markets');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->boolean('with_trashed') && $request->user()->hasPermission('products.restore')) {
            $query->withTrashed();
        }

        $products = $query->orderBy('name')->paginate($request->integer('per_page', 15));

        return $this->paginatedResponse($products, ProductResource::class);
    }

    /**
     * @OA\Post(
     *     path="/products",
     *     tags={"Products"},
     *     summary="Create a new product",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"category_id", "unit_id", "name"},
     *         @OA\Property(property="category_id", type="integer"),
     *         @OA\Property(property="unit_id", type="integer"),
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="slug", type="string"),
     *         @OA\Property(property="description", type="string"),
     *         @OA\Property(property="is_active", type="boolean")
     *     )),
     *     @OA\Response(response=201, description="Product created")
     * )
     */
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        return $this->successResponse(
            new ProductResource($product->load(['category', 'unit'])),
            'Product created successfully',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/products/{id}",
     *     tags={"Products"},
     *     summary="Get a product",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Product details")
     * )
     */
    public function show(Product $product)
    {
        $this->authorize('view', $product);

        return $this->successResponse(
            new ProductResource($product->load(['category', 'unit', 'photos']))
        );
    }

    /**
     * @OA\Put(
     *     path="/products/{id}",
     *     tags={"Products"},
     *     summary="Update a product",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="category_id", type="integer"),
     *         @OA\Property(property="unit_id", type="integer"),
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="slug", type="string"),
     *         @OA\Property(property="description", type="string"),
     *         @OA\Property(property="is_active", type="boolean")
     *     )),
     *     @OA\Response(response=200, description="Product updated")
     * )
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return $this->successResponse(
            new ProductResource($product->load(['category', 'unit', 'photos'])),
            'Product updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *     path="/products/{id}",
     *     tags={"Products"},
     *     summary="Delete a product",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Product deleted")
     * )
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        $product->delete();

        return $this->successResponse(null, 'Product deleted successfully');
    }

    /**
     * @OA\Post(
     *     path="/products/{id}/restore",
     *     tags={"Products"},
     *     summary="Restore a deleted product",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Product restored")
     * )
     */
    public function restore(Product $product)
    {
        $this->authorize('restore', $product);

        $product->restore();

        return $this->successResponse(
            new ProductResource($product->load(['category', 'unit', 'photos'])),
            'Product restored successfully'
        );
    }

    /**
     * @OA\Post(
     *     path="/products/{id}/photos",
     *     tags={"Products"},
     *     summary="Upload a product photo",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *             @OA\Property(property="photo", type="string", format="binary"),
     *             @OA\Property(property="alt_text", type="string"),
     *             @OA\Property(property="is_primary", type="boolean")
     *         )
     *     )),
     *     @OA\Response(response=201, description="Photo uploaded")
     * )
     */
    public function uploadPhoto(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $request->validate([
            'photo' => ['required', 'image', 'max:2048'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        if ($product->photos()->count() >= 10) {
            return $this->errorResponse('Maximum 10 photos allowed per product', 422);
        }

        $path = $request->file('photo')->store('products', 'public');

        if ($request->boolean('is_primary')) {
            $product->photos()->update(['is_primary' => false]);
        }

        $photo = $product->photos()->create([
            'photo_path' => $path,
            'alt_text' => $request->alt_text,
            'is_primary' => $request->boolean('is_primary'),
            'sort_order' => $product->photos()->count(),
        ]);

        return $this->successResponse($photo, 'Photo uploaded successfully', 201);
    }

    /**
     * @OA\Delete(
     *     path="/products/{id}/photos/{photoId}",
     *     tags={"Products"},
     *     summary="Delete a product photo",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="photoId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Photo deleted")
     * )
     */
    public function deletePhoto(Product $product, ProductPhoto $photo)
    {
        $this->authorize('update', $product);

        if ($photo->product_id !== $product->id) {
            return $this->errorResponse('Photo does not belong to this product', 404);
        }

        Storage::disk('public')->delete($photo->photo_path);
        $photo->forceDelete();

        return $this->successResponse(null, 'Photo deleted successfully');
    }
}
