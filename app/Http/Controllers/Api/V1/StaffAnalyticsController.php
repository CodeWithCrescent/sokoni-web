<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffAnalyticsController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/analytics/staff/collectors",
     *     tags={"Staff Analytics"},
     *     summary="Get collector performance stats",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="period", in="query", @OA\Schema(type="string", enum={"week", "month", "year"})),
     *     @OA\Response(response=200, description="Collector performance data")
     * )
     */
    public function collectors(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);

        $collectors = User::whereHas('role', fn($q) => $q->where('slug', 'collector'))
            ->withCount([
                'collectedOrders as total_orders' => fn($q) => $q->where('created_at', '>=', $startDate),
                'collectedOrders as completed_orders' => fn($q) => $q->where('status', Order::STATUS_DELIVERED)
                    ->where('created_at', '>=', $startDate),
            ])
            ->get()
            ->map(function ($collector) use ($startDate) {
                $orders = Order::where('collector_id', $collector->id)
                    ->where('created_at', '>=', $startDate)
                    ->get();

                $avgCollectionTime = $orders->filter(fn($o) => $o->collected_at && $o->confirmed_at)
                    ->avg(fn($o) => $o->collected_at->diffInMinutes($o->confirmed_at));

                return [
                    'id' => $collector->id,
                    'name' => $collector->name,
                    'email' => $collector->email,
                    'phone' => $collector->phone,
                    'total_orders' => $collector->total_orders ?? 0,
                    'completed_orders' => $collector->completed_orders ?? 0,
                    'completion_rate' => $collector->total_orders > 0
                        ? round(($collector->completed_orders / $collector->total_orders) * 100, 1)
                        : 0,
                    'avg_collection_time_minutes' => round($avgCollectionTime ?? 0),
                ];
            })
            ->sortByDesc('total_orders')
            ->values();

        return $this->successResponse($collectors);
    }

    /**
     * @OA\Get(
     *     path="/analytics/staff/drivers",
     *     tags={"Staff Analytics"},
     *     summary="Get driver performance stats",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="period", in="query", @OA\Schema(type="string", enum={"week", "month", "year"})),
     *     @OA\Response(response=200, description="Driver performance data")
     * )
     */
    public function drivers(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);

        $drivers = User::whereHas('role', fn($q) => $q->where('slug', 'driver'))
            ->withCount([
                'deliveredOrders as total_orders' => fn($q) => $q->where('created_at', '>=', $startDate),
                'deliveredOrders as completed_orders' => fn($q) => $q->where('status', Order::STATUS_DELIVERED)
                    ->where('created_at', '>=', $startDate),
            ])
            ->get()
            ->map(function ($driver) use ($startDate) {
                $orders = Order::where('driver_id', $driver->id)
                    ->where('created_at', '>=', $startDate)
                    ->get();

                $avgDeliveryTime = $orders->filter(fn($o) => $o->delivered_at && $o->collected_at)
                    ->avg(fn($o) => $o->delivered_at->diffInMinutes($o->collected_at));

                $totalRevenue = $orders->where('status', Order::STATUS_DELIVERED)->sum('total');

                return [
                    'id' => $driver->id,
                    'name' => $driver->name,
                    'email' => $driver->email,
                    'phone' => $driver->phone,
                    'total_orders' => $driver->total_orders ?? 0,
                    'completed_orders' => $driver->completed_orders ?? 0,
                    'completion_rate' => $driver->total_orders > 0
                        ? round(($driver->completed_orders / $driver->total_orders) * 100, 1)
                        : 0,
                    'avg_delivery_time_minutes' => round($avgDeliveryTime ?? 0),
                    'total_revenue_delivered' => $totalRevenue,
                ];
            })
            ->sortByDesc('total_orders')
            ->values();

        return $this->successResponse($drivers);
    }

    /**
     * @OA\Get(
     *     path="/analytics/staff/{userId}/orders",
     *     tags={"Staff Analytics"},
     *     summary="Get staff member's order history",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="role", in="query", @OA\Schema(type="string", enum={"collector", "driver"})),
     *     @OA\Response(response=200, description="Staff order history")
     * )
     */
    public function staffOrders(Request $request, int $userId)
    {
        $role = $request->get('role', 'collector');

        $query = Order::with(['market', 'user'])
            ->when($role === 'collector', fn($q) => $q->where('collector_id', $userId))
            ->when($role === 'driver', fn($q) => $q->where('driver_id', $userId))
            ->latest();

        $orders = $query->paginate($request->integer('per_page', 20));

        return $this->paginatedResponse($orders, \App\Http\Resources\V1\OrderResource::class);
    }

    /**
     * @OA\Get(
     *     path="/analytics/staff/leaderboard",
     *     tags={"Staff Analytics"},
     *     summary="Get staff leaderboard",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="period", in="query", @OA\Schema(type="string", enum={"week", "month", "year"})),
     *     @OA\Response(response=200, description="Staff leaderboard")
     * )
     */
    public function leaderboard(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);

        // Top collectors
        $topCollectors = DB::table('orders')
            ->join('users', 'orders.collector_id', '=', 'users.id')
            ->where('orders.created_at', '>=', $startDate)
            ->whereNotNull('orders.collector_id')
            ->select(
                'users.id',
                'users.name',
                DB::raw('COUNT(orders.id) as order_count'),
                DB::raw('SUM(CASE WHEN orders.status = "delivered" THEN 1 ELSE 0 END) as completed_count')
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('order_count')
            ->limit(10)
            ->get();

        // Top drivers
        $topDrivers = DB::table('orders')
            ->join('users', 'orders.driver_id', '=', 'users.id')
            ->where('orders.created_at', '>=', $startDate)
            ->whereNotNull('orders.driver_id')
            ->select(
                'users.id',
                'users.name',
                DB::raw('COUNT(orders.id) as order_count'),
                DB::raw('SUM(orders.total) as total_delivered')
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('order_count')
            ->limit(10)
            ->get();

        return $this->successResponse([
            'period' => $period,
            'top_collectors' => $topCollectors,
            'top_drivers' => $topDrivers,
        ]);
    }

    private function getStartDate(string $period): \Carbon\Carbon
    {
        return match ($period) {
            'week' => now()->subWeek(),
            'year' => now()->subYear(),
            default => now()->subMonth(),
        };
    }
}
