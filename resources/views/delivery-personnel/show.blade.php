@extends('layouts.app')

@section('title', 'Delivery Person Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-blue-400">{{ $deliveryPersonnel->user->name }}</h1>
            <p class="mt-1 text-sm text-gray-400">Delivery Personnel ID: #{{ $deliveryPersonnel->id }}</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <a href="{{ route('delivery-personnel.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-700 border border-slate-600 rounded-md font-semibold text-xs text-gray-300 uppercase tracking-widest hover:bg-slate-600 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </a>
            <a href="{{ route('delivery-personnel.edit', $deliveryPersonnel) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Profile
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Contact Information -->
            <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700">
                <div class="px-6 py-4 border-b border-slate-700">
                    <h2 class="text-lg font-semibold text-white">Contact Information</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-400">Full Name</dt>
                            <dd class="mt-1 text-sm text-white">{{ $deliveryPersonnel->user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-400">Email Address</dt>
                            <dd class="mt-1 text-sm text-white">{{ $deliveryPersonnel->user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-400">Phone Number</dt>
                            <dd class="mt-1 text-sm text-white">{{ $deliveryPersonnel->user->phone_number ?? 'Not provided' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-400">Availability Status</dt>
                            <dd class="mt-1">
                                @php
                                    $statusColors = [
                                        'available' => 'bg-green-500/20 text-green-400 border-green-500/50',
                                        'on_delivery' => 'bg-blue-500/20 text-blue-400 border-blue-500/50',
                                        'unavailable' => 'bg-gray-500/20 text-gray-400 border-gray-500/50',
                                    ];
                                    $color = $statusColors[$deliveryPersonnel->availability_status] ?? 'bg-gray-500/20 text-gray-400 border-gray-500/50';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $color }}">
                                    {{ ucfirst(str_replace('_', ' ', $deliveryPersonnel->availability_status)) }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Vehicle Information -->
            <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700">
                <div class="px-6 py-4 border-b border-slate-700">
                    <h2 class="text-lg font-semibold text-white">Vehicle Information</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-400">Vehicle Type</dt>
                            <dd class="mt-1 text-sm text-white">{{ $deliveryPersonnel->vehicle_type ?? 'Not specified' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-400">License Plate</dt>
                            <dd class="mt-1 text-sm text-white">{{ $deliveryPersonnel->license_plate ?? 'Not provided' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Delivery History -->
            <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700">
                <div class="px-6 py-4 border-b border-slate-700">
                    <h2 class="text-lg font-semibold text-white">Recent Deliveries</h2>
                </div>
                <div class="p-6">
                    @if($deliveryPersonnel->orders->count() > 0)
                        <div class="space-y-4">
                            @foreach($deliveryPersonnel->orders->take(5) as $order)
                            <div class="flex items-center justify-between py-3 border-b border-slate-700 last:border-0">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-blue-500/20 rounded-lg">
                                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-white">Order #{{ $order->id }}</p>
                                        <p class="text-xs text-gray-400">{{ $order->customer->user->name }} • {{ $order->order_date->format('M d, Y') }}</p>
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
                        @if($deliveryPersonnel->orders->count() > 5)
                        <div class="mt-4 text-center">
                            <a href="{{ route('orders.index', ['delivery_id' => $deliveryPersonnel->id]) }}" class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                                View all {{ $deliveryPersonnel->orders->count() }} deliveries →
                            </a>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-400">No deliveries yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Statistics -->
            <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700">
                <div class="px-6 py-4 border-b border-slate-700">
                    <h2 class="text-lg font-semibold text-white">Statistics</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-400">Total Deliveries</span>
                        <span class="text-lg font-semibold text-blue-400">{{ $deliveryPersonnel->orders->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-400">Completed</span>
                        <span class="text-lg font-semibold text-green-400">{{ $deliveryPersonnel->orders->where('status', 'delivered')->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-400">In Progress</span>
                        <span class="text-lg font-semibold text-amber-400">{{ $deliveryPersonnel->orders->whereIn('status', ['pending', 'processing'])->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-400">Member Since</span>
                        <span class="text-sm text-gray-300">{{ $deliveryPersonnel->created_at->format('M Y') }}</span>
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
                            Joined {{ $deliveryPersonnel->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            @if($deliveryPersonnel->orders()->whereIn('status', ['pending', 'processing'])->count() === 0)
            <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700">
                <div class="px-6 py-4 border-b border-slate-700">
                    <h2 class="text-lg font-semibold text-white">Quick Actions</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('delivery-personnel.destroy', $deliveryPersonnel) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this delivery person?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-500 transition text-sm font-medium">
                            Delete Personnel
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
