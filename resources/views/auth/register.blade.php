<x-guest-layout>
    <div class="mb-5 text-center">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 text-blue-600 mb-2">
            <i class="bi bi-shield-lock-fill text-2xl"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-1" style="font-family: 'Montserrat', sans-serif;">Administrator Registration</h3>
        <p class="text-xs text-gray-500">Create the primary administrative account for AquaTank</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-4">
            <label for="name" class="block font-semibold text-xs text-gray-700 uppercase tracking-wider mb-2">
                <i class="bi bi-person text-primary-blue me-1"></i> Full Name
            </label>
            <input id="name" class="auth-input" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="e.g. Maria Santos" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-xs text-red-500" />
        </div>

        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="block font-semibold text-xs text-gray-700 uppercase tracking-wider mb-2">
                <i class="bi bi-envelope text-primary-blue me-1"></i> Email Address
            </label>
            <input id="email" class="auth-input" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="e.g. admin@barangay.gov.ph" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-red-500" />
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="block font-semibold text-xs text-gray-700 uppercase tracking-wider mb-2">
                <i class="bi bi-lock text-primary-blue me-1"></i> Password
            </label>
            <input id="password" class="auth-input"
                            type="password"
                            name="password"
                            required autocomplete="new-password"
                            placeholder="Create a secure password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-red-500" />
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="block font-semibold text-xs text-gray-700 uppercase tracking-wider mb-2">
                <i class="bi bi-shield-check text-primary-blue me-1"></i> Confirm Password
            </label>
            <input id="password_confirmation" class="auth-input"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password"
                            placeholder="Re-enter your password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-xs text-red-500" />
        </div>

        <div class="mt-5">
            <button type="submit" class="btn-primary-custom">
                Register Administrator <i class="bi bi-arrow-right"></i>
            </button>
        </div>

        <div class="flex items-center justify-center mt-4">
            <a class="text-xs text-gray-500 hover:text-blue-600 font-medium text-decoration-none" href="{{ route('login') }}">
                Already have an account? <span class="text-blue-600 font-semibold">Sign In</span>
            </a>
        </div>
    </form>
</x-guest-layout>
