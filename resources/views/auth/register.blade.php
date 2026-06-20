<x-guest-layout>
    <div class="mb-8 flex flex-col items-center text-center">
        <a href="/" class="flex flex-col items-center group">
            <x-logo class="h-10 w-auto mb-3 transition-transform group-hover:scale-105 duration-300 rounded-xl overflow-hidden shadow-sm" />
            <h1 class="text-xl font-bold tracking-tight" style="color:var(--text)">TeamSync</h1>
        </a>
        <p class="text-sm mt-2" style="color:var(--secondary)">Join your team workspace</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        {{-- Name --}}
        <div>
            <label class="ts-label" for="name">Full Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}"
                   class="ts-input" required autofocus autocomplete="name"
                   placeholder="Alex Chen">
            @error('name')
                <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label class="ts-label" for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="ts-input" required autocomplete="username"
                   placeholder="you@example.com">
            @error('email')
                <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label class="ts-label" for="password">Password</label>
            <div class="relative">
                <input id="password" type="password" name="password"
                       class="ts-input pr-9" required autocomplete="new-password"
                       placeholder="••••••••">
                <button type="button" class="pw-toggle-btn" data-pw-toggle="password" aria-label="Toggle password visibility">
                    <svg class="eye-open w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg class="eye-closed w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div>
            <label class="ts-label" for="password_confirmation">Confirm Password</label>
            <div class="relative">
                <input id="password_confirmation" type="password" name="password_confirmation"
                       class="ts-input pr-9" required autocomplete="new-password"
                       placeholder="••••••••">
                <button type="button" class="pw-toggle-btn" data-pw-toggle="password_confirmation" aria-label="Toggle password visibility">
                    <svg class="eye-open w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg class="eye-closed w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            @error('password_confirmation')
                <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="ts-btn-primary w-full justify-center">Create Account</button>

        <p class="text-center text-xs" style="color:var(--secondary)">
            Already have an account?
            <a href="{{ route('login') }}" style="color:var(--accent)" class="font-medium">Sign in</a>
        </p>
    </form>
</x-guest-layout>
