<x-guest-layout>
    <!-- Page Title -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Verify your email</h2>
        <p class="mt-2 text-sm text-gray-600">
            We've sent a verification link to your email address.
        </p>
    </div>

    <!-- Verification Message -->
    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-envelope text-yellow-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-800">
                    {{ __('Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just emailed to you.') }}
                </p>
            </div>
        </div>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-800">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="space-y-4">
        <!-- Resend Verification Button -->
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" 
                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200 shadow-sm">
                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                    <i class="fas fa-paper-plane text-primary-500 group-hover:text-primary-400 transition-colors duration-200"></i>
                </span>
                {{ __('Resend verification email') }}
            </button>
        </form>

        <!-- Logout Button -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                class="group relative w-full flex justify-center py-3 px-4 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200 shadow-sm">
                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                    <i class="fas fa-sign-out-alt text-gray-400 group-hover:text-gray-500 transition-colors duration-200"></i>
                </span>
                {{ __('Sign out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
