@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $userRole = Auth::user()->role->name ?? null;
@endphp

<div class="space-y-6">
    <!-- Welcome Header -->
    <div>
        <h1 class="text-2xl font-bold text-white">Welcome back, {{ Auth::user()->name }}!</h1>
        <p class="mt-1 text-gray-400">
            @if(isset($errorMessage))
                {{ $errorMessage }}
            @else
                Here's your dashboard overview.
            @endif
        </p>
    </div>
    
    @if(isset($errorMessage))
    <!-- Profile Setup Alert -->
    <div class="bg-amber-500/20 border border-amber-500/50 rounded-lg p-6">
        <div class="flex items-start space-x-3">
            <svg class="w-6 h-6 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <h3 class="text-lg font-semibold text-amber-400">Profile Setup Required</h3>
                <p class="mt-1 text-sm text-gray-300">{{ $errorMessage }}</p>
                <p class="mt-2 text-sm text-gray-400">An administrator needs to complete your profile setup before you can access all features.</p>
            </div>
        </div>
    </div>
    
    @else
    <!-- Welcome Message for users with profiles -->
    <div class="bg-blue-500/10 border border-blue-500/50 rounded-lg p-6">
        <div class="flex items-start space-x-3">
            <svg class="w-6 h-6 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <h3 class="text-lg font-semibold text-blue-400">Dashboard Access</h3>
                <p class="mt-1 text-sm text-gray-300">
                    You're logged in as <strong>{{ ucfirst($userRole) }}</strong>. 
                    @if($userRole === 'admin')
                        You have full system access.
                    @else
                        Your dashboard features are loading.
                    @endif
                </p>
            </div>
        </div>
    </div>
    @endif

    @if($userRole === 'admin')
    <!-- Stats Grid for Admin Only -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Products -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6 hover:shadow-lg hover:shadow-blue-500/10 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">Total Products</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ \App\Models\Product::count() }}</p>
                </div>
                <div class="p-3 bg-blue-500/20 rounded-lg">
                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('products.index') }}" class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                    View all products →
                </a>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6 hover:shadow-lg hover:shadow-blue-500/10 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">Total Orders</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ \App\Models\Order::count() }}</p>
                </div>
                <div class="p-3 bg-purple-500/20 rounded-lg">
                    <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('orders.index') }}" class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                    View all orders →
                </a>
            </div>
        </div>

        <!-- Total Customers -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6 hover:shadow-lg hover:shadow-blue-500/10 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">Customers</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ \App\Models\Customer::count() }}</p>
                </div>
                <div class="p-3 bg-green-500/20 rounded-lg">
                    <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('customers.index') }}" class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                    View customers →
                </a>
            </div>
        </div>

        <!-- Categories -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6 hover:shadow-lg hover:shadow-blue-500/10 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400">Categories</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ \App\Models\Category::count() }}</p>
                </div>
                <div class="p-3 bg-amber-500/20 rounded-lg">
                    <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('categories.index') }}" class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                    Manage categories →
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Actions for Admin -->
    <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6">
        <h2 class="text-lg font-semibold text-white mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('products.create') }}" class="flex flex-col items-center justify-center p-4 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition-colors group">
                <svg class="w-8 h-8 text-blue-400 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="text-sm text-gray-300 group-hover:text-white">Add Product</span>
            </a>
            
            <a href="{{ route('categories.create') }}" class="flex flex-col items-center justify-center p-4 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition-colors group">
                <svg class="w-8 h-8 text-amber-400 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <span class="text-sm text-gray-300 group-hover:text-white">Add Category</span>
            </a>
            
            <a href="{{ route('orders.index') }}" class="flex flex-col items-center justify-center p-4 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition-colors group">
                <svg class="w-8 h-8 text-purple-400 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="text-sm text-gray-300 group-hover:text-white">View Orders</span>
            </a>
            
            <a href="{{ route('customers.index') }}" class="flex flex-col items-center justify-center p-4 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition-colors group">
                <svg class="w-8 h-8 text-green-400 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="text-sm text-gray-300 group-hover:text-white">Customers</span>
            </a>
        </div>
    </div>

    <!-- Recent Activity (Placeholder) -->
    <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6">
        <h2 class="text-lg font-semibold text-white mb-4">Recent Activity</h2>
        <div class="space-y-3">
            @forelse(\App\Models\Order::latest()->take(5)->get() as $order)
            <div class="flex items-center justify-between py-3 border-b border-slate-700 last:border-0">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-500/20 rounded-lg">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-white">Order #{{ $order->id }}</p>
                        <p class="text-xs text-gray-400">{{ $order->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-blue-400">${{ number_format($order->total_amount, 2) }}</p>
                    <p class="text-xs text-gray-400">{{ ucfirst($order->status) }}</p>
                </div>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-4">No recent orders</p>
            @endforelse
        </div>
    </div>
    @endif

    @if(in_array($userRole, ['vendor', 'customer', 'delivery']) && !isset($errorMessage))
    <!-- Role-specific placeholder (shouldn't normally see this) -->
    <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-8 text-center">
        <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h3 class="text-xl font-semibold text-white mb-2">Dashboard Ready</h3>
        <p class="text-gray-400">Your {{ ucfirst($userRole) }} dashboard is being prepared.</p>
    </div>
    @endif
</div>
@endsection