<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $roles = Role::all();
        
        return response()->json([
            'status' => 'success',
            'data' => $roles
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        $role = Role::create($validated);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Role created successfully',
            'data' => $role
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $role
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $validated = $request->validated();
        
        $role->update($validated);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Role updated successfully',
            'data' => $role
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): JsonResponse
    {
        // Check if role is in use before deleting
        if ($role->users()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete role that is assigned to users'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        $role->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Role deleted successfully'
        ]);
    }
}