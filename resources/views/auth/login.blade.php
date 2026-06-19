<x-guest-layout>
    <div class="mb-5">
        <h1 class="text-sm font-semibold text-workspace-text">Sign in to TeamSync</h1>
        <p class="text-xs text-workspace-secondary mt-0.5">Access your workspace</p>
    </div>

    <x-auth-session-status class="mb-4 text-xs text-workspace-success" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-0.5" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="mt-0.5" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2">
                <input id="remember_me" type="checkbox" class="rounded border-workspace-border bg-workspace-background text-workspace-accent focus:ring-workspace-accent" name="remember">
                <span class="text-xs text-workspace-secondary">{{ __('Remember me') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-xs text-workspace-secondary hover:text-workspace-text transition-colors" href="{{ route('password.request') }}">{{ __('Forgot password?') }}</a>
            @endif
        </div>

        <x-primary-button class="w-full justify-center">{{ __('Sign In') }}</x-primary-button>
    </form>
</x-guest-layout>
