@props(['project', 'url'])

@php
    $metrics = $project->metrics ?? app(\App\Services\ProjectService::class)->getProjectMetrics($project);
    $activeTasks = $metrics['active_tasks'] ?? ($metrics['task_count'] - $metrics['completed_task_count']);

    $hasOverdueTasks = $project->relationLoaded('tasks')
        ? $project->tasks->contains(fn($t) => !in_array($t->status, ['Completed', 'On Hold']) && $t->deadline && \Carbon\Carbon::parse($t->deadline)->isBefore(now()->startOfDay()))
        : $project->tasks()->whereNotIn('status', ['Completed', 'On Hold'])->whereNotNull('deadline')->whereDate('deadline', '<', now())->exists();

    $riskLevel = match(true) {
        $hasOverdueTasks => 'critical',
        $activeTasks > 8 && $metrics['completion_percentage'] < 40 => 'high',
        $metrics['completion_percentage'] < 30 && $metrics['task_count'] > 0 => 'medium',
        default => 'low',
    };

    $riskStyle = match($riskLevel) {
        'critical' => ['color' => 'var(--danger)',  'bg' => 'rgba(239,68,68,0.1)',  'border' => 'rgba(239,68,68,0.3)',  'label' => 'At Risk'],
        'high'     => ['color' => 'var(--warning)', 'bg' => 'rgba(245,158,11,0.1)', 'border' => 'rgba(245,158,11,0.3)', 'label' => 'High Load'],
        'medium'   => ['color' => 'var(--warning)', 'bg' => 'rgba(245,158,11,0.06)','border' => 'rgba(245,158,11,0.2)', 'label' => 'Behind'],
        default    => ['color' => 'var(--success)',  'bg' => 'rgba(34,197,94,0.08)', 'border' => 'rgba(34,197,94,0.3)',  'label' => 'On Track'],
    };

    // Project priority badge
    $priority = $project->priority ?? 'Medium';
    $priorityStyle = match($priority) {
        'Critical' => ['color' => 'var(--danger)',  'bg' => 'rgba(239,68,68,0.1)',  'border' => 'rgba(239,68,68,0.3)'],
        'High'     => ['color' => 'var(--warning)', 'bg' => 'rgba(245,158,11,0.1)', 'border' => 'rgba(245,158,11,0.3)'],
        'Medium'   => ['color' => 'var(--accent)',  'bg' => 'rgba(26,107,138,0.1)', 'border' => 'rgba(26,107,138,0.3)'],
        default    => ['color' => 'var(--success)', 'bg' => 'rgba(34,197,94,0.08)', 'border' => 'rgba(34,197,94,0.3)'],
    };

    $deadline = $project->deadline ?? $project->end_date ?? null;
    if (!$deadline && $project->relationLoaded('tasks')) {
        $nextDeadline = $project->tasks->where('status', '!=', 'Completed')->whereNotNull('deadline')->sortBy('deadline')->first();
        $deadline = $nextDeadline?->deadline;
    }
    $pct = $metrics['completion_percentage'];
    $barColor = $pct >= 80 ? 'var(--success)' : ($pct >= 40 ? 'var(--accent)' : 'var(--warning)');
@endphp

<a href="{{ $url }}" class="ts-card block p-3 hover-lift" style="text-decoration:none;">
    {{-- Header row: name + health badge --}}
    <div class="flex items-start justify-between gap-2 mb-2">
        <div class="min-w-0 flex-1">
            <h3 class="text-sm font-semibold leading-tight truncate transition-colors"
                style="color:var(--text)"
                onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text)'">
                {{ $project->project_name }}
            </h3>
            @if($project->lead ?? null)
                <p class="text-[10px] mt-0.5 truncate" style="color:var(--secondary)">
                    {{ $project->lead->name }}
                </p>
            @endif
        </div>
        <span class="ts-badge flex-shrink-0"
              style="color:{{ $riskStyle['color'] }};background:{{ $riskStyle['bg'] }};border-color:{{ $riskStyle['border'] }}">
            {{ $riskStyle['label'] }}
        </span>
    </div>

    {{-- Priority badge --}}
    <div class="mb-3">
        <span class="ts-badge"
              style="color:{{ $priorityStyle['color'] }};background:{{ $priorityStyle['bg'] }};border-color:{{ $priorityStyle['border'] }}">
            {{ $priority }}
        </span>
    </div>

    {{-- Members --}}
    @php
        $members = $project->members ?? collect();
        $displayMembers = $members->take(4);
        $extraMembers = $members->count() - 4;
    @endphp
    @if($members->count() > 0)
        <div class="flex items-center gap-1.5 mb-3">
            @foreach($displayMembers as $member)
                <div title="{{ $member->name }}">
                    <x-workspace.avatar :user="$member" sizeClass="w-8 h-8" class="border border-[var(--surface)]" />
                </div>
            @endforeach
            @if($extraMembers > 0)
                <div class="flex items-center justify-center w-8 h-8 rounded-full border border-[var(--surface)] text-[10px] font-medium" 
                     style="background:var(--elevated);color:var(--secondary)"
                     title="+{{ $extraMembers }} more members">
                    +{{ $extraMembers }}
                </div>
            @endif
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-1.5 mb-3">
        @foreach([
            ['label' => 'Progress', 'value' => $pct . '%', 'color' => $barColor],
            ['label' => 'Active',   'value' => $activeTasks, 'color' => 'var(--text)'],
            ['label' => 'Team',     'value' => $metrics['member_count'], 'color' => 'var(--text)'],
        ] as $stat)
            <div class="rounded px-2 py-1.5 text-center text-[10px]" style="background:var(--elevated);">
                <div style="color:var(--secondary)">{{ $stat['label'] }}</div>
                <div class="font-semibold tabular" style="color:{{ $stat['color'] }}">{{ $stat['value'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- Progress bar + deadline --}}
    <div class="flex items-center gap-2">
        <div class="ts-progress-track flex-1">
            <div class="ts-progress-fill" style="width:{{ $pct }}%;background:{{ $barColor }}"></div>
        </div>
        @if($deadline)
            <span class="text-[10px] flex-shrink-0" style="color:var(--secondary)">
                {{ \Carbon\Carbon::parse($deadline)->format('M j') }}
            </span>
        @endif
    </div>
</a>
