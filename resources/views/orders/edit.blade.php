@extends('layouts.app')

@section('title', 'Edit Order')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-400">Edit Order #{{ $order->id }}</h1>
                <p class="mt-1 text-sm text-gray-400">Update order details and status</p>
            </div>
            <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center px-4 py-2 bg-slate-700 border border-slate-600 rounded-md font-semibold text-xs text-gray-300 uppercase tracking-widest hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Order
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg shadow-lg border border-slate-700 p-6">
        <form action="{{ route('orders.update', $order) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Order Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-300 mb-2">Order Status *</label>
                    <select 
                        name="status" 
                        id="status" 
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-300 bg-slate-900/60 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') ring-2 ring-red-500 @enderror"
                        required
                    >
                        <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ old('status', $order->status) == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="delivered" {{ old('status', $order->status) == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Customer (Read-only) -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Customer</label>
                    <div class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-400 bg-slate-900/40 border border-slate-700">
                        {{ $order->customer->user->name }} ({{ $order->customer->user->email }})
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Customer cannot be changed after order creation</p>
                </div>

                <!-- Delivery Personnel -->
                <div>
                    <label for="delivery_id" class="block text-sm font-medium text-gray-300 mb-2">Delivery Personnel</label>
                    <select 
                        name="delivery_id" 
                        id="delivery_id" 
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-300 bg-slate-900/60 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">Not assigned</option>
                        @foreach(\App\Models\DeliveryPersonnel::with('user')->get() as $delivery)
                            <option value="{{ $delivery->id }}" {{ old('delivery_id', $order->delivery_id) == $delivery->id ? 'selected' : '' }}>
                                {{ $delivery->user->name }} - {{ $delivery->vehicle_type ?? 'Vehicle' }}
                                @if($delivery->availability_status !== 'available')
                                    ({{ ucfirst($delivery->availability_status) }})
                                @endif
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
                    >{{ old('delivery_address', $order->delivery_address) }}</textarea>
                </div>

                <!-- Order Items (Read-only) -->
                <div class="border-t border-slate-700 pt-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Order Items</h3>
                    <div class="bg-slate-900/40 rounded-lg border border-slate-700 overflow-hidden">
                        <table class="min-w-full divide-y divide-slate-700">
                            <thead class="bg-slate-900/60">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Product</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Price</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Quantity</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700">
                                @foreach($order->orderDetails as $detail)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-300">{{ $detail->product->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-300">${{ number_format($detail->price, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-300">{{ $detail->quantity }}</td>
                                    <td class="px-4 py-3 text-sm font-semibold text-blue-400">${{ number_format($detail->price * $detail->quantity, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-900/60">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right text-sm font-semibold text-white">Total:</td>
                                    <td class="px-4 py-3 text-sm font-bold text-blue-400">${{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Order items cannot be modified after creation. Cancel and create a new order if needed.</p>
                </div>

                <!-- Order Info -->
                <div class="bg-slate-900/40 rounded-lg p-4 border border-slate-700">
                    <h4 class="text-sm font-medium text-gray-300 mb-2">Order Information</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Created:</span>
                            <span class="text-gray-300 ml-2">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Last Updated:</span>
                            <span class="text-gray-300 ml-2">{{ $order->updated_at->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-between pt-6 border-t border-slate-700">
                @if($order->status === 'pending')
                <form action="{{ route('orders.destroy', $order) }}" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="if(confirm('Are you sure you want to cancel this order? Products will be returned to inventory.')) this.closest('form').submit();" class="px-6 py-2.5 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition">
                        Cancel Order
                    </button>
                </form>
                @else
                <div></div>
                @endif

                <div class="flex space-x-3">
                    <a href="{{ route('orders.show', $order) }}" class="px-6 py-2.5 bg-slate-700 text-gray-300 rounded-md hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 transition">
                        Back
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        Update Order
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
