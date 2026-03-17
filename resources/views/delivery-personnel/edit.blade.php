@extends('layouts.app')

@section('title', 'Edit Delivery Person')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-400">Edit Delivery Person</h1>
                <p class="mt-1 text-sm text-gray-400">Update delivery personnel information</p>
            </div>
            <a href="{{ route('delivery-personnel.show', $deliveryPersonnel) }}" class="inline-flex items-center px-4 py-2 bg-slate-700 border border-slate-600 rounded-md font-semibold text-xs text-gray-300 uppercase tracking-widest hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Profile
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg shadow-lg border border-slate-700 p-6">
        <form action="{{ route('delivery-personnel.update', $deliveryPersonnel) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Full Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Full Name *</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name', $deliveryPersonnel->user->name) }}"
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-300 bg-slate-900/60 placeholder:text-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') ring-2 ring-red-500 @enderror" 
                        placeholder="Enter full name"
                        required
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email Address *</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email', $deliveryPersonnel->user->email) }}"
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-300 bg-slate-900/60 placeholder:text-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') ring-2 ring-red-500 @enderror" 
                        placeholder="delivery@example.com"
                        required
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-300 mb-2">Phone Number</label>
                    <input 
                        type="tel" 
                        name="phone" 
                        id="phone" 
                        value="{{ old('phone', $deliveryPersonnel->user->phone_number) }}"
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-300 bg-slate-900/60 placeholder:text-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone') ring-2 ring-red-500 @enderror" 
                        placeholder="+1 (555) 123-4567"
                    >
                    @error('phone')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">New Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-300 bg-slate-900/60 placeholder:text-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') ring-2 ring-red-500 @enderror" 
                        placeholder="Leave blank to keep current password"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Leave empty to keep current password</p>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">Confirm New Password</label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-300 bg-slate-900/60 placeholder:text-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                        placeholder="Confirm new password"
                    >
                </div>

                <!-- Vehicle Type -->
                <div>
                    <label for="vehicle_type" class="block text-sm font-medium text-gray-300 mb-2">Vehicle Type</label>
                    <input 
                        type="text" 
                        name="vehicle_type" 
                        id="vehicle_type" 
                        value="{{ old('vehicle_type', $deliveryPersonnel->vehicle_type) }}"
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-300 bg-slate-900/60 placeholder:text-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('vehicle_type') ring-2 ring-red-500 @enderror" 
                        placeholder="e.g., Motorcycle, Car, Bicycle"
                    >
                    @error('vehicle_type')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- License Plate -->
                <div>
                    <label for="license_plate" class="block text-sm font-medium text-gray-300 mb-2">License Plate</label>
                    <input 
                        type="text" 
                        name="license_plate" 
                        id="license_plate" 
                        value="{{ old('license_plate', $deliveryPersonnel->license_plate) }}"
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-300 bg-slate-900/60 placeholder:text-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('license_plate') ring-2 ring-red-500 @enderror" 
                        placeholder="ABC-1234"
                    >
                    @error('license_plate')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Availability Status -->
                <div class="md:col-span-2">
                    <label for="availability_status" class="block text-sm font-medium text-gray-300 mb-2">Availability Status</label>
                    <select 
                        name="availability_status" 
                        id="availability_status" 
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-300 bg-slate-900/60 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('availability_status') ring-2 ring-red-500 @enderror"
                    >
                        <option value="available" {{ old('availability_status', $deliveryPersonnel->availability_status) == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="on_delivery" {{ old('availability_status', $deliveryPersonnel->availability_status) == 'on_delivery' ? 'selected' : '' }}>On Delivery</option>
                        <option value="unavailable" {{ old('availability_status', $deliveryPersonnel->availability_status) == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    </select>
                    @error('availability_status')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Info -->
                <div class="md:col-span-2 bg-slate-900/40 rounded-lg p-4 border border-slate-700">
                    <h3 class="text-sm font-medium text-gray-300 mb-2">Personnel Statistics</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Personnel ID:</span>
                            <span class="text-gray-300 ml-2">#{{ $deliveryPersonnel->id }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Joined:</span>
                            <span class="text-gray-300 ml-2">{{ $deliveryPersonnel->created_at->format('M d, Y') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Total Deliveries:</span>
                            <span class="text-gray-300 ml-2">{{ $deliveryPersonnel->orders()->count() }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Last Updated:</span>
                            <span class="text-gray-300 ml-2">{{ $deliveryPersonnel->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-between pt-6 border-t border-slate-700">
                @if($deliveryPersonnel->orders()->whereIn('status', ['pending', 'processing'])->count() === 0)
                <form action="{{ route('delivery-personnel.destroy', $deliveryPersonnel) }}" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="if(confirm('Are you sure you want to delete this delivery person? This action cannot be undone.')) this.closest('form').submit();" class="px-6 py-2.5 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition">
                        Delete Personnel
                    </button>
                </form>
                @else
                <div class="text-sm text-gray-400">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Cannot delete - has {{ $deliveryPersonnel->orders()->whereIn('status', ['pending', 'processing'])->count() }} active {{ Str::plural('delivery', $deliveryPersonnel->orders()->whereIn('status', ['pending', 'processing'])->count()) }}
                </div>
                @endif

                <div class="flex space-x-3">
                    <a href="{{ route('delivery-personnel.show', $deliveryPersonnel) }}" class="px-6 py-2.5 bg-slate-700 text-gray-300 rounded-md hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        Update Profile
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
