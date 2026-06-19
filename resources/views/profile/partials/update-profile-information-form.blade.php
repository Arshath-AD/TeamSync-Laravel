<section>
    <header class="mb-4 pb-3 border-b border-workspace-border">
        <h2 class="text-sm font-semibold text-workspace-text">{{ __('Profile Information') }}</h2>
        <p class="text-xs text-workspace-secondary mt-0.5">{{ __("Update your account's profile information and email address.") }}</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-0.5" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-0.5" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <p class="text-xs mt-2 text-workspace-secondary">
                    {{ __('Your email address is unverified.') }}
                    <button form="send-verification" class="text-workspace-accent hover:underline">{{ __('Re-send verification') }}</button>
                </p>
                @if (session('status') === 'verification-link-sent')
                    <p class="mt-1 text-xs text-workspace-success">{{ __('A new verification link has been sent.') }}</p>
                @endif
            @endif
        </div>

        <div class="ws-form-actions !justify-start">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)" class="text-xs text-workspace-success">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
