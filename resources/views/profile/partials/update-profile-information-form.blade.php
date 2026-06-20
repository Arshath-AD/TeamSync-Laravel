
<form method="post" action="{{ route('profile.update') }}" class="space-y-4">
    @csrf
    @method('patch')

    <div>
        <label class="ts-label" for="name">Name</label>
        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
               class="ts-input" required autofocus autocomplete="name">
        @error('name') <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="ts-label" for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
               class="ts-input" required autocomplete="username">
        @error('email') <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p> @enderror

    </div>

    <div class="flex items-center gap-3 pt-1">
        <button type="submit" class="ts-btn-primary">Save Changes</button>
        @if (session('status') === 'profile-updated')
            <p x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)"
               class="text-xs" style="color:var(--success)">✓ Saved.</p>
        @endif
    </div>
</form>
