@props(['member', 'metrics'])

@php
    $activeTasks = $metrics['active_tasks'] ?? 0;
    $completedTasks = $metrics['completed_tasks'] ?? 0;
    $assignedProjects = $metrics['assigned_projects'] ?? ($metrics['project_count'] ?? null);
    $capacityPercentage = min(100, (int) round(($activeTasks / 10) * 100));

    $capacityBarClass = match(true) {
        $capacityPercentage > 80 => 'bg-workspace-danger',
        $capacityPercentage > 50 => 'bg-workspace-warning',
        default => 'bg-workspace-success',
    };

    $availabilityClass = match(true) {
        $capacityPercentage > 80 => 'text-workspace-danger border-workspace-danger/30 bg-workspace-danger/10',
        $capacityPercentage > 50 => 'text-workspace-warning border-workspace-warning/30 bg-workspace-warning/10',
        default => 'text-workspace-success border-workspace-success/30 bg-workspace-success/10',
    };

    $availabilityLabel = match(true) {
        $capacityPercentage > 80 => 'Overloaded',
        $capacityPercentage > 50 => 'Busy',
        default => 'Available',
    };
@endphp

<div {{ $attributes->merge(['class' => 'ws-panel p-3 flex flex-col']) }}>
    <div class="flex items-start gap-2.5 mb-3">
        <div class="h-8 w-8 rounded bg-workspace-elevated flex items-center justify-center text-workspace-text text-xs font-bold flex-shrink-0 border border-workspace-border">
            {{ strtoupper(substr($member->name, 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
            <h4 class="text-xs font-semibold text-workspace-text truncate">{{ $member->name }}</h4>
            <p class="text-[10px] text-workspace-secondary truncate">{{ ucfirst($member->role ?? 'member') }}</p>
        </div>
        <span class="ws-badge {{ $availabilityClass }} flex-shrink-0">{{ $availabilityLabel }}</span>
    </div>

    <div class="grid grid-cols-3 gap-1.5 mb-3 text-[10px]">
        <div class="bg-workspace-elevated rounded px-2 py-1.5 text-center">
            <div class="text-workspace-secondary">Active</div>
            <div class="font-semibold text-workspace-text tabular-nums">{{ $activeTasks }}</div>
        </div>
        <div class="bg-workspace-elevated rounded px-2 py-1.5 text-center">
            <div class="text-workspace-secondary">Done</div>
            <div class="font-semibold text-workspace-success tabular-nums">{{ $completedTasks }}</div>
        </div>
        <div class="bg-workspace-elevated rounded px-2 py-1.5 text-center">
            <div class="text-workspace-secondary">Projects</div>
            <div class="font-semibold text-workspace-text tabular-nums">{{ $assignedProjects ?? '—' }}</div>
        </div>
    </div>

    <div>
        <div class="flex justify-between text-[9px] text-workspace-secondary mb-1 uppercase tracking-wider">
            <span>Capacity</span>
            <span class="tabular-nums">{{ $capacityPercentage }}%</span>
        </div>
        <div class="w-full bg-workspace-elevated rounded-full h-1">
            <div class="{{ $capacityBarClass }} h-1 rounded-full transition-all" style="width: {{ $capacityPercentage }}%"></div>
        </div>
    </div>
</div>
