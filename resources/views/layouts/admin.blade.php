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

    <style>
        /* Modern, unified DataTables pagination styling */
        .dataTables_wrapper .dataTables_paginate {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.25rem;
            padding-top: 1rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 2.25rem;
            min-width: 2.25rem;
            padding: 0 0.75rem;
            margin: 0 2px;
            border: 1px solid #e5e7eb !important; /* gray-200 */
            border-radius: 9999px; /* full */
            background: #ffffff !important;
            color: #374151 !important; /* gray-700 */
            font-size: 0.875rem; /* text-sm */
            line-height: 1;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
            transition: background-color .2s ease, border-color .2s ease, color .2s ease, box-shadow .2s ease;
            cursor: pointer;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled):not(.current) {
            background: #f9fafb !important; /* gray-50 */
            border-color: #d1d5db !important; /* gray-300 */
            color: #111827 !important; /* gray-900 */
            cursor: pointer;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #4F46E5 !important; /* primary-600 */
            border-color: #4F46E5 !important;
            color: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(79,70,229,0.25);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:focus {
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(79,70,229,0.35);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            cursor: default !important;
            background: #f3f4f6 !important; /* gray-100 */
            border-color: #e5e7eb !important; /* gray-200 */
            color: #9ca3af !important; /* gray-400 */
            box-shadow: none !important;
            opacity: 0.8;
        }

        /* Previous/Next adornments */
        .dataTables_wrapper .dataTables_paginate .paginate_button.previous::before {
            content: '\2039'; /* ‹ */
            display: inline-block;
            margin-right: .375rem;
            font-size: .9em;
            line-height: 1;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.next::after {
            content: '\203A'; /* › */
            display: inline-block;
            margin-left: .375rem;
            font-size: .9em;
            line-height: 1;
        }

        /* Ellipsis style for long pagination ranges */
        .dataTables_wrapper .dataTables_paginate span.ellipsis {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 2.25rem;
            min-width: 2.25rem;
            padding: 0 0.5rem;
            margin: 0 2px;
            color: #6b7280; /* gray-500 */
        }
    </style>
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
    
        <script>
        (function () {
            function initSubmitSpinners() {
                var lastSubmitterByForm = new WeakMap();
                function rememberFromEvent(e) {
                    var el = e.target && e.target.closest ? e.target.closest('button[type="submit"], input[type="submit"]') : null;
                    if (!el) return;
                    var form = el.form || (el.closest && el.closest('form'));
                    if (form) lastSubmitterByForm.set(form, el);
                }
                function rememberOnEnter(e) {
                    if (e.key !== 'Enter') return;
                    var target = e.target && e.target.closest ? e.target : null;
                    if (!target) return;
                    var form = target.closest && target.closest('form');
                    if (!form) return;
                    var cand = form.querySelector('button[type="submit"]:not([disabled]), input[type="submit"]:not([disabled])');
                    if (cand) lastSubmitterByForm.set(form, cand);
                }
                document.addEventListener('click', rememberFromEvent, true);
                document.addEventListener('mousedown', rememberFromEvent, true);
                document.addEventListener('keydown', rememberOnEnter, true);
                document.addEventListener('submit', function (e) {
                    var form = e.target && e.target.tagName === 'FORM' ? e.target : null;
                    if (!form) return;
                    if (e.defaultPrevented) return;
                    if (typeof form.checkValidity === 'function' && !form.checkValidity()) return;
                    var btn = e.submitter || lastSubmitterByForm.get(form) || form.querySelector('button[type="submit"], input[type="submit"]');
                    if (!btn) return;
                    if (btn.hasAttribute('data-spinner-active') || btn.hasAttribute('data-no-spinner')) return;
                    var submits = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                    submits.forEach(function (el) { el.disabled = true; });
                    btn.dataset.spinnerActive = 'true';
                    btn.setAttribute('aria-busy', 'true');
                    var spinnerSvg = '<svg class="animate-spin h-5 w-5 text-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">' +
                                                     '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
                                                     '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>' +
                                                     '</svg>';
                    if (btn.tagName === 'BUTTON') {
                        if (!btn.dataset.originalHtml) btn.dataset.originalHtml = btn.innerHTML;
                        var w = btn.offsetWidth, h = btn.offsetHeight;
                        btn.style.width = w + 'px';
                        btn.style.height = h + 'px';
                        btn.classList.add('items-center','justify-center');
                        btn.innerHTML = spinnerSvg;
                    } else if (btn.tagName === 'INPUT') {
                        var w2 = btn.offsetWidth, h2 = btn.offsetHeight;
                        if (!btn.dataset.originalHidden) btn.dataset.originalHidden = btn.style.display || '';
                        var clone = document.createElement('button');
                        clone.type = 'button';
                        clone.disabled = true;
                        clone.className = btn.className;
                        clone.style.width = w2 + 'px';
                        clone.style.height = h2 + 'px';
                        clone.classList.add('flex','items-center','justify-center');
                        clone.innerHTML = spinnerSvg;
                        btn.style.display = 'none';
                        if (btn.parentNode) btn.parentNode.insertBefore(clone, btn.nextSibling);
                    }
                }, { capture: true });
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initSubmitSpinners, { once: true });
            } else {
                initSubmitSpinners();
            }
        })();

            (function () {
                    $('.select2').select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        placeholder: 'Select an option',
                        allowClear: true,
                    });
                })();
        </script>
    @stack('scripts')
</body>
</html>