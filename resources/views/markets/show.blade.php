@extends('layouts.app')

@section('title', 'Vendor Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-blue-400">{{ $vendor->user->name }}</h1>
            @if($vendor->business_name)
                <p class="mt-1 text-sm text-gray-400">{{ $vendor->business_name }}</p>
            @else
                <p class="mt-1 text-sm text-gray-400">Vendor ID: #{{ $vendor->id }}</p>
            @endif
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <a href="{{ route('markets.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-700 border border-slate-600 rounded-md font-semibold text-xs text-gray-300 uppercase tracking-widest hover:bg-slate-600 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Markets
            </a>
            <a href="{{ route('markets.edit', $vendor) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Vendor
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Vendor Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Contact Information -->
            <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700">
                <div class="px-6 py-4 border-b border-slate-700">
                    <h2 class="text-lg font-semibold text-white">Contact Information</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-400">Contact Person</dt>
                            <dd class="mt-1 text-sm text-white">{{ $vendor->user->name }}</dd>
                        </div>
                        @if($vendor->business_name)
                        <div>
                            <dt class="text-sm font-medium text-gray-400">Business Name</dt>
                            <dd class="mt-1 text-sm text-white">{{ $vendor->business_name }}</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-400">Email Address</dt>
                            <dd class="mt-1 text-sm text-white">{{ $vendor->user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-400">Phone Number</dt>
                            <dd class="mt-1 text-sm text-white">{{ $vendor->user->phone_number ?? 'Not provided' }}</dd>
                        </div>
                        @if($vendor->address)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-400">Business Address</dt>
                            <dd class="mt-1 text-sm text-white">{{ $vendor->address }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Products -->
            <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700">
                <div class="px-6 py-4 border-b border-slate-700">
                    <h2 class="text-lg font-semibold text-white">Products</h2>
                </div>
                <div class="p-6">
                    @if($vendor->products->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($vendor->products->take(6) as $product)
                            <div class="flex items-center space-x-3 p-3 bg-slate-900/40 rounded-lg border border-slate-700">
                                <div class="w-12 h-12 rounded-lg overflow-hidden bg-slate-800 flex-shrink-0">
                                    @if($product->image)
                                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-white truncate">{{ $product->name }}</p>
                                    <div class="flex items-center space-x-2 text-xs text-gray-400">
                                        <span>${{ number_format($product->price, 2) }}</span>
                                        <span>•</span>
                                        <span>Stock: {{ $product->stock }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @if($vendor->products->count() > 6)
                        <div class="mt-4 text-center">
                            <a href="{{ route('products.index', ['vendor_id' => $vendor->id]) }}" class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                                View all {{ $vendor->products->count() }} products →
                            </a>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-400">No products yet</p>
                            <div class="mt-4">
                                <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500 transition text-sm">
                                    Add Product
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Vendor Stats -->
            <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700">
                <div class="px-6 py-4 border-b border-slate-700">
                    <h2 class="text-lg font-semibold text-white">Statistics</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-400">Total Products</span>
                        <span class="text-lg font-semibold text-blue-400">{{ $vendor->products->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-400">Active Products</span>
                        <span class="text-lg font-semibold text-green-400">{{ $vendor->products->where('stock', '>', 0)->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-400">Total Inventory Value</span>
                        <span class="text-lg font-semibold text-purple-400">
                            ${{ number_format($vendor->products->sum(function($p) { return $p->price * $p->stock; }), 2) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-400">Member Since</span>
                        <span class="text-sm text-gray-300">{{ $vendor->created_at->format('M Y') }}</span>
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
                            Vendor since {{ $vendor->created_at->diffForHumans() }}
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
                    <a href="{{ route('products.create') }}" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500 transition text-sm font-medium flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add New Product
                    </a>
                    
                    @if($vendor->products->count() === 0)
                    <form action="{{ route('markets.destroy', $vendor) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this market?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-500 transition text-sm font-medium">
                            Delete Vendor
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
