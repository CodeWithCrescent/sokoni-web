@extends('layouts.app')

@section('title', 'Vendor Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div>
        <h1 class="text-2xl font-bold text-blue-400">Vendor Dashboard</h1>
        <p class="mt-1 text-gray-400">{{ $vendor->business_name ?? 'Your business' }} performance overview</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Sales -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6 hover:shadow-lg hover:shadow-blue-500/10 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">Total Sales</p>
                    <p class="mt-2 text-3xl font-bold text-white">${{ number_format($stats['total_sales'], 2) }}</p>
                    <p class="mt-1 text-xs text-green-400">Completed orders</p>
                </div>
                <div class="p-3 bg-green-500/20 rounded-lg">
                    <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Products -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6 hover:shadow-lg hover:shadow-blue-500/10 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">Total Products</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $stats['total_products'] }}</p>
                    <p class="mt-1 text-xs text-blue-400">{{ $stats['active_products'] }} in stock</p>
                </div>
                <div class="p-3 bg-blue-500/20 rounded-lg">
                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Inventory Value -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6 hover:shadow-lg hover:shadow-blue-500/10 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">Inventory Value</p>
                    <p class="mt-2 text-3xl font-bold text-white">${{ number_format($stats['total_inventory_value'], 2) }}</p>
                    <p class="mt-1 text-xs text-purple-400">Current stock</p>
                </div>
                <div class="p-3 bg-purple-500/20 rounded-lg">
                    <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6 hover:shadow-lg hover:shadow-blue-500/10 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">Low Stock</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $stats['low_stock'] }}</p>
                    <p class="mt-1 text-xs text-red-400">{{ $stats['out_of_stock'] }} out of stock</p>
                </div>
                <div class="p-3 bg-red-500/20 rounded-lg">
                    <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Selling Products -->
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
                                    <p class="text-xs text-gray-400">Stock: {{ $product->stock }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-blue-400">{{ $product->order_details_count }}</p>
                                <p class="text-xs text-gray-400">sold</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400 text-sm text-center py-4">No sales data yet</p>
                @endif
            </div>
        </div>

        <!-- Low Stock Products -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700">
            <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">Low Stock Alert</h2>
                <span class="text-sm text-red-400">{{ $low_stock_products->count() }} items</span>
            </div>
            <div class="p-6">
                @if($low_stock_products->count() > 0)
                    <div class="space-y-3">
                        @foreach($low_stock_products->take(5) as $product)
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
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-500/20 text-red-400 border border-red-500/50">
                                    {{ $product->stock }} left
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-400">All products well stocked!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6">
        <h2 class="text-lg font-semibold text-white mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('products.create') }}" class="flex flex-col items-center justify-center p-4 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition-colors group">
                <svg class="w-8 h-8 text-blue-400 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="text-sm text-gray-300 group-hover:text-white">Add Product</span>
            </a>
            
            <a href="{{ route('products.index') }}" class="flex flex-col items-center justify-center p-4 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition-colors group">
                <svg class="w-8 h-8 text-purple-400 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span class="text-sm text-gray-300 group-hover:text-white">Manage Inventory</span>
            </a>
            
            <a href="{{ route('orders.index') }}" class="flex flex-col items-center justify-center p-4 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition-colors group">
                <svg class="w-8 h-8 text-green-400 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="text-sm text-gray-300 group-hover:text-white">View Orders</span>
            </a>
            
            <a href="{{ route('vendors.show', $vendor) }}" class="flex flex-col items-center justify-center p-4 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition-colors group">
                <svg class="w-8 h-8 text-amber-400 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span class="text-sm text-gray-300 group-hover:text-white">Business Profile</span>
            </a>
        </div>
    </div>
</div>
@endsection
