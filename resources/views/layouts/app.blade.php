{{-- <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html> --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') | SOKONI</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

    <style>
        body {
            font-family: 'Space Grotesk', sans-serif;
        }
        
        .glass-effect {
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .hover-glow:hover {
            box-shadow: 0 0 15px rgba(132, 255, 132, 0.6);
            transition: all 0.3s ease;
        }
        
        .menu-item-active {
            border-left: 3px solid #84cc16;
            background: linear-gradient(90deg, rgba(132, 204, 22, 0.2) 0%, rgba(255, 255, 255, 0) 100%);
        }
    </style>
</head>
<body class="font-sans antialiased bg-gradient-to-br from-gray-900 to-green-950">
    <div class="min-h-screen text-white">
        <!-- Mobile Navigation Toggle -->
        <div class="fixed top-0 left-0 z-40 w-full bg-green-950/80 backdrop-blur-md lg:hidden">
            <div class="flex items-center justify-between h-16 px-4">
                <div class="flex items-center space-x-2">
                    <button id="sidebar-toggle" class="p-2 text-lime-400 rounded-lg hover:bg-green-900 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h1 class="text-2xl font-bold text-white">SOKONI</h1>
                </div>
                
                <div class="flex items-center space-x-4">
                    <button class="p-2 text-lime-400 rounded-full hover:bg-green-900 focus:outline-none relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span class="absolute top-0 right-0 w-2 h-2 bg-lime-500 rounded-full"></span>
                    </button>
                    
                    <button class="relative rounded-full overflow-hidden w-8 h-8 border border-lime-500">
                        <img src="https://i.pravatar.cc/150?img=28" alt="Profile" class="object-cover w-full h-full">
                    </button>
                </div>
            </div>
        </div>

        <div class="flex h-screen pt-16 lg:pt-0">
            <!-- Sidebar -->
            <aside id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 transform -translate-x-full lg:translate-x-0 h-full transition-all duration-300 bg-green-950/50 backdrop-blur-md overflow-y-auto">
                <div class="flex items-center justify-center h-16 px-6 border-b border-green-800 lg:border-none lg:bg-green-950/40 lg:backdrop-blur-md">
                    <h1 class="text-2xl font-bold tracking-wider text-white">
                        <span class="text-lime-400">SO</span>KONI
                    </h1>
                </div>

                <div class="flex flex-col flex-1 px-3 py-6 space-y-1">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-gray-300 transition-colors rounded-lg hover:bg-green-900/40 hover:text-lime-400 hover-glow {{ request()->routeIs('dashboard') ? 'menu-item-active text-lime-400' : '' }}">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="mx-4 font-medium">Dashboard</span>
                    </a>

                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-3 text-gray-300 transition-colors rounded-lg hover:bg-green-900/40 hover:text-lime-400 hover-glow">
                            <div class="flex items-center">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                <span class="mx-4 font-medium">Products</span>
                            </div>
                            <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="pl-12 mt-1 space-y-1">
                            <a href="#" class="block py-2 pl-2 pr-4 text-sm text-gray-400 transition-colors rounded-lg hover:bg-green-900/40 hover:text-lime-400">
                                All Products
                            </a>
                            <a href="#" class="block py-2 pl-2 pr-4 text-sm text-gray-400 transition-colors rounded-lg hover:bg-green-900/40 hover:text-lime-400">
                                Add Product
                            </a>
                            <a href="#" class="block py-2 pl-2 pr-4 text-sm text-gray-400 transition-colors rounded-lg hover:bg-green-900/40 hover:text-lime-400">
                                Categories
                            </a>
                        </div>
                    </div>

                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-3 text-gray-300 transition-colors rounded-lg hover:bg-green-900/40 hover:text-lime-400 hover-glow">
                            <div class="flex items-center">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <span class="mx-4 font-medium">Orders</span>
                            </div>
                            <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="pl-12 mt-1 space-y-1">
                            <a href="#" class="block py-2 pl-2 pr-4 text-sm text-gray-400 transition-colors rounded-lg hover:bg-green-900/40 hover:text-lime-400">
                                All Orders
                            </a>
                            <a href="#" class="block py-2 pl-2 pr-4 text-sm text-gray-400 transition-colors rounded-lg hover:bg-green-900/40 hover:text-lime-400">
                                Pending
                            </a>
                            <a href="#" class="block py-2 pl-2 pr-4 text-sm text-gray-400 transition-colors rounded-lg hover:bg-green-900/40 hover:text-lime-400">
                                Completed
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 text-gray-300 transition-colors rounded-lg hover:bg-green-900/40 hover:text-lime-400 hover-glow {{ request()->routeIs('profile.*') ? 'menu-item-active text-lime-400' : '' }}">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="mx-4 font-medium">Profile</span>
                    </a>

                    <a href="#" class="flex items-center px-4 py-3 text-gray-300 transition-colors rounded-lg hover:bg-green-900/40 hover:text-lime-400 hover-glow">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="mx-4 font-medium">Settings</span>
                    </a>
                </div>

                <div class="px-3 py-4 mt-auto border-t border-green-800">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center w-full px-4 py-3 text-gray-300 transition-colors rounded-lg hover:bg-green-900/40 hover:text-lime-400 hover-glow">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span class="mx-4 font-medium">Logout</span>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main content area -->
            <div class="flex-1 w-full lg:ml-64">
                <!-- Desktop Navbar -->
                <nav class="hidden lg:flex items-center justify-between h-16 px-6 bg-green-950/80 backdrop-blur-md z-30">
                    <div class="flex items-center">
                        <h2 class="text-xl font-semibold text-white">@yield('title', 'Dashboard')</h2>
                    </div>
                    
                    <div class="flex items-center space-x-6">
                        <button class="p-2 text-lime-400 rounded-full hover:bg-green-900 focus:outline-none relative">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span class="absolute top-0 right-0 w-2 h-2 bg-lime-500 rounded-full"></span>
                        </button>
                        
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-3 focus:outline-none">
                                <div class="relative rounded-full overflow-hidden w-8 h-8 border border-lime-500">
                                    <img src="https://cdn.pixabay.com/photo/2018/11/13/21/43/avatar-3814049_1280.png" alt="Profile" class="object-cover w-full h-full">
                                </div>
                                <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" class="absolute right-0 z-20 w-48 mt-2 bg-green-900 rounded-lg shadow-lg py-1 border border-green-800">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-200 hover:bg-green-800 hover:text-lime-400">
                                    Profile
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-200 hover:bg-green-800 hover:text-lime-400">
                                    Settings
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-200 hover:bg-green-800 hover:text-lime-400">
                                        Sign out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Content area -->
                <main class="p-4 lg:p-6">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
    
    <script>
        // Alpine.js v3
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile sidebar toggle
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.getElementById('sidebar');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    if (sidebar.classList.contains('-translate-x-full')) {
                        sidebar.classList.remove('-translate-x-full');
                        sidebar.classList.add('translate-x-0');
                    } else {
                        sidebar.classList.remove('translate-x-0');
                        sidebar.classList.add('-translate-x-full');
                    }
                });
            }
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                const isClickInside = sidebar.contains(event.target) || sidebarToggle.contains(event.target);
                
                if (!isClickInside && window.innerWidth < 1024 && sidebar.classList.contains('translate-x-0')) {
                    sidebar.classList.remove('translate-x-0');
                    sidebar.classList.add('-translate-x-full');
                }
            });
            
            // Resize handler
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.classList.add('translate-x-0');
                } else {
                    sidebar.classList.remove('translate-x-0');
                    sidebar.classList.add('-translate-x-full');
                }
            });
        });
    </script>

    @stack('scripts')
    
</body>
</html>
