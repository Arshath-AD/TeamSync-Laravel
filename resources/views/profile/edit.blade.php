<x-app-layout>
    <x-slot name="header">Profile</x-slot>

    <div class="space-y-4 max-w-xl">
        <x-workspace.form-panel>
            @include('profile.partials.update-profile-information-form')
        </x-workspace.form-panel>

        <x-workspace.form-panel>
            @include('profile.partials.update-password-form')
        </x-workspace.form-panel>

        <x-workspace.form-panel>
            @include('profile.partials.delete-user-form')
        </x-workspace.form-panel>
    </div>
</x-app-layout>
