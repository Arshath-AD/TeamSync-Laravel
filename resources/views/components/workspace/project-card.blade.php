@props(['project', 'url'])

@php
    $metrics = $project->metrics ?? app(\App\Services\ProjectService::class)->getProjectMetrics($project);
    $activeTasks = $metrics['active_tasks'] ?? ($metrics['task_count'] - $metrics['completed_task_count']);

    $hasOverdueTasks = $project->relationLoaded('tasks')
        ? $project->tasks->contains(fn($t) => $t->status !== 'Completed' && $t->deadline && \Carbon\Carbon::parse($t->deadline)->isBefore(now()->startOfDay()))
        : $project->tasks()->where('status', '!=', 'Completed')->whereNotNull('deadline')->whereDate('deadline', '<', now())->exists();

    $riskLevel = match(true) {
        $hasOverdueTasks => 'critical',
        $activeTasks > 8 && $metrics['completion_percentage'] < 40 => 'high',
        $metrics['completion_percentage'] < 30 && $metrics['task_count'] > 0 => 'medium',
        default => 'low',
    };

    $riskDotClass = match($riskLevel) {
        'critical' => 'bg-workspace-danger',
        'high' => 'bg-workspace-warning',
        'medium' => 'bg-workspace-warning/70',
        default => 'bg-workspace-success',
    };

    $riskLabel = match($riskLevel) {
        'critical' => 'At Risk',
        'high' => 'High Load',
        'medium' => 'Behind',
        default => 'On Track',
    };

    $riskBadgeClass = match($riskLevel) {
        'critical' => 'text-workspace-danger border-workspace-danger/30 bg-workspace-danger/10',
        'high' => 'text-workspace-warning border-workspace-warning/30 bg-workspace-warning/10',
        'medium' => 'text-workspace-warning border-workspace-warning/20 bg-workspace-warning/5',
        default => 'text-workspace-success border-workspace-success/30 bg-workspace-success/10',
    };

    $deadline = $project->deadline ?? $project->end_date ?? null;
    if (!$deadline && $project->relationLoaded('tasks')) {
        $nextDeadline = $project->tasks->where('status', '!=', 'Completed')->whereNotNull('deadline')->sortBy('deadline')->first();
        $deadline = $nextDeadline?->deadline;
    }
@endphp

<a href="{{ $url }}" class="block ws-panel p-3 hover:border-workspace-accent/60 transition-all group">
    <div class="flex items-start justify-between gap-2 mb-2">
        <div class="min-w-0 flex-1">
            <h3 class="text-sm font-semibold text-workspace-text group-hover:text-workspace-accent truncate leading-tight">{{ $project->project_name }}</h3>
            @if($project->lead ?? null)
                <p class="text-[10px] text-workspace-secondary mt-0.5 truncate">{{ $project->lead->name }}</p>
            @endif
        </div>
        <div class="flex items-center gap-1.5 flex-shrink-0">
            <span class="ws-badge {{ $riskBadgeClass }}">{{ $riskLabel }}</span>
            <div class="h-2 w-2 rounded-full {{ $riskDotClass }}" title="Health indicator"></div>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-2 mb-2.5 text-[10px]">
        <div class="bg-workspace-elevated rounded px-2 py-1.5 text-center">
            <div class="text-workspace-secondary">Progress</div>
            <div class="font-semibold text-workspace-text tabular-nums">{{ $metrics['completion_percentage'] }}%</div>
        </div>
        <div class="bg-workspace-elevated rounded px-2 py-1.5 text-center">
            <div class="text-workspace-secondary">Active</div>
            <div class="font-semibold text-workspace-text tabular-nums">{{ $activeTasks }}</div>
        </div>
        <div class="bg-workspace-elevated rounded px-2 py-1.5 text-center">
            <div class="text-workspace-secondary">Team</div>
            <div class="font-semibold text-workspace-text tabular-nums">{{ $metrics['member_count'] }}</div>
        </div>
    </div>

    <div class="flex items-center gap-2">
        <div class="flex-1 bg-workspace-elevated rounded-full h-1">
            <div class="bg-workspace-accent h-1 rounded-full transition-all" style="width: {{ $metrics['completion_percentage'] }}%"></div>
        </div>
        @if($deadline)
            <span class="text-[10px] text-workspace-secondary flex-shrink-0">
                {{ \Carbon\Carbon::parse($deadline)->format('M j') }}
            </span>
        @endif
    </div>
</a>
