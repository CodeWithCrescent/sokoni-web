@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold text-blue-400">Order #{{ $order->id }}</h1>
                @php
                    $statusColors = [
                        'pending' => 'bg-amber-500/20 text-amber-400 border-amber-500/50',
                        'processing' => 'bg-blue-500/20 text-blue-400 border-blue-500/50',
                        'delivered' => 'bg-green-500/20 text-green-400 border-green-500/50',
                        'cancelled' => 'bg-red-500/20 text-red-400 border-red-500/50',
                    ];
                    $color = $statusColors[$order->status] ?? 'bg-gray-500/20 text-gray-400 border-gray-500/50';
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $color }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            <p class="mt-1 text-sm text-gray-400">Order placed on {{ $order->order_date->format('M d, Y \a\t h:i A') }}</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-700 border border-slate-600 rounded-md font-semibold text-xs text-gray-300 uppercase tracking-widest hover:bg-slate-600 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Orders
            </a>
            @if($order->status !== 'cancelled' && $order->status !== 'delivered')
            <a href="{{ route('orders.edit', $order) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Order
            </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Order Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Items -->
            <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700">
                <div class="px-6 py-4 border-b border-slate-700">
                    <h2 class="text-lg font-semibold text-white">Order Items</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($order->orderDetails as $detail)
                        <div class="flex items-center justify-between py-4 border-b border-slate-700 last:border-0">
                            <div class="flex items-center space-x-4 flex-1">
                                <div class="w-16 h-16 rounded-lg overflow-hidden bg-slate-900 flex-shrink-0">
                                    @if($detail->product->image)
                                        <img src="{{ $detail->product->image }}" alt="{{ $detail->product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-medium text-white truncate">{{ $detail->product->name }}</h3>
                                    <p class="text-xs text-gray-400 mt-1">{{ $detail->product->category->name ?? 'Uncategorized' }}</p>
                                    <div class="flex items-center mt-2 space-x-4 text-xs text-gray-500">
                                        <span>Qty: {{ $detail->quantity }}</span>
                                        <span>•</span>
                                        <span>${{ number_format($detail->price, 2) }} each</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right ml-4">
                                <p class="text-sm font-semibold text-blue-400">${{ number_format($detail->price * $detail->quantity, 2) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Order Summary -->
                    <div class="mt-6 pt-6 border-t border-slate-700 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Subtotal</span>
                            <span class="text-white">${{ number_format($order->total_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Delivery Fee</span>
                            <span class="text-white">$0.00</span>
                        </div>
                        <div class="flex justify-between text-lg font-semibold pt-2 border-t border-slate-700">
                            <span class="text-white">Total</span>
                            <span class="text-blue-400">${{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700">
                <div class="px-6 py-4 border-b border-slate-700">
                    <h2 class="text-lg font-semibold text-white">Order Timeline</h2>
                </div>
                <div class="p-6">
                    <div class="flow-root">
                        <ul class="-mb-8">
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-700" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-500/20 flex items-center justify-center ring-8 ring-slate-800">
                                                <svg class="h-4 w-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-white">Order placed</p>
                                                <p class="text-xs text-gray-500">Order created in the system</p>
                                            </div>
                                            <div class="text-right text-xs whitespace-nowrap text-gray-500">
                                                {{ $order->created_at->format('M d, h:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            
                            @if($order->status !== 'pending')
                            <li>
                                <div class="relative pb-8">
                                    @if($order->status !== 'processing')
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-700" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full {{ $order->status === 'cancelled' ? 'bg-red-500/20' : 'bg-blue-500/20' }} flex items-center justify-center ring-8 ring-slate-800">
                                                <svg class="h-4 w-4 {{ $order->status === 'cancelled' ? 'text-red-400' : 'text-blue-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-white">{{ ucfirst($order->status) }}</p>
                                                <p class="text-xs text-gray-500">Order status updated</p>
                                            </div>
                                            <div class="text-right text-xs whitespace-nowrap text-gray-500">
                                                {{ $order->updated_at->format('M d, h:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Customer Information -->
            <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700">
                <div class="px-6 py-4 border-b border-slate-700">
                    <h2 class="text-lg font-semibold text-white">Customer</h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 h-12 w-12 bg-blue-500/20 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white">{{ $order->customer->user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $order->customer->user->email }}</p>
                            @if($order->customer->user->phone)
                            <p class="text-xs text-gray-400">{{ $order->customer->user->phone }}</p>
                            @endif
                        </div>
                    </div>
                    @if($order->customer->address)
                    <div class="mt-4 pt-4 border-t border-slate-700">
                        <p class="text-xs text-gray-500 mb-1">Delivery Address</p>
                        <p class="text-sm text-gray-300">{{ $order->customer->address }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Delivery Personnel -->
            <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700">
                <div class="px-6 py-4 border-b border-slate-700">
                    <h2 class="text-lg font-semibold text-white">Delivery Personnel</h2>
                </div>
                <div class="p-6">
                    @if($order->deliveryPersonnel)
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 h-12 w-12 bg-purple-500/20 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white">{{ $order->deliveryPersonnel->user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $order->deliveryPersonnel->user->phone ?? 'No phone' }}</p>
                            @if($order->deliveryPersonnel->vehicle_type)
                            <p class="text-xs text-gray-500 mt-1">{{ $order->deliveryPersonnel->vehicle_type }}</p>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <svg class="mx-auto h-8 w-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                        </svg>
                        <p class="text-sm text-gray-400 mt-2">Not assigned yet</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            @if($order->status !== 'cancelled' && $order->status !== 'delivered')
            <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700">
                <div class="px-6 py-4 border-b border-slate-700">
                    <h2 class="text-lg font-semibold text-white">Quick Actions</h2>
                </div>
                <div class="p-6 space-y-2">
                    @if($order->status === 'pending')
                    <form action="{{ route('orders.update-status', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="processing">
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500 transition text-sm font-medium">
                            Mark as Processing
                        </button>
                    </form>
                    @endif
                    
                    @if($order->status === 'processing')
                    <form action="{{ route('orders.update-status', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="delivered">
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-500 transition text-sm font-medium">
                            Mark as Delivered
                        </button>
                    </form>
                    @endif
                    
                    @if($order->status === 'pending')
                    <form action="{{ route('orders.update-status', $order) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-500 transition text-sm font-medium">
                            Cancel Order
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
