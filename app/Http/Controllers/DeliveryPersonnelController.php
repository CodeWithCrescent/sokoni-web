<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeliveryPersonnelRequest;
use App\Http\Requests\UpdateDeliveryPersonnelRequest;
use App\Models\DeliveryPersonnel;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DeliveryPersonnelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $deliveryPersonnel = DeliveryPersonnel::with(['user', 'orders'])->withCount('orders')->paginate(15);
        
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'data' => $deliveryPersonnel
            ]);
        }
        
        return view('delivery-personnel.index', compact('deliveryPersonnel'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('delivery-personnel.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'vehicle_type' => 'nullable|string|max:100',
            'license_plate' => 'nullable|string|max:50',
            'availability_status' => 'nullable|in:available,on_delivery,unavailable',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Get delivery role ID
            $deliveryRoleId = Role::where('name', 'delivery')->first()->id;
            
            // Create user first
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
                'role_id' => $deliveryRoleId,
            ]);
            
            // Then create delivery personnel profile
            $deliveryPersonnel = DeliveryPersonnel::create([
                'id' => $user->id,
                'vehicle_type' => $validated['vehicle_type'] ?? null,
                'license_plate' => $validated['license_plate'] ?? null,
                'availability_status' => $validated['availability_status'] ?? 'available',
            ]);
            
            DB::commit();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Delivery personnel created successfully',
                    'data' => [
                        'delivery_personnel' => $deliveryPersonnel,
                        'user' => $user,
                    ]
                ], Response::HTTP_CREATED);
            }
            
            return redirect()->route('delivery-personnel.index')
                ->with('success', 'Delivery personnel created successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to create delivery personnel',
                    'error' => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
            return back()->withInput()
                ->with('error', 'Failed to create delivery personnel: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, DeliveryPersonnel $deliveryPersonnel)
    {
        $deliveryPersonnel->load(['user', 'orders.customer.user']);
        
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'data' => $deliveryPersonnel
            ]);
        }
        
        return view('delivery-personnel.show', compact('deliveryPersonnel'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DeliveryPersonnel $deliveryPersonnel)
    {
        $deliveryPersonnel->load('user');
        return view('delivery-personnel.edit', compact('deliveryPersonnel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DeliveryPersonnel $deliveryPersonnel)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $deliveryPersonnel->user->id,
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string|max:20',
            'vehicle_type' => 'nullable|string|max:100',
            'license_plate' => 'nullable|string|max:50',
            'availability_status' => 'nullable|in:available,on_delivery,unavailable',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Update user data
            $userData = [];
            if (isset($validated['name'])) $userData['name'] = $validated['name'];
            if (isset($validated['email'])) $userData['email'] = $validated['email'];
            if (isset($validated['phone'])) $userData['phone_number'] = $validated['phone'];
            if (isset($validated['password'])) $userData['password'] = Hash::make($validated['password']);
            
            if (!empty($userData)) {
                $deliveryPersonnel->user->update($userData);
            }
            
            // Update delivery personnel profile
            $profileData = [];
            if (isset($validated['vehicle_type'])) $profileData['vehicle_type'] = $validated['vehicle_type'];
            if (isset($validated['license_plate'])) $profileData['license_plate'] = $validated['license_plate'];
            if (isset($validated['availability_status'])) $profileData['availability_status'] = $validated['availability_status'];
            
            if (!empty($profileData)) {
                $deliveryPersonnel->update($profileData);
            }
            
            DB::commit();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Delivery personnel updated successfully',
                    'data' => $deliveryPersonnel->fresh(['user'])
                ]);
            }
            
            return redirect()->route('delivery-personnel.show', $deliveryPersonnel)
                ->with('success', 'Delivery personnel updated successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to update delivery personnel',
                    'error' => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
            return back()->withInput()
                ->with('error', 'Failed to update delivery personnel: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, DeliveryPersonnel $deliveryPersonnel)
    {
        // Check if delivery personnel has active orders
        if ($deliveryPersonnel->orders()->whereIn('status', ['pending', 'processing'])->exists()) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete delivery personnel with active orders'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            return back()->with('error', 'Cannot delete delivery personnel with active orders');
        }
        
        DB::beginTransaction();
        
        try {
            // Delete the delivery personnel profile
            $deliveryPersonnel->delete();
            
            // Delete the user record
            $deliveryPersonnel->user->delete();
            
            DB::commit();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Delivery personnel deleted successfully'
                ]);
            }
            
            return redirect()->route('delivery-personnel.index')
                ->with('success', 'Delivery personnel deleted successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to delete delivery personnel',
                    'error' => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
            return back()->with('error', 'Failed to delete delivery personnel: ' . $e->getMessage());
        }
    }
}