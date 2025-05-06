<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderDetailRequest;
use App\Http\Requests\UpdateOrderDetailRequest;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class OrderDetailController extends Controller
{
    /**
     * Display a listing of the order details.
     */
    public function index(Order $order): JsonResponse
    {
        $orderDetails = OrderDetail::with('product')
            ->where('order_id', $order->id)
            ->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $orderDetails
        ]);
    }

    /**
     * Store a newly created order detail.
     */
    public function store(StoreOrderDetailRequest $request, Order $order): JsonResponse
    {
        // Only allow adding items to pending orders
        if ($order->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot add items to non-pending orders'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        $validated = $request->validated();
        
        DB::beginTransaction();
        
        try {
            $product = Product::findOrFail($validated['product_id']);
            
            // Check if product already exists in the order
            $existingDetail = OrderDetail::where('order_id', $order->id)
                ->where('product_id', $product->id)
                ->first();
            
            if ($existingDetail) {
                // Update quantity of existing item
                $newQuantity = $existingDetail->quantity + $validated['quantity'];
                
                // Check stock
                if ($product->stock_quantity < $validated['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }
                
                $existingDetail->update([
                    'quantity' => $newQuantity
                ]);
                
                $orderDetail = $existingDetail;
            } else {
                // Create new order detail
                // Check stock
                if ($product->stock_quantity < $validated['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }
                
                $orderDetail = OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $validated['quantity'],
                    'price' => $product->price
                ]);
            }
            
            // Update product stock
            $product->decrement('stock_quantity', $validated['quantity']);
            
            // Recalculate order total
            $this->recalculateOrderTotal($order);
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Product added to order successfully',
                'data' => $orderDetail->load('product')
            ], Response::HTTP_CREATED);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add product to order',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified order detail.
     */
    public function show(Order $order, OrderDetail $orderDetail): JsonResponse
    {
        // Ensure the order detail belongs to the specified order
        if ($orderDetail->order_id !== $order->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order detail not found for the specified order'
            ], Response::HTTP_NOT_FOUND);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $orderDetail->load('product')
        ]);
    }

    /**
     * Update the specified order detail.
     */
    public function update(UpdateOrderDetailRequest $request, Order $order, OrderDetail $orderDetail): JsonResponse
    {
        // Ensure the order detail belongs to the specified order
        if ($orderDetail->order_id !== $order->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order detail not found for the specified order'
            ], Response::HTTP_NOT_FOUND);
        }
        
        // Only allow updating items in pending orders
        if ($order->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot update items in non-pending orders'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        $validated = $request->validated();
        
        DB::beginTransaction();
        
        try {
            $product = $orderDetail->product;
            $quantityDiff = $validated['quantity'] - $orderDetail->quantity;
            
            // Check stock if increasing quantity
            if ($quantityDiff > 0 && $product->stock_quantity < $quantityDiff) {
                throw new \Exception("Insufficient stock for product: {$product->name}");
            }
            
            // Update order detail
            $orderDetail->update([
                'quantity' => $validated['quantity']
            ]);
            
            // Update product stock
            if ($quantityDiff > 0) {
                // Decreasing stock
                $product->decrement('stock_quantity', $quantityDiff);
            } else if ($quantityDiff < 0) {
                // Increasing stock
                $product->increment('stock_quantity', abs($quantityDiff));
            }
            
            // Recalculate order total
            $this->recalculateOrderTotal($order);
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Order detail updated successfully',
                'data' => $orderDetail->fresh('product')
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update order detail',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified order detail.
     */
    public function destroy(Order $order, OrderDetail $orderDetail): JsonResponse
    {
        // Ensure the order detail belongs to the specified order
        if ($orderDetail->order_id !== $order->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order detail not found for the specified order'
            ], Response::HTTP_NOT_FOUND);
        }
        
        // Only allow removing items from pending orders
        if ($order->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot remove items from non-pending orders'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        DB::beginTransaction();
        
        try {
            // Return quantity to product stock
            $orderDetail->product->increment('stock_quantity', $orderDetail->quantity);
            
            // Delete the order detail
            $orderDetail->delete();
            
            // Recalculate order total
            $this->recalculateOrderTotal($order);
            
            // If no items left, cancel the order
            if (!$order->orderDetails()->exists()) {
                $order->update(['status' => 'cancelled']);
                $message = 'Product removed and order cancelled (no items left)';
            } else {
                $message = 'Product removed from order successfully';
            }
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => $message
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to remove product from order',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Recalculate order total based on order details.
     */
    private function recalculateOrderTotal(Order $order): void
    {
        $total = OrderDetail::where('order_id', $order->id)
            ->selectRaw('SUM(price * quantity) as total')
            ->first()
            ->total ?? 0;
            
        $order->update(['total_amount' => $total]);
    }
}