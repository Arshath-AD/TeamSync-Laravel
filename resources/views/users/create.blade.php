<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard', ['section' => 'members', 'tab' => 'admin']) }}" class="ts-btn-secondary text-xs px-2 py-1">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Resource Directory
            </a>
            <span>Create New User</span>
        </div>
    </x-slot>

    <div class="fade-in max-w-2xl mx-auto">
        <form action="{{ route('users.store') }}" method="POST" class="ts-card p-5 sm:p-6 space-y-5">
            @csrf

            <div>
                <label for="name" class="ts-label">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="ts-input" required autofocus>
                @error('name') <span class="text-xs text-[var(--danger)] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="email" class="ts-label">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="ts-input" required>
                @error('email') <span class="text-xs text-[var(--danger)] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="password" class="ts-label">Password</label>
                <input type="password" name="password" id="password" class="ts-input" required>
                @error('password') <span class="text-xs text-[var(--danger)] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="ts-label">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="ts-input" required>
            </div>

            <div>
                <label for="role" class="ts-label">Role</label>
                <select name="role" id="role" class="ts-input" required>
                    <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role') <span class="text-xs text-[var(--danger)] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="ts-btn-primary">Create User</button>
            </div>
        </form>
    </div>
</x-app-layout>
