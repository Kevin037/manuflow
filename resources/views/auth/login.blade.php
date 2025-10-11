<x-guest-layout>
    <!-- Page Title -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Sign in to your account</h2>
        <p class="mt-2 text-sm text-gray-600">
            Access your manufacturing dashboard
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email address')" class="block text-sm font-medium text-gray-700" />
            <div class="mt-1">
                <x-text-input id="email" 
                    class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm transition-colors duration-200" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autofocus 
                    autocomplete="username"
                    placeholder="Enter your email address" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="block text-sm font-medium text-gray-700" />
            <div class="mt-1">
                <x-text-input id="password" 
                    class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm transition-colors duration-200"
                    type="password"
                    name="password"
                    required 
                    autocomplete="current-password"
                    placeholder="Enter your password" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input id="remember_me" 
                    type="checkbox" 
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded transition-colors duration-200" 
                    name="remember">
                <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                    {{ __('Remember me') }}
                </label>
            </div>

            @if (Route::has('password.request'))
                <div class="text-sm">
                    <a href="{{ route('password.request') }}" 
                        class="font-medium text-primary-600 hover:text-primary-500 transition-colors duration-200">
                        {{ __('Forgot your password?') }}
                    </a>
                </div>
            @endif
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit" data-no-spinner
                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200 shadow-sm">
                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                    <i class="fas fa-sign-in-alt text-primary-500 group-hover:text-primary-400 transition-colors duration-200"></i>
                </span>
                {{ __('Sign in') }}
            </button>
        </div>

        <!-- Register Link -->
        <div class="mt-6">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300" />
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Don't have an account?</span>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('register') }}" 
                    class="group relative w-full flex justify-center py-3 px-4 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200 shadow-sm">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-user-plus text-gray-400 group-hover:text-gray-500 transition-colors duration-200"></i>
                    </span>
                    {{ __('Create new account') }}
                </a>
            </div>
        </div>
    </form>
</x-guest-layout>

<script>
// Page-scoped, reliable submit spinner for the Login form
(function () {
    function attachLoginSpinner() {
        var form = document.querySelector('form[action*="login"], form[action="{{ route('login') }}"]');
        if (!form) return;

        var lastSubmitter = null;
        function rememberFromEvent(e) {
            var t = e.target && e.target.closest ? e.target.closest('button[type="submit"], input[type="submit"]') : null;
            if (!t) return;
            var f = t.form || (t.closest && t.closest('form'));
            if (f === form) lastSubmitter = t;
        }
        function rememberOnEnter(e) {
            if (e.key !== 'Enter') return;
            var target = e.target && e.target.closest ? e.target : null;
            if (!target) return;
            var f = target.closest && target.closest('form');
            if (f !== form) return;
            var cand = form.querySelector('button[type="submit"]:not([disabled]), input[type="submit"]:not([disabled])');
            if (cand) lastSubmitter = cand;
        }

        document.addEventListener('click', rememberFromEvent, true);
        document.addEventListener('mousedown', rememberFromEvent, true);
        document.addEventListener('keydown', rememberOnEnter, true);

        form.addEventListener('submit', function (e) {
            if (e.defaultPrevented) return;
            if (typeof form.checkValidity === 'function' && !form.checkValidity()) return;

            var btn = (e.submitter) ? e.submitter : (lastSubmitter || form.querySelector('button[type="submit"], input[type="submit"]'));
            if (!btn) return;

            // Skip if opted out or already active
            if (btn.hasAttribute('data-spinner-active')) return;

            // Disable all submits in form to prevent alternate submits
            var submits = form.querySelectorAll('button[type="submit"], input[type="submit"]');
            submits.forEach(function (el) { el.disabled = true; });

            // Mark active
            btn.dataset.spinnerActive = 'true';
            btn.setAttribute('aria-busy', 'true');

            // Spinner SVG (Tailwind animate-spin)
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
        document.addEventListener('DOMContentLoaded', attachLoginSpinner, { once: true });
    } else {
        attachLoginSpinner();
    }
})();
</script>
