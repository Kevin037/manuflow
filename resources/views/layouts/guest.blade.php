<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Manuflow') }} - @yield('title', 'Authentication')</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body class="h-full bg-gray-50 antialiased">
        <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="sm:mx-auto sm:w-full sm:max-w-md">
                <div class="text-center">
                    <!-- Company Logo/Name -->
                    <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-xl bg-primary-600 shadow-lg">
                        <i class="fas fa-industry text-2xl text-white"></i>
                    </div>
                    <h1 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">
                        {{ config('app.name', 'Manuflow') }}
                    </h1>
                    <p class="mt-2 text-center text-sm text-gray-600">
                        Manufacturing ERP & Inventory Control
                    </p>
                </div>
            </div>

            <!-- Auth Form Container -->
            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                <div class="bg-white py-8 px-4 shadow-lg sm:rounded-xl sm:px-10 border border-gray-100">
                    {{ $slot }}
                </div>
            </div>
            
            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-xs text-gray-500">
                    © {{ date('Y') }} {{ config('app.name', 'Manuflow') }}. All rights reserved.
                </p>
            </div>
        </div>
    </body>
</html>
