{{-- <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout> --}}

@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Profile Information Card -->
        <div class="lg:col-span-2">
            <div class="bg-green-950/30 backdrop-blur-md z-20 rounded-xl overflow-hidden shadow-lg">
                <div class="border-b border-green-800 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-white">Profile Information</h2>
                    <div class="text-xs text-lime-400 uppercase tracking-wider">User Settings</div>
                </div>
                
                <div class="p-6">
                    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                        @csrf
                        @method('patch')
                        
                        <div>
                            <div class="flex justify-center mb-8">
                                <div class="relative group">
                                    <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-lime-500 group-hover:border-lime-400 transition-all duration-300">
                                        <img src="https://cdn.pixabay.com/photo/2018/11/13/21/43/avatar-3814049_1280.png" alt="Profile" class="w-full h-full object-cover">
                                    </div>
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                        <div class="bg-green-900/80 w-full h-full rounded-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-300 mb-1">Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" required 
                                    class="w-full px-4 py-2.5 rounded-lg z-20 bg-green-900/50 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-lime-500 focus:border-transparent transition-all duration-300">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" required 
                                    class="w-full px-4 py-2.5 rounded-lg z-20 bg-green-900/50 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-lime-500 focus:border-transparent transition-all duration-300">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-300 mb-1">Bio</label>
                            <textarea name="bio" id="bio" rows="4" 
                                class="w-full px-4 py-2.5 rounded-lg z-20 bg-green-900/50 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-lime-500 focus:border-transparent transition-all duration-300"
                                placeholder="Write a few sentences about yourself">{{ old('bio', auth()->user()->bio ?? '') }}</textarea>
                        </div>

                        <div class="pt-4">
                            <button type="submit" 
                                class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-lime-500 to-green-600 hover:from-lime-400 hover:to-green-500 text-white font-medium rounded-lg shadow-md hover-glow transition-all duration-300">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Account Settings Card -->
        <div>
            <div class="bg-green-950/30 backdrop-blur-md z-20 rounded-xl overflow-hidden shadow-lg">
                <div class="border-b border-green-800 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-white">Account Settings</h2>
                    <div class="text-xs text-lime-400 uppercase tracking-wider">Security</div>
                </div>
                
                <div class="divide-y divide-green-800">
                    <!-- Update Password Section -->
                    <div class="p-6 space-y-6">
                        <h3 class="text-lg font-medium text-gray-200">Update Password</h3>
                        
                        <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                            @csrf
                            @method('put')
                            
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-300 mb-1">Current Password</label>
                                <input type="password" name="current_password" id="current_password" required 
                                    class="w-full px-4 py-2.5 rounded-lg z-20 bg-green-900/50 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-lime-500 focus:border-transparent transition-all duration-300">
                                @error('current_password')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-300 mb-1">New Password</label>
                                <input type="password" name="password" id="password" required 
                                    class="w-full px-4 py-2.5 rounded-lg z-20 bg-green-900/50 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-lime-500 focus:border-transparent transition-all duration-300">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required 
                                    class="w-full px-4 py-2.5 rounded-lg z-20 bg-green-900/50 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-lime-500 focus:border-transparent transition-all duration-300">
                                @error('password_confirmation')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <button type="submit" 
                                    class="inline-flex items-center px-5 py-2 bg-gradient-to-r from-lime-500 to-green-600 hover:from-lime-400 hover:to-green-500 text-white font-medium rounded-lg shadow-md hover-glow transition-all duration-300">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Two Factor Authentication -->
                    <div class="p-6 space-y-6">
                        <h3 class="text-lg font-medium text-gray-200">Two Factor Authentication</h3>
                        
                        <div class="flex items-center justify-between">
                            <div class="space-y-1">
                                <p class="text-sm text-gray-300">Add additional security to your account using two factor authentication.</p>
                                <p class="text-xs text-gray-400">When two factor authentication is enabled, you will be prompted for a secure, random token during authentication.</p>
                            </div>
                            
                            <div class="ml-4">
                                <label for="toggle-2fa" class="flex items-center cursor-pointer">
                                    <div class="relative">
                                        <input id="toggle-2fa" type="checkbox" class="sr-only" />
                                        <div class="block w-10 h-6 bg-gray-600 rounded-full"></div>
                                        <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition"></div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Account Deletion -->
                    <div class="p-6 space-y-6">
                        <h3 class="text-lg font-medium text-red-400">Delete Account</h3>
                        
                        <p class="text-sm text-gray-300">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
                        
                        <div>
                            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                                class="inline-flex items-center px-4 py-2 bg-red-800/60 hover:bg-red-700/60 text-white font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    
</div>

<!-- Delete Account Confirmation Modal -->
<x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
    <div class="p-6 bg-green-950 z-20">
        <h2 class="text-lg font-medium text-white">Are you sure you want to delete your account?</h2>

        <p class="mt-2 text-sm text-gray-300">
            Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.
        </p>

        <form method="post" action="{{ route('profile.destroy') }}" class="mt-6">
            @csrf
            @method('delete')

            <div>
                <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                <input type="password" name="password" id="password" required 
                    class="w-full px-4 py-2.5 rounded-lg z-20 bg-green-900/50 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-lime-500 focus:border-transparent transition-all duration-300">
                @error('password', 'userDeletion')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Cancel
                </x-secondary-button>

                <button type="submit" 
                    class="inline-flex items-center px-4 py-2 bg-red-800/60 hover:bg-red-700/60 text-white font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete Account
                </button>
            </div>
        </form>
    </div>
</x-modal>

@push('styles') 
<style>
    /* Custom toggle switch styling */
    #toggle-2fa:checked ~ .dot {
        transform: translateX(100%);
        background-color: #84cc16;
    }
#toggle-2fa:checked ~ .block {
    background-color: #65a30d;
}
</style>
@endpush
@endsection