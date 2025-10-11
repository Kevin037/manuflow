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
                </script>
    </body>
</html>
