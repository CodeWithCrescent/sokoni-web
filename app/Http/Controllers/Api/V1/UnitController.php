<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\StoreUnitRequest;
use App\Http\Requests\Api\V1\UpdateUnitRequest;
use App\Http\Resources\V1\UnitResource;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/units",
     *     tags={"Units"},
     *     summary="List all units",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="is_active", in="query", @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="with_trashed", in="query", @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=15)),
     *     @OA\Response(response=200, description="List of units")
     * )
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Unit::class);

        $query = Unit::query()->withCount('products');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('abbreviation', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->boolean('with_trashed') && $request->user()->hasPermission('units.restore')) {
            $query->withTrashed();
        }

        $units = $query->orderBy('name')->paginate($request->integer('per_page', 15));

        return $this->paginatedResponse($units, UnitResource::class);
    }

    /**
     * @OA\Post(
     *     path="/units",
     *     tags={"Units"},
     *     summary="Create a new unit",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"name", "abbreviation"},
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="abbreviation", type="string"),
     *         @OA\Property(property="description", type="string"),
     *         @OA\Property(property="is_active", type="boolean")
     *     )),
     *     @OA\Response(response=201, description="Unit created")
     * )
     */
    public function store(StoreUnitRequest $request)
    {
        $unit = Unit::create($request->validated());

        return $this->successResponse(
            new UnitResource($unit),
            'Unit created successfully',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/units/{id}",
     *     tags={"Units"},
     *     summary="Get a unit",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Unit details")
     * )
     */
    public function show(Unit $unit)
    {
        $this->authorize('view', $unit);

        return $this->successResponse(
            new UnitResource($unit->loadCount('products'))
        );
    }

    /**
     * @OA\Put(
     *     path="/units/{id}",
     *     tags={"Units"},
     *     summary="Update a unit",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="abbreviation", type="string"),
     *         @OA\Property(property="description", type="string"),
     *         @OA\Property(property="is_active", type="boolean")
     *     )),
     *     @OA\Response(response=200, description="Unit updated")
     * )
     */
    public function update(UpdateUnitRequest $request, Unit $unit)
    {
        $unit->update($request->validated());

        return $this->successResponse(
            new UnitResource($unit),
            'Unit updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *     path="/units/{id}",
     *     tags={"Units"},
     *     summary="Delete a unit",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Unit deleted")
     * )
     */
    public function destroy(Unit $unit)
    {
        $this->authorize('delete', $unit);

        $unit->delete();

        return $this->successResponse(null, 'Unit deleted successfully');
    }

    /**
     * @OA\Post(
     *     path="/units/{id}/restore",
     *     tags={"Units"},
     *     summary="Restore a deleted unit",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Unit restored")
     * )
     */
    public function restore(Unit $unit)
    {
        $this->authorize('restore', $unit);

        $unit->restore();

        return $this->successResponse(
            new UnitResource($unit),
            'Unit restored successfully'
        );
    }
}
