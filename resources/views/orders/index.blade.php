@extends('layouts.app')

@section('title', 'Orders Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-blue-400">Orders Management</h1>
            <p class="mt-1 text-sm text-gray-400">View and manage all orders</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('orders.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Order
            </a>
        </div>
    </div>

    <!-- Status Filter Tabs -->
    <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-4">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('orders.index') }}" class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-slate-700/50 text-gray-300 hover:bg-slate-700' }}">
                All Orders
            </a>
            <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request('status') === 'pending' ? 'bg-amber-600 text-white' : 'bg-slate-700/50 text-gray-300 hover:bg-slate-700' }}">
                Pending
            </a>
            <a href="{{ route('orders.index', ['status' => 'processing']) }}" class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request('status') === 'processing' ? 'bg-blue-600 text-white' : 'bg-slate-700/50 text-gray-300 hover:bg-slate-700' }}">
                Processing
            </a>
            <a href="{{ route('orders.index', ['status' => 'delivered']) }}" class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request('status') === 'delivered' ? 'bg-green-600 text-white' : 'bg-slate-700/50 text-gray-300 hover:bg-slate-700' }}">
                Delivered
            </a>
            <a href="{{ route('orders.index', ['status' => 'cancelled']) }}" class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request('status') === 'cancelled' ? 'bg-red-600 text-white' : 'bg-slate-700/50 text-gray-300 hover:bg-slate-700' }}">
                Cancelled
            </a>
        </div>
    </div>

    <!-- Orders Table -->
    @if(count($orders) > 0)
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-700">
                    <thead class="bg-slate-900/60">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Order ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Customer
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Date
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Delivery
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Total
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700">
                        @foreach($orders as $order)
                        <tr class="hover:bg-slate-800/60 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-blue-400">#{{ $order->id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 bg-blue-500/20 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-white">{{ $order->customer->user->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-400">{{ $order->customer->user->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-300">{{ $order->order_date->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $order->order_date->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-amber-500/20 text-amber-400 border-amber-500/50',
                                        'processing' => 'bg-blue-500/20 text-blue-400 border-blue-500/50',
                                        'delivered' => 'bg-green-500/20 text-green-400 border-green-500/50',
                                        'cancelled' => 'bg-red-500/20 text-red-400 border-red-500/50',
                                    ];
                                    $color = $statusColors[$order->status] ?? 'bg-gray-500/20 text-gray-400 border-gray-500/50';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $color }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($order->deliveryPersonnel)
                                    <div class="text-sm text-gray-300">{{ $order->deliveryPersonnel->user->name }}</div>
                                @else
                                    <span class="text-xs text-gray-500">Not assigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-blue-400">${{ number_format($order->total_amount, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('orders.show', $order) }}" class="p-1.5 text-gray-300 hover:text-blue-400 transition-colors" title="View Details">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('orders.edit', $order) }}" class="p-1.5 text-gray-300 hover:text-blue-400 transition-colors" title="Edit Order">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    
                                    @if($order->status !== 'cancelled')
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="p-1.5 text-gray-300 hover:text-blue-400 transition-colors" title="Update Status">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                        </button>
                                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-slate-800 rounded-lg shadow-lg py-1 border border-slate-700 z-10">
                                            @foreach(['pending', 'processing', 'delivered', 'cancelled'] as $status)
                                                @if($status !== $order->status)
                                                <form action="{{ route('orders.update-status', $order) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="{{ $status }}">
                                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-slate-700 hover:text-white">
                                                        Mark as {{ ucfirst($status) }}
                                                    </button>
                                                </form>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-4 flex flex-col sm:flex-row justify-between items-center">
            <div class="text-sm text-gray-400 mb-3 sm:mb-0">
                Showing <span class="font-medium text-blue-400">{{ $orders->firstItem() }}</span> to 
                <span class="font-medium text-blue-400">{{ $orders->lastItem() }}</span> of 
                <span class="font-medium text-blue-400">{{ $orders->total() }}</span> orders
            </div>
            
            <div class="flex space-x-1">
                {{ $orders->links() }}
            </div>
        </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-blue-400">No orders found</h3>
            <p class="mt-2 text-sm text-gray-300">{{ request('status') ? 'No ' . request('status') . ' orders at the moment.' : 'Get started by creating a new order.' }}</p>
            <div class="mt-6">
                <a href="{{ route('orders.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                    Create New Order
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
