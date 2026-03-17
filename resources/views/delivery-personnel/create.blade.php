@extends('layouts.app')

@section('title', 'Add Delivery Person')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-400">Add Delivery Person</h1>
                <p class="mt-1 text-sm text-gray-400">Create a new delivery personnel account</p>
            </div>
            <a href="{{ route('delivery-personnel.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-700 border border-slate-600 rounded-md font-semibold text-xs text-gray-300 uppercase tracking-widest hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg shadow-lg border border-slate-700 p-6">
        <form action="{{ route('delivery-personnel.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Full Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Full Name *</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name') }}"
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
                        value="{{ old('email') }}"
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
                        value="{{ old('phone') }}"
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-300 bg-slate-900/60 placeholder:text-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone') ring-2 ring-red-500 @enderror" 
                        placeholder="+1 (555) 123-4567"
                    >
                    @error('phone')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Password *</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-300 bg-slate-900/60 placeholder:text-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') ring-2 ring-red-500 @enderror" 
                        placeholder="Enter password"
                        required
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">Confirm Password *</label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-300 bg-slate-900/60 placeholder:text-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                        placeholder="Confirm password"
                        required
                    >
                </div>

                <!-- Vehicle Type -->
                <div>
                    <label for="vehicle_type" class="block text-sm font-medium text-gray-300 mb-2">Vehicle Type</label>
                    <input 
                        type="text" 
                        name="vehicle_type" 
                        id="vehicle_type" 
                        value="{{ old('vehicle_type') }}"
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
                        value="{{ old('license_plate') }}"
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
                        <option value="available" {{ old('availability_status') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="on_delivery" {{ old('availability_status') == 'on_delivery' ? 'selected' : '' }}>On Delivery</option>
                        <option value="unavailable" {{ old('availability_status') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    </select>
                    @error('availability_status')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-end space-x-3 pt-6 border-t border-slate-700">
                <a href="{{ route('delivery-personnel.index') }}" class="px-6 py-2.5 bg-slate-700 text-gray-300 rounded-md hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    Create Delivery Person
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
