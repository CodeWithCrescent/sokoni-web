@extends('layouts.app')

@section('title', 'All Products')

@section('content')

<div class="space-y-6">
    <!-- Header with action buttons -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <h1 class="text-2xl font-bold text-lime-400">Products Management</h1>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-lime-500 border border-transparent rounded-md font-semibold text-xs text-green-950 uppercase tracking-widest hover:bg-lime-400 focus:bg-lime-400 active:bg-lime-600 focus:outline-none focus:ring-2 focus:ring-lime-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New Product
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="bg-green-900/40 backdrop-blur-sm rounded-lg shadow-lg border border-green-800 p-4">
        <div class="w-full">
            <button onclick="toggleFilters()" class="flex items-center justify-between w-full text-left text-lime-400 font-medium">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filters & Search
                </span>
                <svg id="filter-arrow" class="w-5 h-5 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>

            <div id="filters-container" class="hidden mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <form method="GET" action="{{ route('products.index') }}" id="filter-form">
                    <!-- Search Input -->
                    <div class="space-y-2">
                        <label for="search" class="block text-sm font-medium text-gray-300">Search</label>
                        <div class="relative rounded-md shadow-sm">
                            <input 
                                type="text" 
                                name="search" 
                                id="search" 
                                value="{{ request('search') }}"
                                class="block w-full rounded-md border-0 py-2 pl-3 pr-10 text-gray-300 bg-green-950/60 placeholder:text-gray-500 focus:ring-2 focus:ring-lime-500 focus:border-lime-500 sm:text-sm" 
                                placeholder="Product name or description..."
                            >
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div class="space-y-2">
                        <label for="category" class="block text-sm font-medium text-gray-300">Category</label>
                        <select name="category_id" id="category" class="block w-full rounded-md border-0 py-2 text-gray-300 bg-green-950/60 focus:ring-2 focus:ring-lime-500 focus:border-lime-500 sm:text-sm">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Vendor Filter -->
                    <div class="space-y-2">
                        <label for="vendor" class="block text-sm font-medium text-gray-300">Vendor</label>
                        <select name="vendor_id" id="vendor" class="block w-full rounded-md border-0 py-2 text-gray-300 bg-green-950/60 focus:ring-2 focus:ring-lime-500 focus:border-lime-500 sm:text-sm">
                            <option value="">All Vendors</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Price Range Filter -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-300">Price Range</label>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <input 
                                    type="number" 
                                    name="min_price" 
                                    value="{{ request('min_price') }}" 
                                    min="0" 
                                    placeholder="Min" 
                                    class="block w-full rounded-md border-0 py-2 text-gray-300 bg-green-950/60 placeholder:text-gray-500 focus:ring-2 focus:ring-lime-500 focus:border-lime-500 sm:text-sm"
                                >
                            </div>
                            <div>
                                <input 
                                    type="number" 
                                    name="max_price" 
                                    value="{{ request('max_price') }}" 
                                    min="0" 
                                    placeholder="Max" 
                                    class="block w-full rounded-md border-0 py-2 text-gray-300 bg-green-950/60 placeholder:text-gray-500 focus:ring-2 focus:ring-lime-500 focus:border-lime-500 sm:text-sm"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Sort Options -->
                    <div class="space-y-2">
                        <label for="sort_by" class="block text-sm font-medium text-gray-300">Sort By</label>
                        <select name="sort_by" id="sort_by" class="block w-full rounded-md border-0 py-2 text-gray-300 bg-green-950/60 focus:ring-2 focus:ring-lime-500 focus:border-lime-500 sm:text-sm">
                            <option value="created_at" {{ request('sort_by', 'created_at') == 'created_at' ? 'selected' : '' }}>Date Added</option>
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                            <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Price</option>
                        </select>
                    </div>

                    <!-- Sort Direction -->
                    <div class="space-y-2">
                        <label for="sort_dir" class="block text-sm font-medium text-gray-300">Sort Direction</label>
                        <select name="sort_dir" id="sort_dir" class="block w-full rounded-md border-0 py-2 text-gray-300 bg-green-950/60 focus:ring-2 focus:ring-lime-500 focus:border-lime-500 sm:text-sm">
                            <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>Ascending</option>
                            <option value="desc" {{ request('sort_dir', 'desc') == 'desc' ? 'selected' : '' }}>Descending</option>
                        </select>
                    </div>

                    <!-- Per Page -->
                    <div class="space-y-2">
                        <label for="per_page" class="block text-sm font-medium text-gray-300">Items Per Page</label>
                        <select name="per_page" id="per_page" class="block w-full rounded-md border-0 py-2 text-gray-300 bg-green-950/60 focus:ring-2 focus:ring-lime-500 focus:border-lime-500 sm:text-sm">
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                            <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>

                    <!-- Filter Actions -->
                    <div class="flex items-end space-x-3 col-span-1 md:col-span-2 lg:col-span-4">
                        <button type="submit" class="flex-1 px-4 py-2 bg-lime-600 text-white rounded-md hover:bg-lime-500 focus:outline-none focus:ring-2 focus:ring-lime-500 focus:ring-offset-2 transition-colors">
                            Apply Filters
                        </button>
                        <a href="{{ route('products.index') }}" class="flex-1 px-4 py-2 bg-green-800 text-center text-gray-300 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                            Reset Filters
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Active Filters Summary -->
    @if(request('search') || request('category_id') || request('vendor_id') || request('min_price') || request('max_price'))
        <div class="bg-green-800/40 backdrop-blur-sm rounded-lg p-3 flex flex-wrap items-center gap-2">
            <span class="text-sm font-medium text-gray-300">Active filters:</span>
            
            @if(request('search'))
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-900 text-gray-300">
                    Search: <span class="ml-1 text-lime-400">{{ request('search') }}</span>
                    <a href="{{ request()->url() . '?' . http_build_query(request()->except('search')) }}" class="ml-1 text-gray-400 hover:text-white">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </span>
            @endif
            
            @if(request('category_id'))
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-900 text-gray-300">
                    Category: <span class="ml-1 text-lime-400">{{ $categories->firstWhere('id', request('category_id'))->name }}</span>
                    <a href="{{ request()->url() . '?' . http_build_query(request()->except('category_id')) }}" class="ml-1 text-gray-400 hover:text-white">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </span>
            @endif
            
            @if(request('vendor_id'))
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-900 text-gray-300">
                    Vendor: <span class="ml-1 text-lime-400">{{ $vendors->firstWhere('id', request('vendor_id'))->name }}</span>
                    <a href="{{ request()->url() . '?' . http_build_query(request()->except('vendor_id')) }}" class="ml-1 text-gray-400 hover:text-white">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </span>
            @endif
            
            @if(request('min_price') || request('max_price'))
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-900 text-gray-300">
                    Price: 
                    <span class="ml-1 text-lime-400">
                        @if(request('min_price') && request('max_price'))
                            ${{ request('min_price') }} - ${{ request('max_price') }}
                        @elseif(request('min_price'))
                            ${{ request('min_price') }}+
                        @elseif(request('max_price'))
                            Up to ${{ request('max_price') }}
                        @endif
                    </span>
                    <a href="{{ request()->url() . '?' . http_build_query(request()->except(['min_price', 'max_price'])) }}" class="ml-1 text-gray-400 hover:text-white">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </span>
            @endif
        </div>
    @endif

    <!-- Products Grid -->
    @if(count($products) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
                <div class="bg-green-900/40 backdrop-blur-sm rounded-lg overflow-hidden border border-green-800 hover:shadow-lg hover:shadow-lime-800/20 transition-all hover-glow">
                    <div class="relative pb-[70%] bg-green-950/80">
                        <img src="{{ $product->image ?? 'https://via.placeholder.com/300x210?text=No+Image' }}" 
                             alt="{{ $product->name }}" 
                             class="absolute inset-0 w-full h-full object-cover">
                        <div class="absolute top-2 right-2 flex space-x-1">
                            @if($product->is_featured)
                                <span class="bg-yellow-500 text-xs font-bold uppercase py-1 px-2 rounded text-green-950">
                                    Featured
                                </span>
                            @endif
                            @if($product->stock <= 5 && $product->stock > 0)
                                <span class="bg-amber-500 text-xs font-bold uppercase py-1 px-2 rounded text-green-950">
                                    Low Stock
                                </span>
                            @endif
                            @if($product->stock === 0)
                                <span class="bg-red-500 text-xs font-bold uppercase py-1 px-2 rounded text-green-950">
                                    Out of Stock
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="p-4 space-y-3">
                        <div class="flex justify-between items-start">
                            <h3 class="text-lg font-semibold text-white truncate">{{ $product->name }}</h3>
                            <span class="text-lime-400 font-bold">${{ number_format($product->price, 2) }}</span>
                        </div>
                        
                        <div class="flex items-center text-xs text-gray-400">
                            <span>{{ $product->category->name ?? 'Uncategorized' }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ $product->vendor->name ?? 'Unknown Vendor' }}</span>
                        </div>
                        
                        <p class="text-sm text-gray-300 line-clamp-2">{{ $product->description }}</p>
                        
                        <div class="flex items-center justify-between pt-2">
                            <span class="text-xs text-gray-400">Stock: {{ $product->stock }}</span>
                            <div class="flex space-x-2">
                                <a href="{{ route('products.edit', $product->id) }}" class="p-1 text-gray-300 hover:text-lime-400 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this)" class="p-1 text-gray-300 hover:text-red-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-green-900/40 backdrop-blur-sm rounded-lg p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-lime-400">No products found</h3>
            <p class="mt-2 text-sm text-gray-300">Try adjusting your search or filter criteria to find what you're looking for.</p>
            <div class="mt-6">
                <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-lime-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-lime-500 focus:outline-none focus:ring-2 focus:ring-lime-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Add New Product
                </a>
            </div>
        </div>
    @endif

    <!-- Pagination -->
    @if($products->hasPages())
        <div class="bg-green-900/40 backdrop-blur-sm rounded-lg p-3 flex flex-col sm:flex-row justify-between items-center">
            <div class="text-sm text-gray-400 mb-3 sm:mb-0">
                Showing <span class="font-medium text-lime-400">{{ $products->firstItem() }}</span> to <span class="font-medium text-lime-400">{{ $products->lastItem() }}</span> of <span class="font-medium text-lime-400">{{ $products->total() }}</span> products
            </div>
            
            <div class="flex space-x-1">
                {{ $products->appends(request()->except('page'))->links('pagination.tailwind', ['colors' => [
                    'active' => 'bg-lime-600 text-white',
                    'inactive' => 'text-gray-300 hover:bg-green-800 hover:text-white',
                    'disabled' => 'opacity-50 cursor-not-allowed text-gray-300',
                ]]) }}
            </div>
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div id="modal-backdrop" class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div id="modal-content" class="inline-block align-bottom rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full glass-effect border border-green-800">
            <div class="bg-green-900/80 backdrop-blur-md px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-lime-400" id="modal-title">Delete Product</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-300">Are you sure you want to delete this product? This action cannot be undone and all associated data will be permanently removed from our servers.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-green-900/60 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button id="confirm-delete-btn" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">Delete</button>
                <button onclick="closeDeleteModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-green-700 shadow-sm px-4 py-2 bg-green-800 text-base font-medium text-gray-300 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Toggle filters section
