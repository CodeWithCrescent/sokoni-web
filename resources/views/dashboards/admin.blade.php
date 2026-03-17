@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div>
        <h1 class="text-2xl font-bold text-blue-400">Admin Dashboard</h1>
        <p class="mt-1 text-gray-400">Complete overview of your delivery system</p>
    </div>

    <!-- Main Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Revenue -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6 hover:shadow-lg hover:shadow-blue-500/10 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">Total Revenue</p>
                    <p class="mt-2 text-3xl font-bold text-white">${{ number_format($stats['total_revenue'], 2) }}</p>
                    <p class="mt-1 text-xs text-green-400">Delivered orders</p>
                </div>
                <div class="p-3 bg-green-500/20 rounded-lg">
                    <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6 hover:shadow-lg hover:shadow-blue-500/10 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">Total Orders</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $stats['total_orders'] }}</p>
                    <p class="mt-1 text-xs text-amber-400">{{ $stats['pending_orders'] }} pending</p>
                </div>
                <div class="p-3 bg-purple-500/20 rounded-lg">
                    <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Customers -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6 hover:shadow-lg hover:shadow-blue-500/10 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">Customers</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $stats['total_customers'] }}</p>
                    <p class="mt-1 text-xs text-blue-400">Active users</p>
                </div>
                <div class="p-3 bg-blue-500/20 rounded-lg">
                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Products -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6 hover:shadow-lg hover:shadow-blue-500/10 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">Products</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $stats['total_products'] }}</p>
                    <p class="mt-1 text-xs text-red-400">{{ $stats['low_stock_products'] }} low stock</p>
                </div>
                <div class="p-3 bg-amber-500/20 rounded-lg">
                    <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Status Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-400">Pending</p>
                    <p class="mt-1 text-2xl font-bold text-amber-400">{{ $stats['pending_orders'] }}</p>
                </div>
                <div class="p-2 bg-amber-500/20 rounded-lg">
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-400">Processing</p>
                    <p class="mt-1 text-2xl font-bold text-blue-400">{{ $stats['processing_orders'] }}</p>
                </div>
                <div class="p-2 bg-blue-500/20 rounded-lg">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-400">Delivered</p>
                    <p class="mt-1 text-2xl font-bold text-green-400">{{ $stats['delivered_orders'] }}</p>
                </div>
                <div class="p-2 bg-green-500/20 rounded-lg">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-400">Cancelled</p>
                    <p class="mt-1 text-2xl font-bold text-red-400">{{ $stats['cancelled_orders'] }}</p>
                </div>
                <div class="p-2 bg-red-500/20 rounded-lg">
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700">
            <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">Recent Orders</h2>
                <a href="{{ route('orders.index') }}" class="text-sm text-blue-400 hover:text-blue-300">View all →</a>
            </div>
            <div class="p-6">
                @if($recent_orders->count() > 0)
                    <div class="space-y-3">
                        @foreach($recent_orders as $order)
                        <div class="flex items-center justify-between py-3 border-b border-slate-700 last:border-0">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-blue-500/20 rounded-lg">
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-white">Order #{{ $order->id }}</p>
                                    <p class="text-xs text-gray-400">{{ $order->customer->user->name }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-blue-400">${{ number_format($order->total_amount, 2) }}</p>
                                @php
                                    $statusColors = [
                                        'pending' => 'text-amber-400',
                                        'processing' => 'text-blue-400',
                                        'delivered' => 'text-green-400',
                                        'cancelled' => 'text-red-400',
                                    ];
                                    $color = $statusColors[$order->status] ?? 'text-gray-400';
                                @endphp
                                <p class="text-xs {{ $color }}">{{ ucfirst($order->status) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400 text-sm text-center py-4">No orders yet</p>
                @endif
            </div>
        </div>

        <!-- Top Products -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700">
            <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">Top Selling Products</h2>
                <a href="{{ route('products.index') }}" class="text-sm text-blue-400 hover:text-blue-300">View all →</a>
            </div>
            <div class="p-6">
                @if($top_products->count() > 0)
                    <div class="space-y-3">
                        @foreach($top_products as $product)
                        <div class="flex items-center justify-between py-3 border-b border-slate-700 last:border-0">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg overflow-hidden bg-slate-700 flex-shrink-0">
                                    @if($product->image)
                                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-white truncate">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-400">${{ number_format($product->price, 2) }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-blue-400">{{ $product->order_details_count }}</p>
                                <p class="text-xs text-gray-400">orders</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400 text-sm text-center py-4">No sales data yet</p>
                @endif
            </div>
        </div>
    </div>

    <!-- System Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-400">Vendors</h3>
                <a href="{{ route('markets.index') }}" class="text-blue-400 hover:text-blue-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['total_vendors'] }}</p>
            <p class="mt-1 text-xs text-gray-400">Active suppliers</p>
        </div>

        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-400">Delivery Team</h3>
                <a href="{{ route('delivery-personnel.index') }}" class="text-blue-400 hover:text-blue-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['total_delivery_personnel'] }}</p>
            <p class="mt-1 text-xs text-gray-400">Delivery staff</p>
        </div>

        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-400">Categories</h3>
                <a href="{{ route('categories.index') }}" class="text-blue-400 hover:text-blue-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['total_categories'] }}</p>
            <p class="mt-1 text-xs text-gray-400">Product categories</p>
        </div>
    </div>
</div>
@endsection
