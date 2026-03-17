@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-blue-400">{{ $customer->user->name }}</h1>
            <p class="mt-1 text-sm text-gray-400">Customer ID: #{{ $customer->id }}</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <a href="{{ route('customers.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-700 border border-slate-600 rounded-md font-semibold text-xs text-gray-300 uppercase tracking-widest hover:bg-slate-600 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Customers
            </a>
            <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Customer
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Customer Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Contact Information -->
            <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700">
                <div class="px-6 py-4 border-b border-slate-700">
                    <h2 class="text-lg font-semibold text-white">Contact Information</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-400">Email Address</dt>
                            <dd class="mt-1 text-sm text-white">{{ $customer->user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-400">Phone Number</dt>
                            <dd class="mt-1 text-sm text-white">{{ $customer->phone_number ?? 'Not provided' }}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-400">Address</dt>
                            <dd class="mt-1 text-sm text-white">
                                @if($customer->address)
                                    {{ $customer->address }}<br>
                                    @if($customer->city || $customer->state || $customer->zip_code)
                                        {{ $customer->city }}{{ $customer->city && ($customer->state || $customer->zip_code) ? ', ' : '' }}
                                        {{ $customer->state }} {{ $customer->zip_code }}
                                    @endif
                                @else
                                    Not provided
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Order History -->
            <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700">
                <div class="px-6 py-4 border-b border-slate-700">
                    <h2 class="text-lg font-semibold text-white">Order History</h2>
                </div>
                <div class="p-6">
                    @if($customer->orders->count() > 0)
                        <div class="space-y-4">
                            @foreach($customer->orders->take(5) as $order)
                            <div class="flex items-center justify-between py-3 border-b border-slate-700 last:border-0">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-blue-500/20 rounded-lg">
                                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-white">Order #{{ $order->id }}</p>
                                        <p class="text-xs text-gray-400">{{ $order->order_date->format('M d, Y') }}</p>
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
                        @if($customer->orders->count() > 5)
                        <div class="mt-4 text-center">
                            <a href="{{ route('orders.index', ['customer_id' => $customer->id]) }}" class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                                View all {{ $customer->orders->count() }} orders →
                            </a>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-400">No orders yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Customer Stats -->
            <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700">
                <div class="px-6 py-4 border-b border-slate-700">
                    <h2 class="text-lg font-semibold text-white">Statistics</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-400">Total Orders</span>
                        <span class="text-lg font-semibold text-blue-400">{{ $customer->orders->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-400">Total Spent</span>
                        <span class="text-lg font-semibold text-green-400">${{ number_format($customer->orders->sum('total_amount'), 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-400">Member Since</span>
                        <span class="text-sm text-gray-300">{{ $customer->created_at->format('M Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-400">Last Order</span>
                        <span class="text-sm text-gray-300">
                            @if($customer->orders->count() > 0)
                                {{ $customer->orders->first()->order_date->diffForHumans() }}
                            @else
                                Never
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Account Status -->
            <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700">
                <div class="px-6 py-4 border-b border-slate-700">
                    <h2 class="text-lg font-semibold text-white">Account Status</h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 bg-green-500/20 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white">Active</p>
                            <p class="text-xs text-gray-400">Account in good standing</p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-700">
                        <p class="text-xs text-gray-500">
                            Account created {{ $customer->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700">
                <div class="px-6 py-4 border-b border-slate-700">
                    <h2 class="text-lg font-semibold text-white">Quick Actions</h2>
                </div>
                <div class="p-6 space-y-2">
                    <a href="{{ route('orders.create', ['customer_id' => $customer->id]) }}" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500 transition text-sm font-medium flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create New Order
                    </a>
                    
                    @if($customer->orders->count() === 0)
                    <form action="{{ route('customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this customer?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-500 transition text-sm font-medium">
                            Delete Customer
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