function toggleFilters() {
    const filtersContainer = document.getElementById('filters-container');
    const filterArrow = document.getElementById('filter-arrow');
    
    if (filtersContainer.classList.contains('hidden')) {
        filtersContainer.classList.remove('hidden');
        filterArrow.classList.add('rotate-180');
    } else {
        filtersContainer.classList.add('hidden');
        filterArrow.classList.remove('rotate-180');
    }
}

// Show filters on page load if there are active filters
document.addEventListener('DOMContentLoaded', function() {
    // Check if there are any active filters
    const hasActiveFilters = {{ (request('search') || request('category_id') || request('vendor_id') || request('min_price') || request('max_price')) ? 'true' : 'false' }};
    
    if (hasActiveFilters) {
        // Show the filters
        toggleFilters();
    }
});

// Delete confirmation
let deleteForm = null;

function confirmDelete(button) {
    deleteForm = button.closest('form');
    document.getElementById('delete-modal').classList.remove('hidden');
    
    // Set up the confirm button
    document.getElementById('confirm-delete-btn').onclick = function() {
        if (deleteForm) {
            deleteForm.submit();
        }
        closeDeleteModal();
    };
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
    deleteForm = null;
}

// Close modal when clicking outside of it
document.getElementById('modal-backdrop').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// Show notification system
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.classList.add('fixed', 'top-4', 'right-4', 'z-50', 'p-4', 'rounded-lg', 'shadow-lg', 'transform', 'transition-all', 'duration-500', 'ease-in-out');
    
    // Style based on notification type
    switch(type) {
        case 'success':
            notification.classList.add('bg-lime-500', 'text-green-950');
            break;
        case 'error':
            notification.classList.add('bg-red-500', 'text-white');
            break;
        case 'warning':
            notification.classList.add('bg-amber-500', 'text-green-950');
            break;
        default: // info
            notification.classList.add('bg-blue-500', 'text-white');
    }
    
    // Create content
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">
                ${type === 'success' ? 
                    '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' :
                type === 'error' ?
                    '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>' :
                type === 'warning' ?
                    '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>' :
                    '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                }
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button onclick="this.parentElement.parentElement.parentElement.parentElement.remove()" class="inline-flex rounded-md p-1.5 hover:bg-opacity-20 hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-800">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Add to DOM
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.add('translate-x-0');
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.add('opacity-0', 'translate-y-[-10px]');
        setTimeout(() => {
            notification.remove();
        }, 500);
    }, 5000);
}

