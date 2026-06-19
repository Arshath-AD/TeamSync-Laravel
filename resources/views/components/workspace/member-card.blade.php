@props(['member', 'metrics'])

@php
    $activeTasks      = $metrics['active_tasks'] ?? 0;
    $completedTasks   = $metrics['completed_tasks'] ?? 0;
    $assignedProjects = $metrics['assigned_projects'] ?? ($metrics['project_count'] ?? null);
    
    // Calculate capacity based on weighted workload (fallback to active tasks * 2 if not provided)
    $weightedWorkload = $metrics['weighted_workload'] ?? $member->weighted_workload ?? ($activeTasks * 2);
    // Assuming 20 points is 100% capacity (e.g., 4 Critical tasks or 10 Medium tasks)
    $capacityPct      = min(100, (int) round(($weightedWorkload / 20) * 100));

    $barColor   = $capacityPct > 80 ? 'var(--danger)' : ($capacityPct > 50 ? 'var(--warning)' : 'var(--success)');
    $badgeColor = match(true) {
        $capacityPct > 80 => ['color' => 'var(--danger)',  'bg' => 'rgba(239,68,68,0.1)',  'border' => 'rgba(239,68,68,0.3)',  'label' => 'Overloaded'],
        $capacityPct > 50 => ['color' => 'var(--warning)', 'bg' => 'rgba(245,158,11,0.1)', 'border' => 'rgba(245,158,11,0.3)', 'label' => 'Busy'],
        $activeTasks > 0  => ['color' => 'var(--success)', 'bg' => 'rgba(34,197,94,0.1)',  'border' => 'rgba(34,197,94,0.3)',  'label' => 'Active'],
        default           => ['color' => 'var(--secondary)','bg'=> 'var(--elevated)',        'border' => 'var(--border)',        'label' => 'Idle'],
    };
@endphp

<div {{ $attributes->merge(['class' => 'ts-card p-3 flex flex-col gap-3']) }}>

    {{-- Header: avatar + name + status --}}
    <div class="flex items-center gap-2.5">
        <div class="ts-avatar w-8 h-8 text-xs flex-shrink-0">
            {{ strtoupper(substr($member->name, 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
            <h4 class="text-xs font-semibold truncate" style="color:var(--text)">{{ $member->name }}</h4>
            <p class="text-[10px] truncate" style="color:var(--secondary)">{{ ucfirst($member->role ?? 'Member') }}</p>
        </div>
        <span class="ts-badge flex-shrink-0"
              style="color:{{ $badgeColor['color'] }};background:{{ $badgeColor['bg'] }};border-color:{{ $badgeColor['border'] }}">
            {{ $badgeColor['label'] }}
        </span>
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
