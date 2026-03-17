@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Create New User</h1>
            <p class="mt-1 text-gray-400">Add a new user to the system</p>
        </div>
        <a href="{{ route('users.index') }}" class="px-4 py-2 bg-slate-600 text-white rounded-md hover:bg-slate-500 transition">
            Back to Users
        </a>
    </div>

    <!-- Create Form -->
    <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                    Full Name <span class="text-red-400">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                    Email Address <span class="text-red-400">*</span>
                </label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                    Password <span class="text-red-400">*</span>
                </label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-400">Minimum 8 characters</p>
                @error('password')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">
                    Confirm Password <span class="text-red-400">*</span>
                </label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                    class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Role -->
            <div class="mb-6">
                <label for="role_id" class="block text-sm font-medium text-gray-300 mb-2">
                    User Role <span class="text-red-400">*</span>
                </label>
                <select id="role_id" name="role_id" required
                    class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 @error('role_id') border-red-500 @enderror">
                    <option value="">Select a role...</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-400">
                    <strong>Note:</strong> After creating this user, you'll need to create their corresponding profile 
                    (Customer, Vendor, or Delivery Personnel) based on the role selected.
                </p>
                @error('role_id')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-700">
                <a href="{{ route('users.index') }}" class="px-6 py-2 bg-slate-600 text-white rounded-md hover:bg-slate-500 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500 transition">
                    Create User
                </button>
            </div>
        </form>
    </div>

    <!-- Info Card -->
    <div class="bg-blue-500/10 border border-blue-500/50 rounded-lg p-4">
        <div class="flex items-start space-x-3">
            <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-sm text-gray-300">
                <p class="font-medium text-blue-400 mb-1">Important Information</p>
                <ul class="list-disc list-inside space-y-1 text-gray-400">
                    <li>User email will be automatically verified upon creation</li>
                    <li>For vendor, customer, or delivery roles, create their profile separately after user creation</li>
                    <li>Admin users don't require additional profiles</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
