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
    public function index(Request $request): JsonResponse
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
        
        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        DB::beginTransaction();
        
        try {
            // Create the order
            $order = Order::create([
                'customer_id' => $validated['customer_id'],
                'delivery_id' => $validated['delivery_id'] ?? null,
                'order_date' => now(),
                'status' => 'pending',
                'total_amount' => 0, // Will calculate this after adding products
            ]);
            
            $totalAmount = 0;
            
            // Add products to the order
            foreach ($validated['products'] as $productData) {
                $product = Product::findOrFail($productData['product_id']);
                
                // Check stock availability
                if ($product->stock_quantity < $productData['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }
                
                // Create order detail
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $productData['quantity'],
                    'price' => $product->price, // Use current price from the product
                ]);
                
                // Update stock quantity
                $product->decrement('stock_quantity', $productData['quantity']);
                
                // Add to total
                $totalAmount += ($product->price * $productData['quantity']);
            }
            
            // Update order total
            $order->update(['total_amount' => $totalAmount]);
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Order created successfully',
                'data' => $order->load(['customer.user', 'deliveryPersonnel.user', 'orderDetails.product'])
            ], Response::HTTP_CREATED);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $order->load([
                'customer.user', 
                'deliveryPersonnel.user', 
                'orderDetails.product'
            ])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $validated = $request->validated();
        
        // Only allow updating certain fields
        $orderData = [];
        
        if (isset($validated['delivery_id'])) {
            $orderData['delivery_id'] = $validated['delivery_id'];
        }
        
        if (isset($validated['status'])) {
            $orderData['status'] = $validated['status'];
        }
        
        if (!empty($orderData)) {
            $order->update($orderData);
        }
        
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order): JsonResponse
    {
        // Only allow cancellation, not actual deletion for orders
        if ($order->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only pending orders can be cancelled'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        DB::beginTransaction();
        
        try {
            // Update status to cancelled instead of deleting
            $order->update(['status' => 'cancelled']);
            
            // Return products to inventory
            foreach ($order->orderDetails as $detail) {
                $detail->product->increment('stock_quantity', $detail->quantity);
            }
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Order cancelled successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to cancel order',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}