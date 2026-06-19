<section>
    <header class="mb-4 pb-3 border-b border-workspace-border">
        <h2 class="text-sm font-semibold text-workspace-text">{{ __('Delete Account') }}</h2>
        <p class="text-xs text-workspace-secondary mt-0.5">{{ __('Permanently delete your account and all associated data.') }}</p>
    </header>

    <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">{{ __('Delete Account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-5">
            @csrf
            @method('delete')

            <h2 class="text-sm font-semibold text-workspace-text">{{ __('Delete your account?') }}</h2>
            <p class="mt-1 text-xs text-workspace-secondary">{{ __('Enter your password to confirm permanent deletion.') }}</p>

            <div class="mt-4">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />
                <x-text-input id="password" name="password" type="password" class="mt-0.5" placeholder="{{ __('Password') }}" />
                <x-input-error :messages="$errors->userDeletion->get('password')" />
            </div>

            <div class="mt-5 flex justify-end gap-2">
                <x-secondary-button x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                <x-danger-button>{{ __('Delete Account') }}</x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
