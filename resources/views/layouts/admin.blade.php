<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Manuflow') }} - @yield('title', 'Dashboard')</title>
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/file-upload-preview.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.tailwindcss.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.tailwindcss.min.css">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.min.css" rel="stylesheet">
    
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
            <main class="py-8 lg:py-10">
                <div class="px-6 sm:px-8 lg:px-12 max-w-7xl mx-auto">
                    <!-- Page header -->
                    @hasSection('header')
                        <div class="mb-8 lg:mb-10">
                            <div class="md:flex md:items-center md:justify-between">
                                <div class="min-w-0 flex-1">
                                    <h1 class="text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:text-4xl sm:tracking-tight">
                                        @yield('header')
                                    </h1>
                                    @hasSection('description')
                                        <p class="mt-2 text-base text-gray-600">@yield('description')</p>
                                    @endif
                                </div>
                                @hasSection('actions')
                                    <div class="mt-6 flex md:ml-4 md:mt-0 space-x-3">
                                        @yield('actions')
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    <!-- Flash Messages -->
                    @include('layouts.partials.flash-messages')
                    
                    <!-- Page content -->
                    <div class="space-y-8">
                        @yield('content')
                    </div>
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
    
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js"></script>
    
    <!-- Custom Select2 Styling -->
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            border-color: #d1d5db !important;
            border-radius: 0.5rem !important;
            padding: 0.5rem 0.75rem !important;
            min-height: 2.5rem !important;
        }
        
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            color: #374151 !important;
            padding-left: 0 !important;
            line-height: 1.5rem !important;
        }
        
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
            height: 2.25rem !important;
            right: 0.75rem !important;
        }
        
        .select2-container--bootstrap-5.select2-container--focus .select2-selection {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2) !important;
        }
        
        .select2-dropdown {
            border-color: #d1d5db !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        }
        
        .select2-results__option--highlighted {
            background-color: #6366f1 !important;
        }
    </style>
    
    @stack('scripts')
</body>
</html>