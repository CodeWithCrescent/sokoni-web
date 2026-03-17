@extends('layouts.app')

@section('title', 'Vendors')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-blue-400">Vendors</h1>
            <p class="mt-1 text-sm text-gray-400">Manage vendor accounts and inventory</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('markets.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New Vendor
            </a>
        </div>
    </div>

    <!-- Vendors Grid -->
    @if(count($vendors) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($vendors as $vendor)
            <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6 hover:shadow-lg hover:shadow-blue-500/20 transition-all hover-glow">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 h-12 w-12 bg-purple-500/20 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">{{ $vendor->user->name }}</h3>
                            @if($vendor->business_name)
                                <p class="text-xs text-gray-400">{{ $vendor->business_name }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="space-y-2 mb-4">
                    <div class="flex items-center text-sm text-gray-300">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ $vendor->user->email }}
                    </div>
                    @if($vendor->user->phone_number)
                    <div class="flex items-center text-sm text-gray-300">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        {{ $vendor->user->phone_number }}
                    </div>
                    @endif
                    @if($vendor->address)
                    <div class="flex items-center text-sm text-gray-300">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="truncate">{{ $vendor->address }}</span>
                    </div>
                    @endif
                </div>
                
                <div class="flex items-center justify-between pt-4 border-t border-slate-700">
                    <div class="flex items-center space-x-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-500/20 text-blue-400 border border-blue-500/50">
                            {{ $vendor->products_count }} {{ Str::plural('product', $vendor->products_count) }}
                        </span>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('markets.show', $vendor) }}" class="p-1.5 text-gray-300 hover:text-blue-400 transition-colors" title="View Details">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        <a href="{{ route('markets.edit', $vendor) }}" class="p-1.5 text-gray-300 hover:text-blue-400 transition-colors" title="Edit Market">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                        @if($vendor->products_count === 0)
                        <form action="{{ route('markets.destroy', $vendor) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="if(confirm('Are you sure you want to delete this vendor?')) this.closest('form').submit();" class="p-1.5 text-gray-300 hover:text-red-500 transition-colors" title="Delete Vendor">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($vendors->hasPages())
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-4 flex flex-col sm:flex-row justify-between items-center">
            <div class="text-sm text-gray-400 mb-3 sm:mb-0">
                Showing <span class="font-medium text-blue-400">{{ $vendors->firstItem() }}</span> to 
                <span class="font-medium text-blue-400">{{ $vendors->lastItem() }}</span> of 
                <span class="font-medium text-blue-400">{{ $vendors->total() }}</span> vendors
            </div>
            
            <div class="flex space-x-1">
                {{ $vendors->links() }}
            </div>
        </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-blue-400">No vendors found</h3>
            <p class="mt-2 text-sm text-gray-300">Get started by adding your first vendor.</p>
            <div class="mt-6">
                <a href="{{ route('markets.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                    Add New Market
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
