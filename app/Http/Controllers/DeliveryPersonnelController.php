<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeliveryPersonnelRequest;
use App\Http\Requests\UpdateDeliveryPersonnelRequest;
use App\Models\DeliveryPersonnel;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DeliveryPersonnelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $deliveryPersonnel = DeliveryPersonnel::with(['user', 'orders'])->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $deliveryPersonnel
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDeliveryPersonnelRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        DB::beginTransaction();
        
        try {
            // Get delivery role ID
            $deliveryRoleId = Role::where('name', 'delivery')->first()->id;
            
            // Create user first
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
                'role_id' => $deliveryRoleId,
            ]);
            
            // Then create delivery personnel profile
            $deliveryPersonnel = DeliveryPersonnel::create([
                'id' => $user->id,
                'license_plate' => $validated['license_plate'],
                'status' => $validated['status'] ?? 'off_duty',
            ]);
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Delivery personnel created successfully',
                'data' => [
                    'delivery_personnel' => $deliveryPersonnel,
                    'user' => $user,
                ]
            ], Response::HTTP_CREATED);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create delivery personnel',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DeliveryPersonnel $deliveryPersonnel): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $deliveryPersonnel->load(['user', 'orders'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeliveryPersonnelRequest $request, DeliveryPersonnel $deliveryPersonnel): JsonResponse
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
                
                $deliveryPersonnel->user->update($userData);
            }
            
            // Update delivery personnel profile
            $profileData = [];
            
            if (isset($validated['license_plate'])) {
                $profileData['license_plate'] = $validated['license_plate'];
            }
            
            if (isset($validated['status'])) {
                $profileData['status'] = $validated['status'];
            }
            
            if (!empty($profileData)) {
                $deliveryPersonnel->update($profileData);
            }
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Delivery personnel updated successfully',
                'data' => $deliveryPersonnel->fresh(['user'])
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update delivery personnel',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeliveryPersonnel $deliveryPersonnel): JsonResponse
    {
        // Check if delivery personnel has active orders
        if ($deliveryPersonnel->orders()->where('status', '!=', 'delivered')->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete delivery personnel with active orders'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        DB::beginTransaction();
        
        try {
            // Delete the delivery personnel profile
            $deliveryPersonnel->delete();
            
            // Delete the user record
            $deliveryPersonnel->user->delete();
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Delivery personnel deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete delivery personnel',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}