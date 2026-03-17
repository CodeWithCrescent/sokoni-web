@extends('layouts.app')

@section('title', 'Delivery Personnel')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-blue-400">Delivery Personnel</h1>
            <p class="mt-1 text-sm text-gray-400">Manage delivery team and assignments</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('delivery-personnel.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Delivery Person
            </a>
        </div>
    </div>

    <!-- Delivery Personnel Grid -->
    @if(count($deliveryPersonnel) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($deliveryPersonnel as $personnel)
            <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6 hover:shadow-lg hover:shadow-blue-500/20 transition-all hover-glow">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 h-12 w-12 bg-purple-500/20 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">{{ $personnel->user->name }}</h3>
                            @php
                                $statusColors = [
                                    'available' => 'bg-green-500/20 text-green-400 border-green-500/50',
                                    'on_delivery' => 'bg-blue-500/20 text-blue-400 border-blue-500/50',
                                    'unavailable' => 'bg-gray-500/20 text-gray-400 border-gray-500/50',
                                ];
                                $color = $statusColors[$personnel->availability_status] ?? 'bg-gray-500/20 text-gray-400 border-gray-500/50';
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $color }} mt-1">
                                {{ ucfirst(str_replace('_', ' ', $personnel->availability_status)) }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-2 mb-4">
                    <div class="flex items-center text-sm text-gray-300">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ $personnel->user->email }}
                    </div>
                    @if($personnel->user->phone_number)
                    <div class="flex items-center text-sm text-gray-300">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        {{ $personnel->user->phone_number }}
                    </div>
                    @endif
                    @if($personnel->vehicle_type)
                    <div class="flex items-center text-sm text-gray-300">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                        </svg>
                        {{ $personnel->vehicle_type }}
                        @if($personnel->license_plate)
                            <span class="ml-2 text-gray-500">• {{ $personnel->license_plate }}</span>
                        @endif
                    </div>
                    @endif
                </div>
                
                <div class="flex items-center justify-between pt-4 border-t border-slate-700">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-500/20 text-blue-400 border border-blue-500/50">
                        {{ $personnel->orders_count }} {{ Str::plural('delivery', $personnel->orders_count) }}
                    </span>
                    <div class="flex space-x-2">
                        <a href="{{ route('delivery-personnel.show', $personnel) }}" class="p-1.5 text-gray-300 hover:text-blue-400 transition-colors" title="View Details">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        <a href="{{ route('delivery-personnel.edit', $personnel) }}" class="p-1.5 text-gray-300 hover:text-blue-400 transition-colors" title="Edit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($deliveryPersonnel->hasPages())
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-4 flex flex-col sm:flex-row justify-between items-center">
            <div class="text-sm text-gray-400 mb-3 sm:mb-0">
                Showing <span class="font-medium text-blue-400">{{ $deliveryPersonnel->firstItem() }}</span> to 
                <span class="font-medium text-blue-400">{{ $deliveryPersonnel->lastItem() }}</span> of 
                <span class="font-medium text-blue-400">{{ $deliveryPersonnel->total() }}</span> personnel
            </div>
            
            <div class="flex space-x-1">
                {{ $deliveryPersonnel->links() }}
            </div>
        </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-blue-400">No delivery personnel found</h3>
            <p class="mt-2 text-sm text-gray-300">Get started by adding your first delivery person.</p>
            <div class="mt-6">
                <a href="{{ route('delivery-personnel.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                    Add Delivery Person
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
