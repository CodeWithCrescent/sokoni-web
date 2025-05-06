<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVendorRequest;
use App\Http\Requests\UpdateVendorRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $vendors = Vendor::with(['user', 'products'])->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $vendors
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVendorRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        DB::beginTransaction();
        
        try {
            // Get vendor role ID
            $vendorRoleId = Role::where('name', 'vendor')->first()->id;
            
            // Create user first
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
                'role_id' => $vendorRoleId,
            ]);
            
            // Then create vendor profile
            $vendor = Vendor::create([
                'id' => $user->id,
                'address' => $validated['address'],
            ]);
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Vendor created successfully',
                'data' => [
                    'vendor' => $vendor,
                    'user' => $user,
                ]
            ], Response::HTTP_CREATED);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create vendor',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Vendor $vendor): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $vendor->load(['user', 'products'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVendorRequest $request, Vendor $vendor): JsonResponse
    {
        $validated = $request->validated();
        
        DB::beginTransaction();
        
        try {
            // Update user data if present
            if (isset($validated['name']) || isset($validated['email']) || isset($validated['phone']) || isset($validated['password'])) {
                $userData = [];
                
                if (isset($validated['name'])) {
                    $userData['name'] = $validated['name'];
                }
                
                if (isset($validated['email'])) {
                    $userData['email'] = $validated['email'];
                }
                
                if (isset($validated['phone'])) {
                    $userData['phone'] = $validated['phone'];
                }
                
                if (isset($validated['password'])) {
                    $userData['password'] = Hash::make($validated['password']);
                }
                
                $vendor->user->update($userData);
            }
            
            // Update vendor profile
            if (isset($validated['address'])) {
                $vendor->update([
                    'address' => $validated['address']
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Vendor updated successfully',
                'data' => $vendor->fresh(['user'])
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update vendor',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vendor $vendor): JsonResponse
    {
        // Check if vendor has products
        if ($vendor->products()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete vendor with existing products'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        DB::beginTransaction();
        
        try {
            // Delete the vendor profile
            $vendor->delete();
            
            // Delete the user record
            $vendor->user->delete();
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Vendor deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete vendor',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}