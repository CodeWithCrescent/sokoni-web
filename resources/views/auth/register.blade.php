@extends('layouts.auth')

@section('title', 'Register | SOKONI')

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-green-900 via-green-950 to-lime-800">
    <div class="flex flex-col justify-center flex-1 px-4 py-12 sm:px-6 lg:px-20 xl:px-24">
        <div class="w-full max-w-lg mx-auto">
            <div class="mb-10 lg:hidden">
                <h1 class="text-4xl font-bold text-white tracking-tight">SOKONI</h1>
                <p class="mt-2 text-lg text-green-200">Your trusted marketplace platform</p>
            </div>
            <div>
                <div class="backdrop-blur-xl rounded-2xl shadow-xl shadow-green-900/40 bg-white/10">
                    <div class="p-6 sm:p-8">
                        <h2 class="text-2xl font-medium text-white">Create your account</h2>
                        <form class="mt-6 space-y-6" action="{{ route('register') }}" method="POST">
                            @csrf
                            <div>
                                <label for="name" class="block text-sm font-medium text-green-200">Full name</label>
                                <div class="mt-1">
                                    <input id="name" name="name" type="text" autocomplete="name" required value="{{ old('name') }}" class="block w-full px-4 py-3 text-white placeholder-green-300 transition duration-150 ease-in-out bg-green-900/50 border border-green-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 focus:outline-none">
                                </div>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-green-200">Email address</label>
                                <div class="mt-1">
                                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" class="block w-full px-4 py-3 text-white placeholder-green-300 transition duration-150 ease-in-out bg-green-900/50 border border-green-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 focus:outline-none">
                                </div>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-green-200">Phone number (optional)</label>
                                <div class="mt-1">
                                    <input id="phone" name="phone" type="text" autocomplete="tel" value="{{ old('phone') }}" class="block w-full px-4 py-3 text-white placeholder-green-300 transition duration-150 ease-in-out bg-green-900/50 border border-green-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 focus:outline-none">
                                </div>
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-green-200">Password</label>
                                <div class="mt-1">
                                    <input id="password" name="password" type="password" autocomplete="new-password" required class="block w-full px-4 py-3 text-white placeholder-green-300 transition duration-150 ease-in-out bg-green-900/50 border border-green-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 focus:outline-none">
                                </div>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-green-200">Confirm password</label>
                                <div class="mt-1">
                                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="block w-full px-4 py-3 text-white placeholder-green-300 transition duration-150 ease-in-out bg-green-900/50 border border-green-700 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 focus:outline-none">
                                </div>
                                @error('password_confirmation')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center">
                                <input id="terms" name="terms" type="checkbox" class="w-4 h-4 border-green-600 rounded text-green-600 focus:ring-green-500" required>
                                <label for="terms" class="block ml-2 text-sm text-green-200">
                                    I agree to the <a href="#" class="font-medium text-green-300 hover:text-white transition">Terms of Service</a> and <a href="#" class="font-medium text-green-300 hover:text-white transition">Privacy Policy</a>
                                </label>
                            </div>

                            <div>
                                <button type="submit" class="flex justify-center w-full px-4 py-3 text-sm font-medium text-white transition duration-150 ease-in-out bg-gradient-to-r from-green-600 to-green-600 border border-transparent rounded-xl hover:from-green-500 hover:to-green-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 focus:ring-offset-green-900">
                                    Create account
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="p-6 bg-green-900/70 backdrop-blur-xl border-t border-green-800 rounded-b-2xl">
                        <p class="text-sm text-center text-green-300">
                            Already have an account?
                            <a href="{{ route('login') }}" class="font-medium text-green-200 hover:text-white transition">
                                Sign in
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="relative flex-1 hidden w-0 lg:block">
        <div class="absolute inset-0 flex flex-col items-center justify-center">
            <div class="w-full max-w-md p-8">
                <div class="mb-8">
                    <h1 class="text-5xl font-bold text-white tracking-tight">SOKONI</h1>
                    <p class="mt-3 text-xl text-green-200">Your trusted marketplace platform</p>
                </div>
                <div class="space-y-6">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-green-600/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-xl font-medium text-white">Marketplace Access</h2>
                            <p class="mt-1 text-green-200">Connect with buyers and sellers worldwide</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-green-600/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h11M9 21V3m11 11h-4m0 0V4m0 10v7" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-xl font-medium text-white">Simple Listings</h2>
                            <p class="mt-1 text-green-200">Post and manage products with ease</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-green-600/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.1 0-2 .9-2 2v1H8v3h2v6h3v-6h2.2l.3-3H13v-.5c0-.3.2-.5.5-.5H15V8h-3z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-xl font-medium text-white">Secure Transactions</h2>
                            <p class="mt-1 text-green-200">Shop with confidence and secure payments</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