// Listen for server-side session flash messages on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check for Laravel's session flash messages
    const successMessage = '{{ session('success') }}';
    const errorMessage = '{{ session('error') }}';
    const warningMessage = '{{ session('warning') }}';
    const infoMessage = '{{ session('info') }}';
    
    if (successMessage && successMessage !== '') {
        showNotification(successMessage, 'success');
    }
    
    if (errorMessage && errorMessage !== '') {
        showNotification(errorMessage, 'error');
    }
    
    if (warningMessage && warningMessage !== '') {
        showNotification(warningMessage, 'warning');
    }
    
    if (infoMessage && infoMessage !== '') {
        showNotification(infoMessage, 'info');
    }

    // Add hover glow effect to product cards
    const productCards = document.querySelectorAll('.hover-glow');
    productCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.classList.add('shadow-lg', 'shadow-lime-800/30');
        });
        card.addEventListener('mouseleave', function() {
            this.classList.remove('shadow-lg', 'shadow-lime-800/30');
        });
    });
});

// Add keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Alt+N = New Product
    if (e.altKey && e.key === 'n') {
        window.location.href = '{{ route('products.create') }}';
    }
    
    // Alt+F = Focus on search filter
    if (e.altKey && e.key === 'f') {
        const filtersContainer = document.getElementById('filters-container');
        if (filtersContainer.classList.contains('hidden')) {
            toggleFilters();
        }
        document.getElementById('search').focus();
    }
});

// Handle Enter key in search field to submit form
document.getElementById('search')?.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        document.getElementById('filter-form').submit();
    }
});

// Make form filter changes apply automatically
const autoApplyFilters = [
    'category', 'vendor', 'sort_by', 'sort_dir', 'per_page'
];

autoApplyFilters.forEach(filterId => {
    const element = document.getElementById(filterId);
    if (element) {
        element.addEventListener('change', function() {
            document.getElementById('filter-form').submit();
        });
    }
});
</script>
@endpush