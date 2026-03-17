@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-400">Edit Product</h1>
                <p class="mt-1 text-sm text-gray-400">Update product details</p>
            </div>
            <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-700 border border-slate-600 rounded-md font-semibold text-xs text-gray-300 uppercase tracking-widest hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Products
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg shadow-lg border border-slate-700 p-6">
        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Product Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Product Name *</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name', $product->name) }}"
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-300 bg-slate-900/60 placeholder:text-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') ring-2 ring-red-500 @enderror" 
                        placeholder="Enter product name"
                        required
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-300 mb-2">Category *</label>
                    <select 
                        name="category_id" 
                        id="category_id" 
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-300 bg-slate-900/60 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('category_id') ring-2 ring-red-500 @enderror"
                        required
                    >
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Vendor -->
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-300 mb-2">Vendor *</label>
                    <select 
                        name="user_id" 
                        id="user_id" 
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-300 bg-slate-900/60 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('user_id') ring-2 ring-red-500 @enderror"
                        required
                    >
                        <option value="">Select a vendor</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ old('user_id', $product->user_id) == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-300 mb-2">Price ($) *</label>
                    <input 
                        type="number" 
                        name="price" 
                        id="price" 
                        value="{{ old('price', $product->price) }}"
                        step="0.01"
                        min="0"
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-300 bg-slate-900/60 placeholder:text-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('price') ring-2 ring-red-500 @enderror" 
                        placeholder="0.00"
                        required
                    >
                    @error('price')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stock -->
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-300 mb-2">Stock Quantity *</label>
                    <input 
                        type="number" 
                        name="stock" 
                        id="stock" 
                        value="{{ old('stock', $product->stock) }}"
                        min="0"
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-300 bg-slate-900/60 placeholder:text-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('stock') ring-2 ring-red-500 @enderror" 
                        placeholder="0"
                        required
                    >
                    @error('stock')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                    <textarea 
                        name="description" 
                        id="description" 
                        rows="4"
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-300 bg-slate-900/60 placeholder:text-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') ring-2 ring-red-500 @enderror" 
                        placeholder="Enter product description"
                    >{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image URL -->
                <div class="md:col-span-2">
                    <label for="image" class="block text-sm font-medium text-gray-300 mb-2">Image URL</label>
                    <input 
                        type="url" 
                        name="image" 
                        id="image" 
                        value="{{ old('image', $product->image) }}"
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-300 bg-slate-900/60 placeholder:text-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('image') ring-2 ring-red-500 @enderror" 
                        placeholder="https://example.com/image.jpg"
                    >
                    @error('image')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Provide a direct URL to the product image</p>
                </div>

                <!-- Current Image Preview -->
                @if($product->image)
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Current Image</label>
                    <div class="w-48 h-48 rounded-lg overflow-hidden border border-slate-700">
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    </div>
                </div>
                @endif

                <!-- Featured Checkbox -->
                <div class="md:col-span-2">
                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="is_featured" 
                            id="is_featured" 
                            value="1"
                            {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 bg-slate-900 border-slate-600 rounded focus:ring-blue-500 focus:ring-2"
                        >
                        <label for="is_featured" class="ml-2 text-sm font-medium text-gray-300">
                            Mark as Featured Product
                        </label>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-between pt-6 border-t border-slate-700">
                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="if(confirm('Are you sure you want to delete this product?')) this.closest('form').submit();" class="px-6 py-2.5 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition">
                        Delete Product
                    </button>
                </form>

                <div class="flex space-x-3">
                    <a href="{{ route('products.index') }}" class="px-6 py-2.5 bg-slate-700 text-gray-300 rounded-md hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        Update Product
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
