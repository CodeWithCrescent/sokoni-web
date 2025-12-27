<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/permissions",
     *     tags={"Permissions"},
     *     summary="List all permissions",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="List of permissions")
     * )
     */
    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('permissions.view')) {
            return $this->errorResponse('Forbidden', 403);
        }

        $permissions = Permission::orderBy('group')->orderBy('name')->get();

        return $this->successResponse(PermissionResource::collection($permissions));
    }

    /**
     * @OA\Get(
     *     path="/permissions/grouped",
     *     tags={"Permissions"},
     *     summary="Get permissions grouped by category",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Grouped permissions")
     * )
     */
    public function grouped(Request $request)
    {
        if (!$request->user()->hasPermission('permissions.view')) {
            return $this->errorResponse('Forbidden', 403);
        }

        $permissions = Permission::orderBy('name')->get();
        $grouped = $permissions->groupBy('group')->map(function ($items, $group) {
            return [
                'group' => $group,
                'label' => ucfirst(str_replace('-', ' ', $group)),
                'permissions' => PermissionResource::collection($items),
            ];
        })->values();

        return $this->successResponse($grouped);
    }
}
