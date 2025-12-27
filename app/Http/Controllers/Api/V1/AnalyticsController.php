<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Models\Market;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/analytics/dashboard",
     *     tags={"Analytics"},
     *     summary="Get dashboard analytics",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Dashboard statistics")
     * )
     */
    public function dashboard(Request $request)
    {
        $today = now()->startOfDay();
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        // Order statistics
        $totalOrders = Order::count();
        $ordersToday = Order::where('created_at', '>=', $today)->count();
        $ordersThisMonth = Order::where('created_at', '>=', $thisMonth)->count();
        $pendingOrders = Order::where('status', Order::STATUS_PENDING)->count();

        // Revenue statistics
        $totalRevenue = Payment::completed()->sum('amount');
        $revenueToday = Payment::completed()->where('paid_at', '>=', $today)->sum('amount');
        $revenueThisMonth = Payment::completed()->where('paid_at', '>=', $thisMonth)->sum('amount');
        $revenueLastMonth = Payment::completed()
            ->whereBetween('paid_at', [$lastMonth, $thisMonth])
            ->sum('amount');

        // User statistics
        $totalUsers = User::count();
        $newUsersThisMonth = User::where('created_at', '>=', $thisMonth)->count();
        $activeCustomers = Order::where('created_at', '>=', $thisMonth)
            ->distinct('user_id')
            ->count('user_id');

        // Product & Market statistics
        $totalProducts = Product::count();
        $totalMarkets = Market::count();
        $activeMarkets = Market::where('is_active', true)->count();

        // Order status breakdown
        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        return $this->successResponse([
            'orders' => [
                'total' => $totalOrders,
                'today' => $ordersToday,
                'this_month' => $ordersThisMonth,
                'pending' => $pendingOrders,
                'by_status' => $ordersByStatus,
            ],
            'revenue' => [
                'total' => $totalRevenue,
                'today' => $revenueToday,
                'this_month' => $revenueThisMonth,
                'last_month' => $revenueLastMonth,
                'growth_percent' => $revenueLastMonth > 0 
                    ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
                    : 0,
            ],
            'users' => [
                'total' => $totalUsers,
                'new_this_month' => $newUsersThisMonth,
                'active_customers' => $activeCustomers,
            ],
            'catalog' => [
                'total_products' => $totalProducts,
                'total_markets' => $totalMarkets,
                'active_markets' => $activeMarkets,
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/analytics/revenue",
     *     tags={"Analytics"},
     *     summary="Get revenue analytics",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="period", in="query", @OA\Schema(type="string", enum={"week", "month", "year"})),
     *     @OA\Response(response=200, description="Revenue data")
     * )
     */
    public function revenue(Request $request)
    {
        $period = $request->get('period', 'month');
        
        $query = Payment::completed();

        if ($period === 'week') {
            $startDate = now()->subWeek();
            $groupBy = 'DATE(paid_at)';
        } elseif ($period === 'year') {
            $startDate = now()->subYear();
            $groupBy = "DATE_FORMAT(paid_at, '%Y-%m')";
        } else {
            $startDate = now()->subMonth();
            $groupBy = 'DATE(paid_at)';
        }

        $revenue = $query->where('paid_at', '>=', $startDate)
            ->select(DB::raw("$groupBy as date"), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $this->successResponse([
            'period' => $period,
            'data' => $revenue,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/analytics/top-products",
     *     tags={"Analytics"},
     *     summary="Get top selling products",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Top products")
     * )
     */
    public function topProducts(Request $request)
    {
        $limit = $request->integer('limit', 10);

        $products = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.total_price) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as order_count')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();

        return $this->successResponse($products);
    }

    /**
     * @OA\Get(
     *     path="/analytics/top-markets",
     *     tags={"Analytics"},
     *     summary="Get top performing markets",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Top markets")
     * )
     */
    public function topMarkets(Request $request)
    {
        $limit = $request->integer('limit', 10);

        $markets = DB::table('orders')
            ->join('markets', 'orders.market_id', '=', 'markets.id')
            ->select(
                'markets.id',
                'markets.name',
                DB::raw('COUNT(orders.id) as order_count'),
                DB::raw('SUM(orders.total) as total_revenue')
            )
            ->where('orders.status', '!=', Order::STATUS_CANCELLED)
            ->groupBy('markets.id', 'markets.name')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();

        return $this->successResponse($markets);
    }

    /**
     * @OA\Get(
     *     path="/analytics/orders-trend",
     *     tags={"Analytics"},
     *     summary="Get orders trend",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="days", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Orders trend data")
     * )
     */
    public function ordersTrend(Request $request)
    {
        $days = $request->integer('days', 30);

        $trend = Order::where('created_at', '>=', now()->subDays($days))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $this->successResponse($trend);
    }
}
