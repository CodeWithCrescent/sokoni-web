<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\StoreOrderRequest;
use App\Http\Requests\Api\V1\UpdateOrderStatusRequest;
use App\Http\Resources\V1\OrderResource;
use App\Models\MarketProduct;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/orders",
     *     tags={"Orders"},
     *     summary="List orders",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="market_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=15)),
     *     @OA\Response(response=200, description="List of orders")
     * )
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Order::query()
            ->with(['user', 'market', 'items', 'latestPayment'])
            ->withCount('items');

        // Role-based filtering
        if ($user->isCustomer()) {
            $query->forUser($user->id);
        } elseif ($user->isCollector()) {
            $query->forCollector($user->id);
        } elseif ($user->isDriver()) {
            $query->forDriver($user->id);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('market_id')) {
            $query->forMarket($request->integer('market_id'));
        }

        $orders = $query->latest()->paginate($request->integer('per_page', 15));

        return $this->paginatedResponse($orders, OrderResource::class);
    }

    /**
     * @OA\Post(
     *     path="/orders",
     *     tags={"Orders"},
     *     summary="Create a new order",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"market_id", "items", "delivery_address", "delivery_phone"},
     *         @OA\Property(property="market_id", type="integer"),
     *         @OA\Property(property="items", type="array", @OA\Items(
     *             @OA\Property(property="market_product_id", type="integer"),
     *             @OA\Property(property="quantity", type="number"),
     *             @OA\Property(property="notes", type="string")
     *         )),
     *         @OA\Property(property="delivery_address", type="string"),
     *         @OA\Property(property="delivery_phone", type="string"),
     *         @OA\Property(property="delivery_instructions", type="string")
     *     )),
     *     @OA\Response(response=201, description="Order created")
     * )
     */
    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();

        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => $user->id,
                'market_id' => $data['market_id'],
                'status' => Order::STATUS_PENDING,
                'delivery_address' => $data['delivery_address'],
                'delivery_latitude' => $data['delivery_latitude'] ?? null,
                'delivery_longitude' => $data['delivery_longitude'] ?? null,
                'delivery_phone' => $data['delivery_phone'],
                'delivery_instructions' => $data['delivery_instructions'] ?? null,
                'notes' => $data['notes'] ?? null,
                'delivery_fee' => 2000, // Default delivery fee - should be calculated
                'service_fee' => 500, // Default service fee
            ]);

            foreach ($data['items'] as $item) {
                $marketProduct = MarketProduct::with(['product.unit'])->findOrFail($item['market_product_id']);
                
                $order->items()->create([
                    'market_product_id' => $marketProduct->id,
                    'product_id' => $marketProduct->product_id,
                    'product_name' => $marketProduct->product->name,
                    'quantity' => $item['quantity'],
                    'unit_name' => $marketProduct->product->unit->abbreviation ?? 'unit',
                    'unit_price' => $marketProduct->current_price,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            $order->calculateTotals();

            // Create initial status history
            $order->statusHistories()->create([
                'user_id' => $user->id,
                'to_status' => Order::STATUS_PENDING,
                'notes' => 'Order placed',
            ]);

            DB::commit();

            return $this->successResponse(
                new OrderResource($order->load(['items', 'market', 'user'])),
                'Order created successfully',
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to create order: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/orders/{id}",
     *     tags={"Orders"},
     *     summary="Get order details",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Order details")
     * )
     */
    public function show(Request $request, Order $order)
    {
        $user = $request->user();

        // Check access
        if (!$user->isAdmin() && $order->user_id !== $user->id && 
            $order->collector_id !== $user->id && $order->driver_id !== $user->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        return $this->successResponse(
            new OrderResource($order->load([
                'user', 'market', 'collector', 'driver', 
                'items.product', 'latestPayment', 'statusHistories.user'
            ]))
        );
    }

    /**
     * @OA\Put(
     *     path="/orders/{id}/status",
     *     tags={"Orders"},
     *     summary="Update order status",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"status"},
     *         @OA\Property(property="status", type="string"),
     *         @OA\Property(property="notes", type="string"),
     *         @OA\Property(property="cancellation_reason", type="string")
     *     )),
     *     @OA\Response(response=200, description="Order status updated")
     * )
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $data = $request->validated();
        $user = $request->user();

        // Validate status transition
        $allowedTransitions = [
            Order::STATUS_PENDING => [Order::STATUS_CONFIRMED, Order::STATUS_CANCELLED],
            Order::STATUS_CONFIRMED => [Order::STATUS_COLLECTING, Order::STATUS_CANCELLED],
            Order::STATUS_COLLECTING => [Order::STATUS_COLLECTED],
            Order::STATUS_COLLECTED => [Order::STATUS_IN_TRANSIT],
            Order::STATUS_IN_TRANSIT => [Order::STATUS_DELIVERED],
        ];

        if (!$user->isAdmin() && isset($allowedTransitions[$order->status])) {
            if (!in_array($data['status'], $allowedTransitions[$order->status])) {
                return $this->errorResponse('Invalid status transition', 422);
            }
        }

        if ($data['status'] === Order::STATUS_CANCELLED) {
            $order->cancellation_reason = $data['cancellation_reason'] ?? null;
        }

        $order->updateStatus($data['status'], $user->id, $data['notes'] ?? null);

        return $this->successResponse(
            new OrderResource($order->load(['items', 'statusHistories'])),
            'Order status updated successfully'
        );
    }

    /**
     * @OA\Post(
     *     path="/orders/{id}/assign-collector",
     *     tags={"Orders"},
     *     summary="Assign collector to order",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"collector_id"},
     *         @OA\Property(property="collector_id", type="integer")
     *     )),
     *     @OA\Response(response=200, description="Collector assigned")
     * )
     */
    public function assignCollector(Request $request, Order $order)
    {
        $request->validate(['collector_id' => 'required|exists:users,id']);

        $order->collector_id = $request->collector_id;
        $order->save();

        return $this->successResponse(
            new OrderResource($order->load(['collector'])),
            'Collector assigned successfully'
        );
    }

    /**
     * @OA\Post(
     *     path="/orders/{id}/assign-driver",
     *     tags={"Orders"},
     *     summary="Assign driver to order",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"driver_id"},
     *         @OA\Property(property="driver_id", type="integer")
     *     )),
     *     @OA\Response(response=200, description="Driver assigned")
     * )
     */
    public function assignDriver(Request $request, Order $order)
    {
        $request->validate(['driver_id' => 'required|exists:users,id']);

        $order->driver_id = $request->driver_id;
        $order->save();

        return $this->successResponse(
            new OrderResource($order->load(['driver'])),
            'Driver assigned successfully'
        );
    }

    /**
     * @OA\Get(
     *     path="/orders/my-orders",
     *     tags={"Orders"},
     *     summary="Get current user's orders",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="List of user's orders")
     * )
     */
    public function myOrders(Request $request)
    {
        $orders = Order::forUser($request->user()->id)
            ->with(['market', 'items', 'latestPayment'])
            ->withCount('items')
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $this->paginatedResponse($orders, OrderResource::class);
    }
}
