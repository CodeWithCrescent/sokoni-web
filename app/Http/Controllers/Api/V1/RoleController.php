<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\RoleResource;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/roles",
     *     tags={"Roles"},
     *     summary="List all roles",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="with_trashed", in="query", @OA\Schema(type="boolean")),
     *     @OA\Response(response=200, description="List of roles")
     * )
     */
    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('roles.view')) {
            return $this->errorResponse('Forbidden', 403);
        }

        $query = Role::query()->with('permissions')->withCount('users');

        if ($request->boolean('with_trashed') && $request->user()->hasPermission('roles.restore')) {
            $query->withTrashed();
        }

        $roles = $query->orderBy('name')->get();

        return $this->successResponse(RoleResource::collection($roles));
    }

    /**
     * @OA\Post(
     *     path="/roles",
     *     tags={"Roles"},
     *     summary="Create a new role",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"name", "slug"},
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="slug", type="string"),
     *         @OA\Property(property="description", type="string"),
     *         @OA\Property(property="is_default", type="boolean"),
     *         @OA\Property(property="permissions", type="array", @OA\Items(type="integer"))
     *     )),
     *     @OA\Response(response=201, description="Role created")
     * )
     */
    public function store(Request $request)
    {
        if (!$request->user()->hasPermission('roles.create')) {
            return $this->errorResponse('Forbidden', 403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:roles,slug'],
            'description' => ['nullable', 'string'],
            'is_default' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        if ($request->boolean('is_default')) {
            Role::where('is_default', true)->update(['is_default' => false]);
        }

        $role = Role::create($request->only(['name', 'slug', 'description', 'is_default']));

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return $this->successResponse(
            new RoleResource($role->load('permissions')),
            'Role created successfully',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/roles/{id}",
     *     tags={"Roles"},
     *     summary="Get a role",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Role details")
     * )
     */
    public function show(Request $request, Role $role)
    {
        if (!$request->user()->hasPermission('roles.view')) {
            return $this->errorResponse('Forbidden', 403);
        }

        return $this->successResponse(
            new RoleResource($role->load('permissions')->loadCount('users'))
        );
    }

    /**
     * @OA\Put(
     *     path="/roles/{id}",
     *     tags={"Roles"},
     *     summary="Update a role",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="slug", type="string"),
     *         @OA\Property(property="description", type="string"),
     *         @OA\Property(property="is_default", type="boolean"),
     *         @OA\Property(property="permissions", type="array", @OA\Items(type="integer"))
     *     )),
     *     @OA\Response(response=200, description="Role updated")
     * )
     */
    public function update(Request $request, Role $role)
    {
        if (!$request->user()->hasPermission('roles.edit')) {
            return $this->errorResponse('Forbidden', 403);
        }

        $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'required', 'string', 'max:255', 'unique:roles,slug,' . $role->id],
            'description' => ['nullable', 'string'],
            'is_default' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        if ($request->boolean('is_default') && !$role->is_default) {
            Role::where('is_default', true)->update(['is_default' => false]);
        }

        $role->update($request->only(['name', 'slug', 'description', 'is_default']));

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return $this->successResponse(
            new RoleResource($role->load('permissions')),
            'Role updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *     path="/roles/{id}",
     *     tags={"Roles"},
     *     summary="Delete a role",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Role deleted")
     * )
     */
    public function destroy(Request $request, Role $role)
    {
        if (!$request->user()->hasPermission('roles.delete')) {
            return $this->errorResponse('Forbidden', 403);
        }

        if ($role->slug === 'admin') {
            return $this->errorResponse('Cannot delete the admin role', 422);
        }

        if ($role->users()->count() > 0) {
            return $this->errorResponse('Cannot delete a role with assigned users', 422);
        }

        $role->delete();

        return $this->successResponse(null, 'Role deleted successfully');
    }

    /**
     * @OA\Post(
     *     path="/roles/{id}/restore",
     *     tags={"Roles"},
     *     summary="Restore a deleted role",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Role restored")
     * )
     */
    public function restore(Request $request, Role $role)
    {
        if (!$request->user()->hasPermission('roles.restore')) {
            return $this->errorResponse('Forbidden', 403);
        }

        $role->restore();

        return $this->successResponse(
            new RoleResource($role->load('permissions')),
            'Role restored successfully'
        );
    }
}
