@extends('layouts.auth')

@section('title', 'Login | SOKONI')

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-green-900 via-green-950 to-lime-800">
    <div class="relative flex-1 hidden w-0 lg:block">
        <div class="absolute inset-0 flex flex-col items-center justify-center">
            <div class="w-full max-w-md p-8">
                <div class="mb-8">
                    <h1 class="text-5xl font-bold text-white tracking-tight">SOKONI</h1>
                    <p class="mt-3 text-xl text-lime-200">Your trusted marketplace platform</p>
                </div>
                <div class="space-y-6">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-lime-600/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-lime-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-xl font-medium text-white">Secure Authentication</h2>
                            <p class="mt-1 text-lime-200">State-of-the-art security for your account</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-lime-600/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-lime-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-xl font-medium text-white">Seamless Transactions</h2>
                            <p class="mt-1 text-lime-200">Fast and reliable payment processing</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flex flex-col justify-center flex-1 px-4 py-12 sm:px-6 lg:px-20 xl:px-24">
        <div class="w-full max-w-lg mx-auto">
            <div class="mb-10 lg:hidden">
                <h1 class="text-4xl font-bold text-white tracking-tight">SOKONI</h1>
                <p class="mt-2 text-lg text-lime-200">Your trusted marketplace platform</p>
            </div>
            <div>
                <div class="backdrop-blur-xl rounded-2xl shadow-xl shadow-lime-900/40 bg-white/10">
                    <div class="p-6 sm:p-8">
                        <h2 class="text-2xl font-medium text-white">Sign in to your account</h2>
                        <form class="mt-6 space-y-6" action="{{ route('login') }}" method="POST">
                            @csrf
                            <div>
                                <label for="email" class="block text-sm font-medium text-lime-200">Email address</label>
                                <div class="mt-1">
                                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" class="block w-full px-4 py-3 text-white placeholder-lime-300 transition duration-150 ease-in-out bg-lime-900/50 border border-lime-700 rounded-xl focus:ring-2 focus:ring-lime-500 focus:border-lime-500 focus:outline-none">
                                </div>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-lime-200">Password</label>
                                <div class="mt-1">
                                    <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full px-4 py-3 text-white placeholder-lime-300 transition duration-150 ease-in-out bg-lime-900/50 border border-lime-700 rounded-xl focus:ring-2 focus:ring-lime-500 focus:border-lime-500 focus:outline-none">
                                </div>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input id="remember_me" name="remember" type="checkbox" class="w-4 h-4 border-lime-600 rounded text-lime-600 focus:ring-lime-500">
                                    <label for="remember_me" class="block ml-2 text-sm text-lime-200">Remember me</label>
                                </div>

                                <div class="text-sm">
                                    <a href="{{ route('password.request') }}" class="font-medium text-lime-300 hover:text-lime-200 transition">Forgot your password?</a>
                                </div>
                            </div>

                            <div>
                                <button type="submit" class="flex justify-center w-full px-4 py-3 text-sm font-medium text-white transition duration-150 ease-in-out bg-gradient-to-r from-lime-600 to-green-600 border border-transparent rounded-xl hover:from-lime-500 hover:to-green-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500 focus:ring-offset-lime-900">
                                    Sign in
                                </button>
                            </div>
                        </form>

                        <!-- <div class="mt-6">
                            <div class="relative">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-lime-700"></div>
                                </div>
                                <div class="relative flex justify-center text-sm">
                                    <span class="px-2 text-lime-300 bg-lime-900/50 backdrop-blur-xl">Or continue with</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mt-6">
                                <a href="#" class="flex items-center justify-center px-4 py-2 text-sm font-medium text-lime-200 transition duration-150 ease-in-out bg-lime-900/50 border border-lime-700 rounded-xl hover:bg-green-800/50">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 0C4.477 0 0 4.477 0 10c0 4.42 2.865 8.166 6.839 9.489.5.092.682-.217.682-.482 0-.237-.008-.866-.013-1.7-2.782.605-3.369-1.343-3.369-1.343-.454-1.155-1.11-1.462-1.11-1.462-.908-.62.069-.608.069-.608 1.003.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.27.098-2.647 0 0 .84-.269 2.75 1.022A9.578 9.578 0 0110 4.836c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.377.203 2.394.1 2.647.64.699 1.028 1.592 1.028 2.683 0 3.842-2.339 4.687-4.566 4.935.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48C17.14 18.163 20 14.418 20 10c0-5.523-4.477-10-10-10z" clip-rule="evenodd" />
                                    </svg>
                                    <span>GitHub</span>
                                </a>
                                <a href="#" class="flex items-center justify-center px-4 py-2 text-sm font-medium text-green-200 transition duration-150 ease-in-out bg-green-900/50 border border-green-700 rounded-xl hover:bg-green-800/50">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 0C4.477 0 0 4.477 0 10c0 5.523 4.477 10 10 10 5.523 0 10-4.477 10-10C20 4.477 15.523 0 10 0zm-1.786 15h-2.84v-9.1h2.84V15zm-1.42-10.346c-.94 0-1.7-.764-1.7-1.704 0-.94.76-1.704 1.7-1.704.94 0 1.7.764 1.7 1.704 0 .94-.76 1.704-1.7 1.704zm10.406 10.346h-2.84v-4.95c0-2.69-3.18-2.483-3.18 0V15H8.34V5.9h2.84v1.71c1.408-2.61 5.02-2.803 5.02 2.5V15z" clip-rule="evenodd" />
                                    </svg>
                                    <span>LinkedIn</span>
                                </a>
                            </div>
                        </div> -->
                    </div>
                    <div class="p-6 bg-green-900/70 backdrop-blur-xl border-t border-green-800 rounded-b-2xl">
                        <p class="text-sm text-center text-green-300">
                            Don't have an account?
                            <a href="{{ route('register') }}" class="font-medium text-green-200 hover:text-white transition">
                                Register now
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection