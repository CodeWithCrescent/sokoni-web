<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $customers = Customer::with(['user', 'orders'])->withCount('orders')->paginate(15);
        
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'data' => $customers
            ]);
        }
        
        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customers.create');
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
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
        ]);
        
        // Start a transaction to ensure both user and customer are created
        DB::beginTransaction();
        
        try {
            // Get customer role ID
            $customerRoleId = Role::where('name', 'customer')->first()->id;
            
            // Create user first
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
                'role_id' => $customerRoleId,
            ]);
            
            // Then create customer profile
            $customer = Customer::create([
                'id' => $user->id,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'zip_code' => $validated['zip_code'] ?? null,
                'phone_number' => $validated['phone'] ?? null,
            ]);
            
            DB::commit();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Customer created successfully',
                    'data' => [
                        'customer' => $customer,
                        'user' => $user,
                    ]
                ], Response::HTTP_CREATED);
            }
            
            return redirect()->route('customers.index')
                ->with('success', 'Customer created successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to create customer',
                    'error' => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
            return back()->withInput()
                ->with('error', 'Failed to create customer: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Customer $customer)
    {
        $customer->load(['user', 'orders.orderDetails.product']);
        
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'data' => $customer
            ]);
        }
        
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $customer->load('user');
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $customer->user->id,
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
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
                $customer->user->update($userData);
            }
            
            // Update customer profile
            $customerData = [];
            if (isset($validated['address'])) $customerData['address'] = $validated['address'];
            if (isset($validated['city'])) $customerData['city'] = $validated['city'];
            if (isset($validated['state'])) $customerData['state'] = $validated['state'];
            if (isset($validated['zip_code'])) $customerData['zip_code'] = $validated['zip_code'];
            if (isset($validated['phone'])) $customerData['phone_number'] = $validated['phone'];
            
            if (!empty($customerData)) {
                $customer->update($customerData);
            }
            
            DB::commit();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Customer updated successfully',
                    'data' => $customer->fresh(['user'])
                ]);
            }
            
            return redirect()->route('customers.show', $customer)
                ->with('success', 'Customer updated successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to update customer',
                    'error' => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
            return back()->withInput()
                ->with('error', 'Failed to update customer: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Customer $customer)
    {
        DB::beginTransaction();
        
        try {
            // Check if customer has orders
            if ($customer->orders()->exists()) {
                if ($request->wantsJson() || $request->is('api/*')) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Cannot delete customer with existing orders'
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
                
                return back()->with('error', 'Cannot delete customer with existing orders');
            }
            
            // Delete the customer profile
            $customer->delete();
            
            // Delete the user record
            $customer->user->delete();
            
            DB::commit();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Customer deleted successfully'
                ]);
            }
            
            return redirect()->route('customers.index')
                ->with('success', 'Customer deleted successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to delete customer',
                    'error' => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
            return back()->with('error', 'Failed to delete customer: ' . $e->getMessage());
        }
    }
}