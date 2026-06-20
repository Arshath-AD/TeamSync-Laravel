@php
    $overloaded  = $members->filter(fn($m) => $m->active_tasks > 8)->count();
    $available   = $members->filter(fn($m) => $m->active_tasks <= 4)->count();
    $totalActive = $members->sum('active_tasks');
    $currentTab  = request('tab', 'members'); // 'members' or 'admin'
@endphp

<div class="space-y-4 fade-in">

    {{-- Tab Bar + Search --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex gap-1 p-1 rounded-md" style="background:var(--elevated)">
            <a href="{{ route('dashboard', ['section' => 'members', 'tab' => 'members', 'search' => request('search')]) }}"
               class="px-3 py-1 rounded text-xs font-medium transition-all {{ $currentTab !== 'admin' ? 'text-white' : '' }}"
               style="{{ $currentTab !== 'admin' ? 'background:var(--accent)' : 'color:var(--secondary)' }}">
                Team Members
            </a>
            @if($isAdmin)
            <a href="{{ route('dashboard', ['section' => 'members', 'tab' => 'admin', 'search' => request('search')]) }}"
               class="px-3 py-1 rounded text-xs font-medium transition-all {{ $currentTab === 'admin' ? 'text-white' : '' }}"
               style="{{ $currentTab === 'admin' ? 'background:var(--accent)' : 'color:var(--secondary)' }}">
                User Administration
            </a>
            @endif
        </div>

        <div class="flex items-center gap-2">
            <form action="{{ route('dashboard') }}" method="GET" class="relative" id="member-search-form">
                <input type="hidden" name="section" value="members">
                <input type="hidden" name="tab" value="{{ $currentTab }}">
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Search name, email or role…"
                       class="ts-input pl-8 pr-3 py-1.5 text-xs w-56"
                       oninput="clearTimeout(window.searchTimeout); window.searchTimeout = setTimeout(() => document.getElementById('member-search-form').submit(), 400);"
                       @if(request()->has('search')) autofocus onfocus="this.setSelectionRange(this.value.length, this.value.length);" @endif>
                <svg class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2" style="color:var(--secondary)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </form>
            @if($isAdmin && $currentTab === 'admin')
            <a href="{{ route('users.create') }}" class="ts-btn-primary text-xs">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add User
            </a>
            @endif
        </div>
    </div>

    {{-- Resource KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <x-workspace.metric-card value="{{ $members->count() }}" label="Team Members" />
        <x-workspace.metric-card value="{{ $totalActive }}"      label="Active Tasks"  statusColor="accent" />
        <x-workspace.metric-card value="{{ $overloaded }}"       label="Overloaded"
            statusColor="{{ $overloaded > 0 ? 'danger' : 'success' }}"
            trend="{{ $overloaded > 0 ? '⚠ Review' : '' }}" />
        <x-workspace.metric-card value="{{ $available }}"        label="Available"     statusColor="success" />
    </div>

    @if($currentTab !== 'admin')
    {{-- ── TEAM MEMBERS TAB ────────────────────────────────────────── --}}

    {{-- Workforce load bar --}}
    <div class="ts-panel p-3">
        <div class="flex items-center justify-between mb-2">
            <span class="ts-section-title">Workforce Load Distribution</span>
            <span class="text-[10px]" style="color:var(--secondary)">{{ $members->count() }} resources tracked</span>
        </div>

        {{-- Segmented bar --}}
        <div class="flex gap-0.5 h-2 rounded overflow-hidden">
            @foreach($members as $member)
                @php
                    $w = max(4, ($member->active_tasks / max(1, $totalActive)) * 100);
                    $c = match(true) {
                        $member->active_tasks > 8 => 'var(--danger)',
                        $member->active_tasks > 4 => 'var(--warning)',
                        $member->active_tasks > 0 => 'var(--accent)',
                        default                   => 'var(--elevated)',
                    };
                @endphp
                <div style="width:{{ $w }}%;background:{{ $c }};transition:width 0.5s"
                     title="{{ $member->name }}: {{ $member->active_tasks }} tasks"></div>
            @endforeach
        </div>

        {{-- Legend --}}
        <div class="flex gap-4 mt-2 text-[10px]" style="color:var(--secondary)">
            @foreach([
                ['Overloaded', 'var(--danger)'],
                ['Busy',       'var(--warning)'],
                ['Active',     'var(--accent)'],
                ['Idle',       'var(--elevated)'],
            ] as [$label, $color])
                <span class="flex items-center gap-1">
                    <span class="h-2 w-2 rounded-sm inline-block" style="background:{{ $color }};border:1px solid var(--border)"></span>
                    {{ $label }}
                </span>
            @endforeach
        </div>
    </div>

    {{-- Member grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
        @forelse($members as $member)
            <x-workspace.member-card
                :member="$member"
                :isAdmin="$isAdmin"
                :metrics="[
                    'active_tasks'      => $member->active_tasks,
                    'completed_tasks'   => $member->completed_tasks,
                    'assigned_projects' => $member->project_count,
                    'weighted_workload' => $member->weighted_workload,
                ]"
            />
        @empty
            <div class="col-span-full ts-empty py-12 text-center text-sm" style="color:var(--secondary)">
                No team members found{{ $search ? ' for "' . e($search) . '"' : '' }}.
            </div>
        @endforelse
    </div>

    @else
    {{-- ── USER ADMINISTRATION TAB ──────────────────────────────────── --}}
    <div class="ts-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="text-xs uppercase border-b border-[var(--border)]" style="background:var(--elevated);color:var(--text)">
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
                    @forelse($members as $member)
                    <tr class="transition-colors" style="color:var(--secondary)" onmouseover="this.style.background='var(--elevated)'" onmouseout="this.style.background=''">
                        <td class="px-4 py-3 font-medium" style="color:var(--text)">
                            <div class="flex items-center gap-2">
                                <div class="ts-avatar w-7 h-7 text-[10px] flex-shrink-0">{{ strtoupper(substr($member->name, 0, 1)) }}</div>
                                {{ $member->name }}
                                @if($member->id === Auth::id())
                                    <span class="text-[9px] px-1.5 py-0.5 rounded" style="background:var(--accent-dim);color:var(--accent)">You</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs">{{ $member->email }}</td>
                        <td class="px-4 py-3">
                            @if($member->role === 'admin')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium" style="background:var(--accent-dim);color:var(--accent);border:1px solid rgba(59,130,246,0.3)">Admin</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium" style="background:var(--elevated);color:var(--secondary);border:1px solid var(--border)">User</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-xs">{{ $member->project_count }}</td>
                        <td class="px-4 py-3 text-center text-xs">{{ $member->total_tasks }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-1.5">
                                <a href="{{ route('users.edit', $member) }}" class="ts-btn-secondary text-xs px-2 py-1">Edit</a>
                                <form action="{{ route('users.destroy', $member) }}" method="POST"
                                      onsubmit="return confirm('Delete {{ addslashes($member->name) }}? Their led projects will be reassigned to you and their tasks unassigned.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ts-btn-danger text-xs px-2 py-1"
                                        {{ $member->id === Auth::id() ? 'disabled title=Cannot delete yourself' : '' }}>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm" style="color:var(--secondary)">
                            No users found{{ $search ? ' for "' . e($search) . '"' : '' }}.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
