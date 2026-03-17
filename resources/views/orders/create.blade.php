@extends('layouts.app')

@section('title', 'Create New Order')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-400">Create New Order</h1>
                <p class="mt-1 text-sm text-gray-400">Add products and assign to customer</p>
            </div>
            <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-700 border border-slate-600 rounded-md font-semibold text-xs text-gray-300 uppercase tracking-widest hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Orders
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg shadow-lg border border-slate-700 p-6" x-data="orderForm()">
        <form action="{{ route('orders.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <!-- Customer Selection -->
                <div>
                    <label for="customer_id" class="block text-sm font-medium text-gray-300 mb-2">Customer *</label>
                    <select 
                        name="customer_id" 
                        id="customer_id" 
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-300 bg-slate-900/60 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('customer_id') ring-2 ring-red-500 @enderror"
                        required
                    >
                        <option value="">Select a customer</option>
                        @foreach(\App\Models\Customer::with('user')->get() as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->user->name }} ({{ $customer->user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Delivery Personnel (Optional) -->
                <div>
                    <label for="delivery_id" class="block text-sm font-medium text-gray-300 mb-2">Delivery Personnel (Optional)</label>
                    <select 
                        name="delivery_id" 
                        id="delivery_id" 
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-300 bg-slate-900/60 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">Assign later</option>
                        @foreach(\App\Models\DeliveryPersonnel::with('user')->where('availability_status', 'available')->get() as $delivery)
                            <option value="{{ $delivery->id }}" {{ old('delivery_id') == $delivery->id ? 'selected' : '' }}>
                                {{ $delivery->user->name }} - {{ $delivery->vehicle_type ?? 'Vehicle' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Delivery Address -->
                <div>
                    <label for="delivery_address" class="block text-sm font-medium text-gray-300 mb-2">Delivery Address</label>
                    <textarea 
                        name="delivery_address" 
                        id="delivery_address" 
                        rows="3"
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-300 bg-slate-900/60 placeholder:text-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                        placeholder="Enter delivery address"
                    >{{ old('delivery_address') }}</textarea>
                </div>

                <!-- Products Section -->
                <div class="border-t border-slate-700 pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-white">Order Items</h3>
                        <button type="button" @click="addProduct()" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500 transition text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Product
                        </button>
                    </div>

                    <div class="space-y-4" id="products-container">
                        <template x-for="(product, index) in products" :key="index">
                            <div class="bg-slate-900/40 rounded-lg p-4 border border-slate-700">
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                    <!-- Product Selection -->
                                    <div class="md:col-span-6">
                                        <label class="block text-xs font-medium text-gray-400 mb-1">Product</label>
                                        <select 
                                            :name="'products[' + index + '][product_id]'" 
                                            x-model="product.product_id"
                                            @change="updateProductPrice(index, $event.target.value)"
                                            class="block w-full rounded-md border-0 py-2 px-3 text-sm text-gray-300 bg-slate-800 focus:ring-2 focus:ring-blue-500"
                                            required
                                        >
                                            <option value="">Select product</option>
                                            @foreach(\App\Models\Product::with('category')->where('stock', '>', 0)->get() as $prod)
                                                <option value="{{ $prod->id }}" data-price="{{ $prod->price }}" data-stock="{{ $prod->stock }}">
                                                    {{ $prod->name }} (Stock: {{ $prod->stock }}) - ${{ number_format($prod->price, 2) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Quantity -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-400 mb-1">Quantity</label>
                                        <input 
                                            type="number" 
                                            :name="'products[' + index + '][quantity]'" 
                                            x-model="product.quantity"
                                            @input="calculateTotal()"
                                            min="1"
                                            class="block w-full rounded-md border-0 py-2 px-3 text-sm text-gray-300 bg-slate-800 focus:ring-2 focus:ring-blue-500"
                                            required
                                        >
                                    </div>

                                    <!-- Price -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-400 mb-1">Price</label>
                                        <input 
                                            type="number" 
                                            :name="'products[' + index + '][price]'" 
                                            x-model="product.price"
                                            @input="calculateTotal()"
                                            step="0.01"
                                            class="block w-full rounded-md border-0 py-2 px-3 text-sm text-gray-300 bg-slate-800 focus:ring-2 focus:ring-blue-500"
                                            readonly
                                        >
                                    </div>

                                    <!-- Subtotal & Remove -->
                                    <div class="md:col-span-2 flex items-end justify-between">
                                        <div class="flex-1">
                                            <label class="block text-xs font-medium text-gray-400 mb-1">Subtotal</label>
                                            <div class="text-sm font-semibold text-blue-400" x-text="'$' + (product.quantity * product.price).toFixed(2)"></div>
                                        </div>
                                        <button type="button" @click="removeProduct(index)" class="p-2 text-red-400 hover:text-red-300 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="text-sm text-gray-400 mt-2" x-show="products.length === 0">
                        No products added yet. Click "Add Product" to start.
                    </div>
                </div>

                <!-- Order Total -->
                <div class="bg-slate-900/40 rounded-lg p-4 border border-slate-700">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-white">Order Total</span>
                        <span class="text-2xl font-bold text-blue-400" x-text="'$' + total.toFixed(2)"></span>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-end space-x-3 pt-6 border-t border-slate-700">
                <a href="{{ route('orders.index') }}" class="px-6 py-2.5 bg-slate-700 text-gray-300 rounded-md hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    Create Order
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function orderForm() {
    return {
        products: [],
        total: 0,
        
        addProduct() {
            this.products.push({
                product_id: '',
                quantity: 1,
                price: 0
            });
        },
        
        removeProduct(index) {
            this.products.splice(index, 1);
            this.calculateTotal();
        },
        
        updateProductPrice(index, productId) {
            const select = document.querySelector(`select[name="products[${index}][product_id]"]`);
            const selectedOption = select.options[select.selectedIndex];
            const price = parseFloat(selectedOption.dataset.price) || 0;
            this.products[index].price = price;
            this.calculateTotal();
        },
        
        calculateTotal() {
            this.total = this.products.reduce((sum, product) => {
                return sum + (parseFloat(product.quantity) * parseFloat(product.price));
            }, 0);
        },
        
        init() {
            // Add one product by default
            this.addProduct();
        }
    }
}
</script>
@endpush
@endsection
