<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Manuflow') }} - @yield('title', 'Dashboard')</title>
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.tailwindcss.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.tailwindcss.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @stack('styles')
</head>
<body class="h-full bg-gray-50" x-data="{ sidebarOpen: false }">
    <div class="min-h-full">
        <!-- Navigation -->
        @include('layouts.partials.navbar')
        
        <!-- Sidebar -->
        @include('layouts.partials.sidebar')
        
        <!-- Main content -->
        <div class="lg:pl-sidebar transition-all duration-300 ease-in-out">
            <main class="py-6 lg:py-8">
                <div class="px-4 sm:px-6 lg:px-8">
                    <!-- Page header -->
                    @hasSection('header')
                        <div class="mb-6 lg:mb-8">
                            <div class="md:flex md:items-center md:justify-between">
                                <div class="min-w-0 flex-1">
                                    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                                        @yield('header')
                                    </h1>
                                    @hasSection('description')
                                        <p class="mt-1 text-sm text-gray-500">@yield('description')</p>
                                    @endif
                                </div>
                                @hasSection('actions')
                                    <div class="mt-4 flex md:ml-4 md:mt-0 space-x-3">
                                        @yield('actions')
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    <!-- Flash Messages -->
                    @include('layouts.partials.flash-messages')
                    
                    <!-- Page content -->
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.tailwindcss.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    
    @stack('scripts')
</body>
</html>