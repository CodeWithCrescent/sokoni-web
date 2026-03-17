<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\DeliveryPersonnel;
use App\Models\Market;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role->name;

        return match($role) {
            'admin' => $this->adminDashboard(),
            'vendor' => $this->vendorDashboard(),
            'customer' => $this->customerDashboard(),
            'delivery' => $this->deliveryDashboard(),
            default => view('dashboard'),
        };
    }

    private function adminDashboard()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_customers' => Customer::count(),
            'total_markets' => Market::count(),
            'total_delivery_personnel' => DeliveryPersonnel::count(),
            'total_categories' => Category::count(),
            
            // Revenue stats
            'total_revenue' => Order::where('status', 'delivered')->sum('total_amount'),
            'pending_revenue' => Order::whereIn('status', ['pending', 'processing'])->sum('total_amount'),
            
            // Order stats
            'pending_orders' => Order::where('status', 'pending')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            
            // Product stats
            'low_stock_products' => Product::where('stock', '<=', 10)->count(),
            'out_of_stock_products' => Product::where('stock', 0)->count(),
        ];

        // Recent orders
        $recent_orders = Order::with(['customer.user', 'deliveryPersonnel.user'])
            ->latest()
            ->take(10)
            ->get();

        // Top products
        $top_products = Product::withCount(['orderDetails'])
            ->orderBy('order_details_count', 'desc')
            ->take(5)
            ->get();

        // Monthly revenue chart data (last 6 months)
        $dbDriver = config('database.default');
        $dateFormat = $dbDriver === 'sqlite' 
            ? DB::raw("strftime('%Y-%m', order_date) as month")
            : DB::raw("DATE_FORMAT(order_date, '%Y-%m') as month");
            
        $monthly_revenue = Order::where('status', 'delivered')
            ->where('order_date', '>=', now()->subMonths(6))
            ->select(
                $dateFormat,
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('dashboards.admin', compact('stats', 'recent_orders', 'top_products', 'monthly_revenue'));
    }

    private function vendorDashboard()
    {
        $vendor = Auth::user()->vendor;
        
        // Handle case where vendor relationship doesn't exist
        if (!$vendor) {
            return $this->genericDashboard('Vendor profile not setup. Please contact administrator.');
        }

        $stats = [
            'total_products' => $vendor->products()->count(),
            'active_products' => $vendor->products()->where('stock', '>', 0)->count(),
            'out_of_stock' => $vendor->products()->where('stock', 0)->count(),
            'low_stock' => $vendor->products()->where('stock', '>', 0)->where('stock', '<=', 10)->count(),
            
            // Sales stats through order details
            'total_sales' => $vendor->products()
                ->join('order_details', 'products.id', '=', 'order_details.product_id')
                ->join('orders', 'order_details.order_id', '=', 'orders.id')
                ->where('orders.status', 'delivered')
                ->sum(DB::raw('order_details.quantity * order_details.price')),
            
            'pending_sales' => $vendor->products()
                ->join('order_details', 'products.id', '=', 'order_details.product_id')
                ->join('orders', 'order_details.order_id', '=', 'orders.id')
                ->whereIn('orders.status', ['pending', 'processing'])
                ->sum(DB::raw('order_details.quantity * order_details.price')),
                
            'total_inventory_value' => $vendor->products()->sum(DB::raw('price * stock')),
        ];

        // Recent product orders
        $recent_orders = $vendor->products()
            ->join('order_details', 'products.id', '=', 'order_details.product_id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->select('orders.*', 'products.name as product_name', 'order_details.quantity')
            ->with(['customer.user'])
            ->latest('orders.created_at')
            ->take(10)
            ->get();

        // Top selling products
        $top_products = $vendor->products()
            ->withCount(['orderDetails'])
            ->orderBy('order_details_count', 'desc')
            ->take(5)
            ->get();

        // Low stock products
        $low_stock_products = $vendor->products()
            ->where('stock', '>', 0)
            ->where('stock', '<=', 10)
            ->orderBy('stock')
            ->get();

        return view('dashboards.vendor', compact('vendor', 'stats', 'recent_orders', 'top_products', 'low_stock_products'));
    }

    private function customerDashboard()
    {
        $customer = Auth::user()->customer;
        
        // Handle case where customer relationship doesn't exist
        if (!$customer) {
            return $this->genericDashboard('Customer profile not setup. Please contact administrator.');
        }

        $stats = [
            'total_orders' => $customer->orders()->count(),
            'pending_orders' => $customer->orders()->where('status', 'pending')->count(),
            'processing_orders' => $customer->orders()->where('status', 'processing')->count(),
            'delivered_orders' => $customer->orders()->where('status', 'delivered')->count(),
            'total_spent' => $customer->orders()->where('status', 'delivered')->sum('total_amount'),
        ];

        // Recent orders
        $recent_orders = $customer->orders()
            ->with(['deliveryPersonnel.user', 'orderDetails.product'])
            ->latest()
            ->take(5)
            ->get();

        // Order status breakdown
        $order_statuses = $customer->orders()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        return view('dashboards.customer', compact('customer', 'stats', 'recent_orders', 'order_statuses'));
    }

    private function deliveryDashboard()
    {
        $delivery = Auth::user()->deliveryPersonnel;
        
        // Handle case where delivery personnel relationship doesn't exist
        if (!$delivery) {
            return $this->genericDashboard('Delivery personnel profile not setup. Please contact administrator.');
        }

        $stats = [
            'total_deliveries' => $delivery->orders()->count(),
            'pending_deliveries' => $delivery->orders()->where('status', 'pending')->count(),
            'active_deliveries' => $delivery->orders()->where('status', 'processing')->count(),
            'completed_deliveries' => $delivery->orders()->where('status', 'delivered')->count(),
            'availability_status' => $delivery->availability_status,
        ];

        // Active/upcoming deliveries
        $active_deliveries = $delivery->orders()
            ->whereIn('status', ['pending', 'processing'])
            ->with(['customer.user', 'orderDetails.product'])
            ->orderBy('order_date')
            ->get();

        // Recent completed deliveries
        $recent_deliveries = $delivery->orders()
            ->where('status', 'delivered')
            ->with(['customer.user'])
            ->latest()
            ->take(10)
            ->get();

        // Performance stats (last 30 days)
        $monthly_stats = [
            'deliveries_this_month' => $delivery->orders()
                ->where('status', 'delivered')
                ->where('delivery_date', '>=', now()->subDays(30))
                ->count(),
            
            'average_delivery_time' => null, // Could calculate if we had pickup/delivery timestamps
        ];

        return view('dashboards.delivery', compact('delivery', 'stats', 'active_deliveries', 'recent_deliveries', 'monthly_stats'));
    }
    
    private function genericDashboard($message = null)
    {
        $user = Auth::user();
        $errorMessage = $message ?? 'Your profile is not fully configured.';
        
        return view('dashboard', compact('user', 'errorMessage'));
    }
}
