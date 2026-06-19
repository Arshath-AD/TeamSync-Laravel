<x-app-layout>
    <x-slot name="header">
        @if(request('section') === 'members')
            Resource Directory
        @else
            Command Center
        @endif
    </x-slot>

    @if(session('success'))
        <div class="mb-4 bg-workspace-elevated border border-workspace-success text-workspace-success px-3 py-2 rounded text-xs font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if(request('section') === 'members')
        <x-workspace.members-directory />
    @elseif($user->role === 'admin')
        @include('dashboard.partials._admin')
    @else
        @include('dashboard.partials._user')
    @endif
</x-app-layout>
