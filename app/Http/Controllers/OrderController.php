<?php
/**
 * Command to create this controller:
 * php artisan make:controller OrderController --resource
 */

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer.user', 'deliveryPersonnel.user', 'orderDetails']);
        
        // Filter by customer if provided
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        
        // Filter by delivery personnel if provided
        if ($request->has('delivery_id')) {
            $query->where('delivery_id', $request->delivery_id);
        }
        
        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->has('start_date')) {
            $query->whereDate('order_date', '>=', $request->start_date);
        }
        
        if ($request->has('end_date')) {
            $query->whereDate('order_date', '<=', $request->end_date);
        }
        
        // Sort options
        if ($request->has('sort_by')) {
            $sortDirection = $request->sort_dir ?? 'desc';
            $query->orderBy($request->sort_by, $sortDirection);
        } else {
            // Default sort by latest order date
            $query->orderBy('order_date', 'desc');
        }
        
        $orders = $query->paginate($request->per_page ?? 15);
        
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'data' => $orders
            ]);
        }
        
        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('orders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'delivery_id' => 'nullable|exists:delivery_personnel,id',
            'delivery_address' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Create the order
            $order = Order::create([
                'customer_id' => $validated['customer_id'],
                'delivery_id' => $validated['delivery_id'] ?? null,
                'delivery_address' => $validated['delivery_address'] ?? null,
                'order_date' => now(),
                'status' => 'pending',
                'total_amount' => 0,
            ]);
            
            $totalAmount = 0;
            
            // Add products to the order
            foreach ($validated['products'] as $productData) {
                $product = Product::findOrFail($productData['product_id']);
                
                // Check stock availability
                if ($product->stock < $productData['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }
                
                // Create order detail
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $productData['quantity'],
                    'price' => $productData['price'],
                ]);
                
                // Update stock
                $product->decrement('stock', $productData['quantity']);
                
                // Add to total
                $totalAmount += ($productData['price'] * $productData['quantity']);
            }
            
            // Update order total
            $order->update(['total_amount' => $totalAmount]);
            
            DB::commit();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Order created successfully',
                    'data' => $order->load(['customer.user', 'deliveryPersonnel.user', 'orderDetails.product'])
                ], Response::HTTP_CREATED);
            }
            
            return redirect()->route('orders.show', $order)
                ->with('success', 'Order created successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to create order',
                    'error' => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
            return back()->withInput()
                ->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Order $order)
    {
        $order->load([
            'customer.user', 
            'deliveryPersonnel.user', 
            'orderDetails.product.category'
        ]);
        
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'data' => $order
            ]);
        }
        
        return view('orders.show', compact('order'));
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $order->load(['customer.user', 'deliveryPersonnel.user', 'orderDetails.product']);
        return view('orders.edit', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'nullable|in:pending,processing,delivered,cancelled',
            'delivery_id' => 'nullable|exists:delivery_personnel,id',
            'delivery_address' => 'nullable|string',
        ]);
        
        $order->update($validated);
        
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'message' => 'Order updated successfully',
                'data' => $order->fresh([
                    'customer.user', 
                    'deliveryPersonnel.user', 
                    'orderDetails.product'
                ])
            ]);
        }
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'Order updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Order $order)
    {
        // Only allow cancellation for pending orders
        if ($order->status !== 'pending') {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Only pending orders can be cancelled'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            return back()->with('error', 'Only pending orders can be cancelled');
        }
        
        DB::beginTransaction();
        
        try {
            // Update status to cancelled instead of deleting
            $order->update(['status' => 'cancelled']);
            
            // Return products to inventory
            foreach ($order->orderDetails as $detail) {
                $detail->product->increment('stock', $detail->quantity);
            }
            
            DB::commit();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Order cancelled successfully'
                ]);
            }
            
            return redirect()->route('orders.index')
                ->with('success', 'Order cancelled successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to cancel order',
                    'error' => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
            return back()->with('error', 'Failed to cancel order: ' . $e->getMessage());
        }
    }
}