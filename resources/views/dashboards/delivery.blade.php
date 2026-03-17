@extends('layouts.app')

@section('title', 'Delivery Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-blue-400">Delivery Dashboard</h1>
            <p class="mt-1 text-gray-400">Manage your deliveries and availability</p>
        </div>
        <div>
            @php
                $statusColors = [
                    'available' => 'bg-green-500/20 text-green-400 border-green-500/50',
                    'on_delivery' => 'bg-blue-500/20 text-blue-400 border-blue-500/50',
                    'unavailable' => 'bg-gray-500/20 text-gray-400 border-gray-500/50',
                ];
                $color = $statusColors[$stats['availability_status']] ?? 'bg-gray-500/20 text-gray-400 border-gray-500/50';
            @endphp
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium border {{ $color }}">
                {{ ucfirst(str_replace('_', ' ', $stats['availability_status'])) }}
            </span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Deliveries -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6 hover:shadow-lg hover:shadow-blue-500/10 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">Total Deliveries</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $stats['total_deliveries'] }}</p>
                </div>
                <div class="p-3 bg-blue-500/20 rounded-lg">
                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Deliveries -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6 hover:shadow-lg hover:shadow-blue-500/10 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">Active Deliveries</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $stats['active_deliveries'] }}</p>
                </div>
                <div class="p-3 bg-amber-500/20 rounded-lg">
                    <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Completed -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6 hover:shadow-lg hover:shadow-blue-500/10 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">Completed</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $stats['completed_deliveries'] }}</p>
                </div>
                <div class="p-3 bg-green-500/20 rounded-lg">
                    <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- This Month -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6 hover:shadow-lg hover:shadow-blue-500/10 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">This Month</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ $monthly_stats['deliveries_this_month'] }}</p>
                </div>
                <div class="p-3 bg-purple-500/20 rounded-lg">
                    <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Active/Upcoming Deliveries -->
    @if($active_deliveries->count() > 0)
    <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700">
        <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-white">Active & Upcoming Deliveries</h2>
            <span class="text-sm text-amber-400">{{ $active_deliveries->count() }} pending</span>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($active_deliveries as $order)
                <div class="bg-slate-900/40 rounded-lg border border-slate-700 p-4 hover:border-blue-500/50 transition-colors">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <div class="flex items-center space-x-2">
                                <h3 class="text-sm font-semibold text-white">Order #{{ $order->id }}</h3>
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-amber-500/20 text-amber-400 border-amber-500/50',
                                        'processing' => 'bg-blue-500/20 text-blue-400 border-blue-500/50',
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
                    
                    <!-- Customer Info -->
                    <div class="bg-slate-800/40 rounded-lg p-3 mb-3">
                        <div class="flex items-start space-x-3">
                            <div class="p-2 bg-blue-500/20 rounded-lg">
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-white">{{ $order->customer->user->name }}</p>
                                @if($order->customer->user->phone_number)
                                <p class="text-xs text-gray-400">{{ $order->customer->user->phone_number }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="mt-2 flex items-start space-x-2 text-sm text-gray-300">
                            <svg class="w-4 h-4 mt-0.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>{{ $order->delivery_address }}</span>
                        </div>
                    </div>

                    <!-- Items Preview -->
                    <div class="space-y-1 mb-3">
                        @foreach($order->orderDetails->take(3) as $detail)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-300">{{ $detail->product->name }}</span>
                            <span class="text-gray-500">×{{ $detail->quantity }}</span>
                        </div>
                        @endforeach
                        @if($order->orderDetails->count() > 3)
                        <p class="text-xs text-gray-500">+ {{ $order->orderDetails->count() - 3 }} more items</p>
                        @endif
                    </div>

                    <div class="flex space-x-2">
                        <a href="{{ route('orders.show', $order) }}" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500 transition text-sm font-medium text-center">
                            View Details
                        </a>
                        @if($order->status === 'pending')
                        <form action="{{ route('orders.update-status', $order) }}" method="POST" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="processing">
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-500 transition text-sm font-medium">
                                Start Delivery
                            </button>
                        </form>
                        @elseif($order->status === 'processing')
                        <form action="{{ route('orders.update-status', $order) }}" method="POST" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="delivered">
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-500 transition text-sm font-medium">
                                Mark Delivered
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-12 text-center">
        <svg class="mx-auto h-16 w-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-400">No active deliveries</h3>
        <p class="mt-2 text-sm text-gray-500">You're all caught up! New deliveries will appear here.</p>
    </div>
    @endif

    <!-- Recent Completed Deliveries -->
    <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700">
        <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-white">Recent Completed Deliveries</h2>
            <a href="{{ route('orders.index') }}" class="text-sm text-blue-400 hover:text-blue-300">View all →</a>
        </div>
        <div class="p-6">
            @if($recent_deliveries->count() > 0)
                <div class="space-y-3">
                    @foreach($recent_deliveries as $order)
                    <div class="flex items-center justify-between py-3 border-b border-slate-700 last:border-0">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-green-500/20 rounded-lg">
                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-white">Order #{{ $order->id }}</p>
                                <p class="text-xs text-gray-400">{{ $order->customer->user->name }} • {{ $order->delivery_date ? $order->delivery_date->format('M d, Y') : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-green-400">${{ number_format($order->total_amount, 2) }}</p>
                            <p class="text-xs text-gray-400">Delivered</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 text-sm text-center py-4">No completed deliveries yet</p>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6">
        <h2 class="text-lg font-semibold text-white mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <a href="{{ route('orders.index') }}" class="flex flex-col items-center justify-center p-4 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition-colors group">
                <svg class="w-8 h-8 text-blue-400 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="text-sm text-gray-300 group-hover:text-white">All Orders</span>
            </a>
            
            <a href="{{ route('delivery-personnel.show', $delivery) }}" class="flex flex-col items-center justify-center p-4 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition-colors group">
                <svg class="w-8 h-8 text-purple-400 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-sm text-gray-300 group-hover:text-white">My Profile</span>
            </a>
            
            <a href="{{ route('delivery-personnel.edit', $delivery) }}" class="flex flex-col items-center justify-center p-4 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition-colors group">
                <svg class="w-8 h-8 text-green-400 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span class="text-sm text-gray-300 group-hover:text-white">Update Status</span>
            </a>
        </div>
    </div>
</div>
@endsection
