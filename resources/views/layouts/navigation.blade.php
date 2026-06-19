<nav class="w-56 bg-workspace-surface border-r border-workspace-border flex flex-col h-full flex-shrink-0">
    <div class="h-12 flex items-center px-4 border-b border-workspace-border">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
            <div class="h-5 w-5 bg-workspace-accent rounded flex items-center justify-center text-white font-bold text-[9px] group-hover:bg-indigo-400 transition-colors">TS</div>
            <span class="font-bold text-workspace-text text-sm tracking-tight">TeamSync</span>
        </a>
    </div>

    <div class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5">
        @php
            $links = [
                ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'active' => request()->routeIs('dashboard') && request('section') !== 'members'],
                ['route' => 'projects.index', 'label' => 'Projects', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'active' => request()->routeIs('projects.*')],
                ['route' => 'tasks.index', 'label' => 'Tasks', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'active' => request()->routeIs('tasks.index') || request()->routeIs('tasks.board')],
                ['route' => 'tasks.myTasks', 'label' => 'My Tasks', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'active' => request()->routeIs('tasks.myTasks')],
            ];
        @endphp

        @foreach($links as $link)
            <a href="{{ route($link['route']) }}"
               class="flex items-center gap-2.5 px-2.5 py-1.5 rounded text-xs font-medium transition-colors {{ $link['active'] ? 'bg-workspace-elevated text-workspace-text border border-workspace-border' : 'text-workspace-secondary hover:bg-workspace-elevated/50 hover:text-workspace-text' }}">
                <svg class="w-3.5 h-3.5 {{ $link['active'] ? 'text-workspace-accent' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"></path>
                </svg>
                {{ $link['label'] }}
            </a>
        @endforeach

        <a href="{{ route('dashboard', ['section' => 'members']) }}"
           class="flex items-center gap-2.5 px-2.5 py-1.5 rounded text-xs font-medium transition-colors {{ request('section') === 'members' ? 'bg-workspace-elevated text-workspace-text border border-workspace-border' : 'text-workspace-secondary hover:bg-workspace-elevated/50 hover:text-workspace-text' }}">
            <svg class="w-3.5 h-3.5 {{ request('section') === 'members' ? 'text-workspace-accent' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            Members
        </a>
    </div>

    <div class="p-3 border-t border-workspace-border">
        <div class="text-[10px] text-workspace-secondary uppercase tracking-wider mb-1">Workspace</div>
        <div class="text-[10px] text-workspace-secondary/60">TeamSync v1.0</div>
    </div>
</nav>
