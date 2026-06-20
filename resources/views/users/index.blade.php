<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full gap-3">
            <span>User Management</span>
            <a href="{{ route('users.create') }}" class="ts-btn-primary">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New User
            </a>
        </div>
    </x-slot>

    <div class="space-y-4 fade-in">
        {{-- Search & Controls --}}
        <div class="flex flex-col sm:flex-row gap-3 items-center justify-between">
            <form action="{{ route('users.index') }}" method="GET" class="w-full sm:w-1/3 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="ts-input w-full pl-9">
                <svg class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </form>
        </div>

        {{-- Users Table --}}
        <div class="ts-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-[var(--secondary)]">
                    <thead class="text-xs uppercase bg-[var(--elevated)] border-b border-[var(--border)] text-[var(--text)]">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Name</th>
                            <th class="px-4 py-3 font-semibold">Email</th>
                            <th class="px-4 py-3 font-semibold">Role</th>
                            <th class="px-4 py-3 font-semibold text-center">Projects</th>
                            <th class="px-4 py-3 font-semibold text-center">Tasks</th>
                            <th class="px-4 py-3 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        @forelse($users as $user)
                        <tr class="hover:bg-[var(--elevated)] transition-colors">
                            <td class="px-4 py-3 font-medium text-[var(--text)]">{{ $user->name }}</td>
                            <td class="px-4 py-3">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                @if($user->role === 'admin')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-[var(--accent-dim)] text-[var(--accent)] border border-[var(--accent)]" style="border-color: rgba(59,130,246,0.3)">Admin</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-[var(--elevated)] text-[var(--secondary)] border border-[var(--border)]">User</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">{{ $user->projects_count }}</td>
                            <td class="px-4 py-3 text-center">{{ $user->tasks_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('users.edit', $user) }}" class="ts-btn-secondary text-xs px-2 py-1">Edit</a>
                                    
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this user? Their led projects and tasks will be reassigned/unassigned.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ts-btn-danger text-xs px-2 py-1" {{ $user->id === Auth::id() ? 'disabled' : '' }} title="{{ $user->id === Auth::id() ? 'You cannot delete yourself' : 'Delete User' }}">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center ts-empty">
                                No users found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
