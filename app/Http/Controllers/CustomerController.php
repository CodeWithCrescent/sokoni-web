<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $customers = Customer::with('user')->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $customers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        // Start a transaction to ensure both user and customer are created
        DB::beginTransaction();
        
        try {
            // Get customer role ID
            $customerRoleId = Role::where('name', 'customer')->first()->id;
            
            // Create user first
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
                'role_id' => $customerRoleId,
            ]);
            
            // Then create customer profile
            $customer = Customer::create([
                'id' => $user->id,
                'address' => $validated['address'],
            ]);
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Customer created successfully',
                'data' => [
                    'customer' => $customer,
                    'user' => $user,
                ]
            ], Response::HTTP_CREATED);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create customer',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $customer->load('user', 'orders')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
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
                
                $customer->user->update($userData);
            }
            
            // Update customer profile
            if (isset($validated['address'])) {
                $customer->update([
                    'address' => $validated['address']
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Customer updated successfully',
                'data' => $customer->fresh(['user'])
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update customer',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            // Delete the customer profile
            $customer->delete();
            
            // Delete the user record
            $customer->user->delete();
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Customer deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete customer',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}