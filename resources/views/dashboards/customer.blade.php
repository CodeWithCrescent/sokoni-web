@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div>
        <h1 class="text-2xl font-bold text-blue-400">My Dashboard</h1>
        <p class="mt-1 text-gray-400">Track your orders and manage your account</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Orders -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6 hover:shadow-lg hover:shadow-blue-500/10 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">Total Orders</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $stats['total_orders'] }}</p>
                </div>
                <div class="p-3 bg-blue-500/20 rounded-lg">
                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6 hover:shadow-lg hover:shadow-blue-500/10 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">Pending</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $stats['pending_orders'] }}</p>
                </div>
                <div class="p-3 bg-amber-500/20 rounded-lg">
                    <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- In Transit -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6 hover:shadow-lg hover:shadow-blue-500/10 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">In Transit</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $stats['processing_orders'] }}</p>
                </div>
                <div class="p-3 bg-purple-500/20 rounded-lg">
                    <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Spent -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6 hover:shadow-lg hover:shadow-blue-500/10 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">Total Spent</p>
                    <p class="mt-2 text-3xl font-bold text-white">${{ number_format($stats['total_spent'], 2) }}</p>
                </div>
                <div class="p-3 bg-green-500/20 rounded-lg">
                    <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700">
        <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-white">Recent Orders</h2>
            <a href="{{ route('orders.index') }}" class="text-sm text-blue-400 hover:text-blue-300">View all →</a>
        </div>
        <div class="p-6">
            @if($recent_orders->count() > 0)
                <div class="space-y-4">
                    @foreach($recent_orders as $order)
                    <div class="bg-slate-900/40 rounded-lg border border-slate-700 p-4 hover:border-blue-500/50 transition-colors">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <div class="flex items-center space-x-2">
                                    <h3 class="text-sm font-semibold text-white">Order #{{ $order->id }}</h3>
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-amber-500/20 text-amber-400 border-amber-500/50',
                                            'processing' => 'bg-blue-500/20 text-blue-400 border-blue-500/50',
                                            'delivered' => 'bg-green-500/20 text-green-400 border-green-500/50',
                                            'cancelled' => 'bg-red-500/20 text-red-400 border-red-500/50',
                                        ];
                                        $color = $statusColors[$order->status] ?? 'bg-gray-500/20 text-gray-400 border-gray-500/50';
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $color }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">{{ $order->order_date->format('M d, Y h:i A') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-blue-400">${{ number_format($order->total_amount, 2) }}</p>
                                <p class="text-xs text-gray-400">{{ $order->orderDetails->count() }} items</p>
                            </div>
                        </div>
                        
                        <div class="space-y-2 mb-3">
                            @foreach($order->orderDetails->take(2) as $detail)
                            <div class="flex items-center space-x-2 text-sm">
                                <span class="text-gray-400">•</span>
                                <span class="text-gray-300">{{ $detail->product->name }}</span>
                                <span class="text-gray-500">×{{ $detail->quantity }}</span>
                            </div>
                            @endforeach
                            @if($order->orderDetails->count() > 2)
                            <p class="text-xs text-gray-500">+ {{ $order->orderDetails->count() - 2 }} more items</p>
                            @endif
                        </div>

                        @if($order->deliveryPersonnel)
                        <div class="flex items-center space-x-2 text-sm text-gray-400 border-t border-slate-700 pt-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                            </svg>
                            <span>Delivery: {{ $order->deliveryPersonnel->user->name }}</span>
                        </div>
                        @endif

                        <div class="mt-3 flex space-x-2">
                            <a href="{{ route('orders.show', $order) }}" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500 transition text-sm font-medium text-center">
                                View Details
                            </a>
                            @if($order->status === 'pending')
                            <form action="{{ route('orders.destroy', $order) }}" method="POST" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Cancel this order?')" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-500 transition text-sm font-medium">
                                    Cancel Order
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-400">No orders yet</h3>
                    <p class="mt-2 text-sm text-gray-500">Start shopping to see your orders here</p>
                    <div class="mt-6">
                        <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500 transition">
                            Browse Products
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6">
        <h2 class="text-lg font-semibold text-white mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <a href="{{ route('orders.create') }}" class="flex flex-col items-center justify-center p-4 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition-colors group">
                <svg class="w-8 h-8 text-blue-400 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="text-sm text-gray-300 group-hover:text-white">New Order</span>
            </a>
            
            <a href="{{ route('orders.index') }}" class="flex flex-col items-center justify-center p-4 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition-colors group">
                <svg class="w-8 h-8 text-purple-400 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="text-sm text-gray-300 group-hover:text-white">Order History</span>
            </a>
            
            <a href="{{ route('customers.show', $customer) }}" class="flex flex-col items-center justify-center p-4 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition-colors group">
                <svg class="w-8 h-8 text-green-400 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-sm text-gray-300 group-hover:text-white">My Profile</span>
            </a>
        </div>
    </div>
</div>
@endsection
