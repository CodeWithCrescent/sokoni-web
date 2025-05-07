@extends('layouts.auth')

@section('title', 'Verify Email | SOKONI')

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-green-900 via-green-950 to-lime-800">
    <div class="flex flex-col justify-center flex-1 px-4 py-12 sm:px-6 lg:px-20 xl:px-24">
        <div class="w-full max-w-lg mx-auto">
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-bold text-white tracking-tight">SOKONI</h1>
                <p class="mt-2 text-lg text-green-200">Your trusted marketplace platform</p>
            </div>
            <div class="backdrop-blur-xl rounded-2xl shadow-xl shadow-green-900/40 bg-white/10">
                <div class="p-6 sm:p-8">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-6 rounded-full bg-green-600/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-medium text-center text-white">Verify your email</h2>
                    <p class="mt-2 text-center text-green-200">
                        Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?
                    </p>
                    
                    @if (session('status') == 'verification-link-sent')
                        <div class="p-4 mt-6 rounded-xl bg-green-900/50 backdrop-blur-xl">
                            <p class="text-sm text-green-200">A new verification link has been sent to the email address you provided during registration.</p>
                        </div>
                    @endif
                    
                    <div class="flex items-center justify-between mt-6">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="px-5 py-2 text-sm font-medium text-white transition duration-150 ease-in-out bg-gradient-to-r from-green-600 to-green-600 border border-transparent rounded-xl hover:from-green-500 hover:to-green-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 focus:ring-offset-green-900">
                                Resend Verification Email
                            </button>
                        </form>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-green-200 transition duration-150 ease-in-out bg-green-900/50 border border-green-700 rounded-xl hover:bg-green-800/50">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection