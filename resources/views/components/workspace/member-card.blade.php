@props(['member', 'metrics', 'isAdmin' => false])

@php
    $activeTasks      = $metrics['active_tasks'] ?? 0;
    $completedTasks   = $metrics['completed_tasks'] ?? 0;
    $assignedProjects = $metrics['assigned_projects'] ?? ($metrics['project_count'] ?? null);

    $weightedWorkload = $metrics['weighted_workload'] ?? $member->weighted_workload ?? ($activeTasks * 2);
    $capacityPct      = min(100, (int) round(($weightedWorkload / 20) * 100));

    $barColor   = $capacityPct > 80 ? 'var(--danger)' : ($capacityPct > 50 ? 'var(--warning)' : 'var(--success)');
    $badgeColor = match(true) {
        $capacityPct > 80 => ['color' => 'var(--danger)',  'bg' => 'rgba(239,68,68,0.1)',  'border' => 'rgba(239,68,68,0.3)',  'label' => 'Overloaded'],
        $capacityPct > 50 => ['color' => 'var(--warning)', 'bg' => 'rgba(245,158,11,0.1)', 'border' => 'rgba(245,158,11,0.3)', 'label' => 'Busy'],
        $activeTasks > 0  => ['color' => 'var(--success)', 'bg' => 'rgba(34,197,94,0.1)',  'border' => 'rgba(34,197,94,0.3)',  'label' => 'Active'],
        default           => ['color' => 'var(--secondary)','bg'=> 'var(--elevated)',        'border' => 'var(--border)',        'label' => 'Idle'],
    };

    $roleColor = $member->role === 'admin'
        ? ['color' => 'var(--accent)', 'bg' => 'var(--accent-dim)', 'border' => 'rgba(59,130,246,0.3)', 'label' => 'Admin']
        : ['color' => 'var(--secondary)', 'bg' => 'var(--elevated)', 'border' => 'var(--border)', 'label' => 'User'];

    $dropdownId = 'member-menu-' . $member->id;
@endphp

<div {{ $attributes->merge(['class' => 'ts-card p-3 flex flex-col gap-3 relative']) }}>

    {{-- Header: avatar + name + status badge + admin menu --}}
    <div class="flex items-start gap-2.5">
        <div class="flex-shrink-0">
            <x-workspace.avatar :user="$member" sizeClass="w-8 h-8" />
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-1.5 flex-wrap">
                <h4 class="text-xs font-semibold truncate" style="color:var(--text)">{{ $member->name }}</h4>
                <span class="text-[9px] px-1.5 py-0.5 rounded font-medium flex-shrink-0"
                      style="color:{{ $roleColor['color'] }};background:{{ $roleColor['bg'] }};border:1px solid {{ $roleColor['border'] }}">
                    {{ $roleColor['label'] }}
                </span>
            </div>
            <p class="text-[10px] truncate" style="color:var(--secondary)">{{ $member->email }}</p>
        </div>

        {{-- Status badge + Admin actions --}}
        <div class="flex items-center gap-1 flex-shrink-0">
            <span class="ts-badge"
                  style="color:{{ $badgeColor['color'] }};background:{{ $badgeColor['bg'] }};border-color:{{ $badgeColor['border'] }}">
                {{ $badgeColor['label'] }}
            </span>

            @if($isAdmin)
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button @click="open = !open"
                        class="w-5 h-5 flex items-center justify-center rounded transition-colors focus:outline-none"
                        style="color:var(--secondary)"
                        onmouseover="this.style.background='var(--elevated)'"
                        onmouseout="this.style.background=''"
                        title="Member actions">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                    </svg>
                </button>

                <div x-show="open" x-transition
                     class="absolute right-0 top-6 z-50 w-40 rounded shadow-lg py-1 text-xs"
                     style="background:var(--surface);border:1px solid var(--border)">
                    <a href="{{ route('users.edit', $member) }}"
                       class="flex items-center gap-2 w-full px-3 py-1.5 transition-colors"
                       style="color:var(--text)"
                       onmouseover="this.style.background='var(--elevated)'"
                       onmouseout="this.style.background=''">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit User
                    </a>

                    <div style="border-top:1px solid var(--border);margin:2px 0"></div>

                    <form action="{{ route('users.destroy', $member) }}" method="POST"
                          onsubmit="return confirm('Delete {{ addslashes($member->name) }}? Projects led by them will be reassigned to you and their tasks unassigned.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="flex items-center gap-2 w-full px-3 py-1.5 transition-colors text-left"
                                style="color:var(--danger)"
                                onmouseover="this.style.background='rgba(239,68,68,0.08)'"
                                onmouseout="this.style.background=''"
                                {{ $member->id === Auth::id() ? 'disabled title=You cannot delete yourself' : '' }}>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Remove User
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-1.5 text-[10px]">
        <div class="rounded px-2 py-1.5 text-center" style="background:var(--elevated)">
            <div style="color:var(--secondary)">Active</div>
            <div class="font-semibold tabular" style="color:var(--text)">{{ $activeTasks }}</div>
        </div>
        <div class="rounded px-2 py-1.5 text-center" style="background:var(--elevated)">
            <div style="color:var(--secondary)">Done</div>
            <div class="font-semibold tabular" style="color:var(--success)">{{ $completedTasks }}</div>
        </div>
        <div class="rounded px-2 py-1.5 text-center" style="background:var(--elevated)">
            <div style="color:var(--secondary)">Projects</div>
            <div class="font-semibold tabular" style="color:var(--text)">{{ $assignedProjects ?? '—' }}</div>
        </div>
    </div>

    {{-- Capacity bar --}}
    <div>
        <div class="flex justify-between text-[9px] uppercase tracking-wider mb-1" style="color:var(--secondary)">
            <span>Capacity</span>
            <span class="tabular">{{ $capacityPct }}%</span>
        </div>
        <div class="ts-progress-track">
            <div class="ts-progress-fill" style="width:{{ $capacityPct }}%;background:{{ $barColor }}"></div>
        </div>
    </div>
</div>
