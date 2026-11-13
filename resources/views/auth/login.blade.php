<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (session('error'))
        <div class="p-3 mb-4 rounded-xl border border-red-200 bg-red-50 text-red-700 text-xs font-medium">
            <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
        </div>
    @endif

    @if (\App\Models\User::count() === 0)
        <div class="p-4 mb-4 rounded-xl border border-blue-200 bg-blue-50 text-blue-900 shadow-sm">
            <div class="flex items-start gap-3">
                <i class="bi bi-shield-lock-fill text-xl text-blue-600 mt-0.5"></i>
                <div class="text-xs">
                    <p class="font-bold text-sm mb-1 text-blue-950">Initial Setup Required</p>
                    <p class="mb-2 text-blue-800 leading-relaxed">No administrator accounts currently exist. Please set up the primary administrator account to begin using AquaTank.</p>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 font-bold text-blue-700 hover:text-blue-900 underline text-xs">
                        Create Initial Administrator <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div class="mb-5 text-center">
        <h3 class="text-lg font-bold text-gray-900 mb-1" style="font-family: 'Montserrat', sans-serif;">Staff Authentication</h3>
        <p class="text-xs text-gray-500">Sign in with your administrative credentials</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="block font-semibold text-xs text-gray-700 uppercase tracking-wider mb-2">
                <i class="bi bi-envelope text-primary-blue me-1"></i> Email Address
            </label>
            <input id="email" class="auth-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Enter your administrator email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-red-500" />
        </div>

        <!-- Password -->
        <div class="mb-4">
            <div class="flex items-center justify-between mb-2">
                <label for="password" class="block font-semibold text-xs text-gray-700 uppercase tracking-wider">
                    <i class="bi bi-lock text-primary-blue me-1"></i> Password
                </label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-medium text-decoration-none" style="color: #4a6cf7;" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>

            <input id="password" class="auth-input"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="Enter password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-red-500" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between my-4">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 w-4 h-4 cursor-pointer" name="remember">
                <span class="ms-2 text-xs text-gray-600 font-medium">Keep me signed in</span>
            </label>
        </div>

        <div class="mt-5">
            <button type="submit" class="btn-primary-custom">
                Sign In to Dashboard <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </form>
</x-guest-layout>
