<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMarketRequest;
use App\Http\Requests\UpdateMarketRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\Market;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MarketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $markets = Market::with(['user', 'products'])->withCount('products')->paginate(15);
        
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'data' => $markets
            ]);
        }
        
        return view('markets.index', compact('markets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('markets.create');
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
            // Get market role ID
            $marketRoleId = Role::where('name', 'vendor')->first()->id;
            
            // Create user first
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
                'role_id' => $marketRoleId,
            ]);
            
            // Then create market profile
            $market = Market::create([
                'id' => $user->id,
                'address' => $validated['address'] ?? null,
                'business_name' => $validated['business_name'] ?? null,
            ]);
            
            DB::commit();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Market created successfully',
                    'data' => [
                        'market' => $market,
                        'user' => $user,
                    ]
                ], Response::HTTP_CREATED);
            }
            
            return redirect()->route('markets.index')
                ->with('success', 'Market created successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to create market',
                    'error' => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
            return back()->withInput()
                ->with('error', 'Failed to create market: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Market $market)
    {
        $market->load(['user', 'products']);
        
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'data' => $market
            ]);
        }
        
        return view('markets.show', compact('market'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Market $market)
    {
        $market->load('user');
        return view('markets.edit', compact('market'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Market $market)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $market->user->id,
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
                $market->user->update($userData);
            }
            
            // Update market profile
            $marketData = [];
            if (isset($validated['address'])) $marketData['address'] = $validated['address'];
            if (isset($validated['business_name'])) $marketData['business_name'] = $validated['business_name'];
            
            if (!empty($marketData)) {
                $market->update($marketData);
            }
            
            DB::commit();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Market updated successfully',
                    'data' => $market->fresh(['user'])
                ]);
            }
            
            return redirect()->route('markets.show', $market)
                ->with('success', 'Market updated successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to update market',
                    'error' => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
            return back()->withInput()
                ->with('error', 'Failed to update market: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Market $market)
    {
        // Check if market has products
        if ($market->products()->exists()) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete market with existing products'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            return back()->with('error', 'Cannot delete market with existing products');
        }
        
        DB::beginTransaction();
        
        try {
            // Delete the market profile
            $market->delete();
            
            // Delete the user record
            $market->user->delete();
            
            DB::commit();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Market deleted successfully'
                ]);
            }
            
            return redirect()->route('markets.index')
                ->with('success', 'Market deleted successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to delete market',
                    'error' => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
            return back()->with('error', 'Failed to delete market: ' . $e->getMessage());
        }
    }
}