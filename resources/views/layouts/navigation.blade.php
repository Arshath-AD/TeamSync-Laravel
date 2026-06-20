{{-- Sidebar: compact, glassmorphism, workspace-first --}}
<nav class="ts-sidebar w-52 flex flex-col h-full flex-shrink-0 z-20">

    {{-- Logo / workspace header --}}
    <div class="ts-sidebar-header h-14 flex items-center px-3 flex-shrink-0" style="border-bottom:1px solid var(--glass-border)">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group w-full overflow-visible" id="sidebar-logo-link">
            <x-logo class="h-8 w-auto flex-shrink-0" />
            <span class="font-bold text-[15px] tracking-tight nav-label" style="color:var(--text)">TeamSync</span>
        </a>
    </div>

    {{-- Navigation --}}
    <div class="flex-1 overflow-y-auto ts-scroll py-2 px-2 space-y-0.5">

        @php
            $role = Auth::user()->role ?? 'user';

            $navItems = [
                [
                    'route'  => 'dashboard',
                    'label'  => 'Dashboard',
                    'active' => request()->routeIs('dashboard') && request('section') !== 'members',
                    'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 22V12h6v10"/>',
                ],
                [
                    'route'  => 'projects.index',
                    'label'  => 'Projects',
                    'active' => request()->routeIs('projects.*'),
                    'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7h18M3 12h18M3 17h18"/><rect x="3" y="3" width="18" height="18" rx="2" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" fill="none"/>',
                ],
                [
                    'route'  => 'tasks.index',
                    'label'  => 'Tasks',
                    'active' => request()->routeIs('tasks.index') || request()->routeIs('tasks.board'),
                    'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>',
                ],
                [
                    'route'  => 'tasks.myTasks',
                    'label'  => 'My Tasks',
                    'active' => request()->routeIs('tasks.myTasks'),
                    'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
                ],
            ];
        @endphp

        @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}"
               class="ts-nav-item {{ $item['active'] ? 'active' : '' }}">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $item['icon'] !!}
                </svg>
                <span class="nav-label">{{ $item['label'] }}</span>
            </a>
        @endforeach

        @if($role === 'admin')
            {{-- Separator --}}
            <div class="px-2 pt-3 pb-1 nav-text-hide">
                <div class="text-[10px] font-semibold uppercase tracking-widest nav-label" style="color:var(--muted)">Team</div>
            </div>

            <a href="{{ route('dashboard', ['section' => 'members']) }}"
               class="ts-nav-item {{ request('section') === 'members' ? 'active' : '' }}">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="nav-label">Resource Directory</span>
            </a>
        @endif

    </div>
    {{-- end nav scroll area --}}

    {{-- Collapse button pinned to bottom --}}
    <div class="px-2 py-1 flex-shrink-0" style="border-top:1px solid var(--glass-border)">
        <button onclick="toggleSidebar()" class="ts-nav-item w-full bg-transparent border-none focus:outline-none" style="justify-content: flex-start;">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                <line x1="9" x2="9" y1="3" y2="21"/>
                <path d="m16 15-3-3 3-3"/>
            </svg>
            <span class="nav-label">Collapse</span>
        </button>
    </div>

    {{-- Unified User Card & Attribution --}}
    <div class="flex-shrink-0" style="border-top:1px solid var(--glass-border); background:var(--surface)">
        <a href="{{ route('profile.edit') }}"
           class="px-3 py-3 flex flex-col gap-3 transition-colors group"
           onmouseover="this.style.background='var(--elevated)'" onmouseout="this.style.background=''"
           title="View your profile">
            
            <div class="flex items-center gap-2">
                <img src="{{ Auth::user()->avatarUrl() }}" alt="{{ Auth::user()->name }}"
                     class="w-7 h-7 rounded-full object-cover flex-shrink-0"
                     onerror="this.src='{{ asset('images/default-avatar.jpg') }}'">
                <div class="min-w-0 flex-1 nav-label">
                    <div class="text-[11px] font-medium truncate" style="color:var(--text)">{{ Auth::user()->name }}</div>
                    <div class="text-[10px] capitalize" style="color:var(--secondary)">{{ Auth::user()->role ?? 'User' }}</div>
                </div>
            </div>

            <div class="nav-label text-[9px] flex items-center justify-between" style="color:var(--secondary); opacity: 0.6;">
                <span>TeamSync v1.0</span>
                <span>&copy; 2026 Arshath AD</span>
            </div>
        </a>
    </div>

</nav>
