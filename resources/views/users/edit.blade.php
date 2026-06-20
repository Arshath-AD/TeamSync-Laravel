<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard', ['section' => 'members', 'tab' => 'admin']) }}" class="ts-btn-secondary text-xs px-2 py-1">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Resource Directory
            </a>
            <span>Edit User: {{ $user->name }}</span>
        </div>
    </x-slot>

    <div class="fade-in max-w-2xl mx-auto">
        <form action="{{ route('users.update', $user) }}" method="POST" class="ts-card p-5 sm:p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="ts-label">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="ts-input" required>
                @error('name') <span class="text-xs text-[var(--danger)] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="email" class="ts-label">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="ts-input" required>
                @error('email') <span class="text-xs text-[var(--danger)] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="ts-label mb-2 block">Role</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 text-sm text-[var(--text)] cursor-pointer">
                        <input type="radio" name="role" value="admin" {{ old('role', $user->role) === 'admin' ? 'checked' : '' }} {{ $user->id === Auth::id() ? 'disabled' : '' }} class="text-[var(--accent)] bg-[var(--elevated)] border-[var(--border)] focus:ring-[var(--accent)]">
                        <span>Admin</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm text-[var(--text)] cursor-pointer">
                        <input type="radio" name="role" value="user" {{ old('role', $user->role) === 'user' ? 'checked' : '' }} {{ $user->id === Auth::id() ? 'disabled' : '' }} class="text-[var(--accent)] bg-[var(--elevated)] border-[var(--border)] focus:ring-[var(--accent)]">
                        <span>User</span>
                    </label>
                </div>
                @if($user->id === Auth::id())
                    <input type="hidden" name="role" value="{{ $user->role }}">
                    <span class="text-xs text-[var(--secondary)] mt-1 block">You cannot change your own role.</span>
                @endif
                @error('role') <span class="text-xs text-[var(--danger)] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <hr class="border-[var(--border)]">

            <div>
                <label class="ts-section-title mb-2 block">Change Password (Optional)</label>
                <p class="text-xs text-[var(--secondary)] mb-3">Leave blank if you do not want to change the password.</p>
                
                <div class="space-y-4">
                    <div>
                        <label for="password" class="ts-label">New Password</label>
                        <input type="password" name="password" id="password" class="ts-input">
                        @error('password') <span class="text-xs text-[var(--danger)] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="ts-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="ts-input">
                    </div>
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="ts-btn-primary">Update User</button>
            </div>
        </form>
    </div>
</x-app-layout>
