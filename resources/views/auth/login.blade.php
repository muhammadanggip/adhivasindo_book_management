<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-4">
        <div class="mx-auto mb-3" style="width: 4rem; height: 4rem; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <img src="{{ asset('logo.webp') }}" alt="Logo" class="w-100 h-100" style="object-fit: cover;">
        </div>
        <h2 class="h4 mb-2 text-gradient">Welcome Back</h2>
        <p class="text-muted">Sign in to your account to continue</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
            <x-input-label for="email" :value="__('Email Address')" />
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-envelope text-muted"></i>
                </span>
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" class="border-start-0" />
            </div>
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div class="mb-4">
            <x-input-label for="password" :value="__('Password')" />
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-lock text-muted"></i>
                </span>
                <x-text-input id="password" type="password" name="password" required autocomplete="current-password" class="border-start-0" />
            </div>
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Remember Me -->
        <div class="mb-4 form-check">
            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
            <label for="remember_me" class="form-check-label">
                {{ __('Remember me') }}
            </label>
        </div>

        <div class="d-grid gap-2">
            <x-primary-button class="btn-lg">
                <i class="bi bi-box-arrow-in-right me-2"></i>{{ __('Sign In') }}
            </x-primary-button>
        </div>

        <div class="text-center mt-3">
            @if (Route::has('password.request'))
                <a class="text-decoration-none text-muted" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>
    </form>
</x-guest-layout>
