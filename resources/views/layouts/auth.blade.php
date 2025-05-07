<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SOKONI')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        /* Animation for floating elements */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        /* Animated background gradient */
        @keyframes gradient-animation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .animated-gradient {
            background-size: 200% 200%;
            animation: gradient-animation 15s ease infinite;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-900">
        <!-- Animated background elements -->
        <div class="fixed inset-0 overflow-hidden z-0 opacity-20">
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-green-600 rounded-full mix-blend-multiply filter blur-3xl animate-float opacity-40"></div>
            <div class="absolute top-1/3 right-1/4 w-96 h-96 bg-lime-600 rounded-full mix-blend-multiply filter blur-3xl animate-float opacity-30" style="animation-delay: 2s;"></div>
            <div class="absolute bottom-1/4 right-1/3 w-64 h-64 bg-yellow-600 rounded-full mix-blend-multiply filter blur-3xl animate-float opacity-25" style="animation-delay: 4s;"></div>
        </div>

        <div class="relative z-10">
            @yield('content')
        </div>
    </div>
</body>
</html>