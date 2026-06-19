@props(['task', 'compact' => false])

@php
    $priorityConfig = match($task->priority) {
        'Critical' => ['class' => 'text-workspace-danger border-workspace-danger/40 bg-workspace-danger/10', 'bar' => 'bg-workspace-danger'],
        'High' => ['class' => 'text-workspace-danger border-workspace-danger/30 bg-workspace-danger/5', 'bar' => 'bg-workspace-danger/80'],
        'Medium' => ['class' => 'text-workspace-warning border-workspace-warning/30 bg-workspace-warning/10', 'bar' => 'bg-workspace-warning'],
        'Low' => ['class' => 'text-workspace-success border-workspace-success/30 bg-workspace-success/10', 'bar' => 'bg-workspace-success'],
        default => ['class' => 'text-workspace-secondary border-workspace-border bg-workspace-elevated', 'bar' => 'bg-workspace-secondary'],
    };

    $statusConfig = match($task->status) {
        'In Progress' => ['class' => 'text-workspace-accent border-workspace-accent/30 bg-workspace-accent/10', 'label' => 'In Progress'],
        'Completed' => ['class' => 'text-workspace-success border-workspace-success/30 bg-workspace-success/10', 'label' => 'Done'],
        default => ['class' => 'text-workspace-secondary border-workspace-border bg-workspace-elevated', 'label' => 'Todo'],
    };

    $isOverdue = $task->status !== 'Completed' && $task->deadline && \Carbon\Carbon::parse($task->deadline)->isBefore(now()->startOfDay());
    $isDueToday = $task->status !== 'Completed' && $task->deadline && \Carbon\Carbon::parse($task->deadline)->isToday();
@endphp

<div {{ $attributes->merge(['class' => 'relative bg-workspace-surface border border-workspace-border rounded-md p-2.5 hover:border-workspace-accent/40 transition-colors group']) }}>
    <div class="absolute left-0 top-2 bottom-2 w-0.5 {{ $priorityConfig['bar'] }} rounded-full opacity-70"></div>

    <div class="pl-2">
        <div class="flex items-start justify-between gap-2 mb-1.5">
            <a href="{{ route('tasks.edit', $task) }}" class="text-xs font-medium text-workspace-text leading-snug group-hover:text-workspace-accent transition-colors line-clamp-2">
                {{ $task->task_name }}
            </a>
            @if($task->assignee)
                <div class="h-5 w-5 rounded bg-workspace-elevated flex items-center justify-center text-[9px] font-bold text-workspace-text flex-shrink-0 border border-workspace-border" title="{{ $task->assignee->name }}">
                    {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                </div>
            @endif
        </div>

        <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-1.5 min-w-0">
                <span class="text-[10px] text-workspace-secondary truncate max-w-[100px]">{{ $task->project->project_name ?? '—' }}</span>
                <span class="ws-badge {{ $priorityConfig['class'] }} flex-shrink-0">{{ $task->priority }}</span>
            </div>
            <span class="ws-badge {{ $statusConfig['class'] }} flex-shrink-0">{{ $statusConfig['label'] }}</span>
        </div>

        @if($task->deadline)
            <div class="mt-1.5 flex items-center gap-1 text-[10px] {{ $isOverdue ? 'text-workspace-danger font-semibold' : ($isDueToday ? 'text-workspace-warning' : 'text-workspace-secondary') }}">
                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                @if($isOverdue)
                    Overdue · {{ \Carbon\Carbon::parse($task->deadline)->format('M j') }}
                @elseif($isDueToday)
                    Due today
                @else
                    {{ \Carbon\Carbon::parse($task->deadline)->format('M j') }}
                @endif
            </div>
        @endif
    </div>
</div>
