<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVendorRequest;
use App\Http\Requests\UpdateVendorRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $vendors = Vendor::with(['user', 'products'])->withCount('products')->paginate(15);
        
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'data' => $vendors
            ]);
        }
        
        return view('vendors.index', compact('vendors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vendors.create');
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
            'address' => 'nullable|string',
            'business_name' => 'nullable|string|max:255',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Get vendor role ID
            $vendorRoleId = Role::where('name', 'vendor')->first()->id;
            
            // Create user first
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
                'role_id' => $vendorRoleId,
            ]);
            
            // Then create vendor profile
            $vendor = Vendor::create([
                'id' => $user->id,
                'address' => $validated['address'] ?? null,
                'business_name' => $validated['business_name'] ?? null,
            ]);
            
            DB::commit();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Vendor created successfully',
                    'data' => [
                        'vendor' => $vendor,
                        'user' => $user,
                    ]
                ], Response::HTTP_CREATED);
            }
            
            return redirect()->route('vendors.index')
                ->with('success', 'Vendor created successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to create vendor',
                    'error' => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
            return back()->withInput()
                ->with('error', 'Failed to create vendor: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Vendor $vendor)
    {
        $vendor->load(['user', 'products']);
        
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'data' => $vendor
            ]);
        }
        
        return view('vendors.show', compact('vendor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vendor $vendor)
    {
        $vendor->load('user');
        return view('vendors.edit', compact('vendor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $vendor->user->id,
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'business_name' => 'nullable|string|max:255',
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
                $vendor->user->update($userData);
            }
            
            // Update vendor profile
            $vendorData = [];
            if (isset($validated['address'])) $vendorData['address'] = $validated['address'];
            if (isset($validated['business_name'])) $vendorData['business_name'] = $validated['business_name'];
            
            if (!empty($vendorData)) {
                $vendor->update($vendorData);
            }
            
            DB::commit();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Vendor updated successfully',
                    'data' => $vendor->fresh(['user'])
                ]);
            }
            
            return redirect()->route('vendors.show', $vendor)
                ->with('success', 'Vendor updated successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to update vendor',
                    'error' => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
            return back()->withInput()
                ->with('error', 'Failed to update vendor: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Vendor $vendor)
    {
        // Check if vendor has products
        if ($vendor->products()->exists()) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete vendor with existing products'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            return back()->with('error', 'Cannot delete vendor with existing products');
        }
        
        DB::beginTransaction();
        
        try {
            // Delete the vendor profile
            $vendor->delete();
            
            // Delete the user record
            $vendor->user->delete();
            
            DB::commit();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Vendor deleted successfully'
                ]);
            }
            
            return redirect()->route('vendors.index')
                ->with('success', 'Vendor deleted successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to delete vendor',
                    'error' => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
            return back()->with('error', 'Failed to delete vendor: ' . $e->getMessage());
        }
    }
}