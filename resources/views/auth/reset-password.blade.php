@extends('layouts.auth')

@section('title', 'Reset Password | SOKONI')

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-green-900 via-green-950 to-lime-800">
    <div class="flex flex-col justify-center flex-1 px-4 py-12 sm:px-6 lg:px-20 xl:px-24">
        <div class="w-full max-w-lg mx-auto">
            <div class="mb-10">
                <a href="{{ route('login') }}" class="flex items-center text-green-300 hover:text-white transition duration-150 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to login
                </a>
            </div>
            <div>
                <div class="mb-8 text-center">
                    <h1 class="text-4xl font-bold text-white tracking-tight">SOKONI</h1>
                    <p class="mt-2 text-lg text-green-200">Your trusted marketplace platform</p>
                </div>
                <div class="backdrop-blur-xl rounded-2xl shadow-xl shadow-green-900/40 bg-white/10">
                    <div class="p-6 sm:p-8">
                        <h2 class="text-2xl font-medium text-white">Create new password</h2>
                        <p class="mt-2 text-green-200">Enter your new password below.</p>
                        
                        <form class="mt-6 space-y-6" action="{{ route('password.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">
                            <input type="hidden" name="email" value="{{ $request->email }}">
                            
                            <div>
                                <label for="password" class="block text-sm font-medium text-green-200">New password</label>
                                <div class="mt-1">
                                    <input id="password" name="password" type="password" autocomplete="new-password" required class="block w-full px-4 py-3 text-white placeholder-green-300 transition duration-150 ease-in-out bg-green-900/50 border border-green-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 focus:outline-none">
                                </div>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-green-200">Confirm new password</label>
                                <div class="mt-1">
                                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="block w-full px-4 py-3 text-white placeholder-green-300 transition duration-150 ease-in-out bg-green-900/50 border border-green-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 focus:outline-none">
                                </div>
                                @error('password_confirmation')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <button type="submit" class="flex justify-center w-full px-4 py-3 text-sm font-medium text-white transition duration-150 ease-in-out bg-gradient-to-r from-green-600 to-green-600 border border-transparent rounded-xl hover:from-green-500 hover:to-green-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 focus:ring-offset-green-900">
                                    Reset password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection