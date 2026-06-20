@props(['task', 'compact' => false])

@php
    $priorityBar = match($task->priority) {
        'Critical' => 'var(--danger)',
        'High'     => 'var(--warning)',
        'Medium'   => 'var(--accent)',
        default    => 'var(--muted)',
    };
    $priorityText = match($task->priority) {
        'Critical' => ['color' => 'var(--danger)',  'bg' => 'rgba(239,68,68,0.1)',  'border' => 'rgba(239,68,68,0.3)'],
        'High'     => ['color' => 'var(--warning)', 'bg' => 'rgba(245,158,11,0.1)', 'border' => 'rgba(245,158,11,0.3)'],
        'Medium'   => ['color' => 'var(--accent)',  'bg' => 'rgba(26,107,138,0.1)', 'border' => 'rgba(26,107,138,0.3)'],
        default    => ['color' => 'var(--secondary)','bg'=> 'var(--elevated)',       'border' => 'var(--border)'],
    };
    $statusText = match($task->status) {
        'In Progress' => ['label' => 'In Progress', 'class' => 'status-in-progress'],
        'On Hold'     => ['label' => 'On Hold',     'class' => 'status-on-hold'],
        'Completed'   => ['label' => 'Done',        'class' => 'status-completed'],
        default       => ['label' => 'Todo',        'class' => 'status-pending'],
    };
    $isOverdue  = $task->status !== 'Completed' && $task->status !== 'On Hold' && $task->deadline && \Carbon\Carbon::parse($task->deadline)->isBefore(now()->startOfDay());
    $isDueToday = $task->status !== 'Completed' && $task->status !== 'On Hold' && $task->deadline && \Carbon\Carbon::parse($task->deadline)->isToday();
@endphp

<div {{ $attributes->merge(['class' => 'ts-card p-3 relative group fade-in']) }}
     style="padding-left:14px;">
    {{-- Priority bar --}}
    <div style="position:absolute;left:0;top:6px;bottom:6px;width:2px;border-radius:4px;background:{{ $priorityBar }};opacity:0.8;"></div>

    <div class="flex items-start justify-between gap-2 mb-1.5">
        <a href="{{ route('tasks.edit', $task) }}"
           class="text-xs font-medium leading-snug line-clamp-2 transition-colors"
           style="color:var(--text)"
           onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text)'">
            {{ $task->task_name }}
        </a>
        @if($task->assignee)
            <img src="{{ $task->assignee->avatarUrl() }}"
                 alt="{{ $task->assignee->name }}"
                 title="{{ $task->assignee->name }}"
                 class="w-5 h-5 rounded-full object-cover ring-1 flex-shrink-0"
                 style="ring-color:var(--border)"
                 onerror="this.src='{{ asset('images/default-avatar.jpg') }}'">
        @endif
    </div>

    <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-1.5 min-w-0">
            @if($task->project)
                <span class="text-[10px] truncate max-w-[100px]" style="color:var(--secondary)">
                    {{ $task->project->project_name }}
                </span>
            @else
                <span class="ts-badge" style="color:var(--secondary);background:var(--elevated);border-color:var(--border)">
                    No Project
                </span>
            @endif
            <span class="ts-badge" style="color:{{ $priorityText['color'] }};background:{{ $priorityText['bg'] }};border-color:{{ $priorityText['border'] }}">
                {{ $task->priority }}
            </span>
        </div>
        <span class="status-pill {{ $statusText['class'] }}">{{ $statusText['label'] }}</span>
    </div>

    @if($task->deadline)
        <div class="mt-1.5 flex items-center gap-1 text-[10px]"
             style="color:{{ $isOverdue ? 'var(--danger)' : ($isDueToday ? 'var(--warning)' : 'var(--secondary)') }};
                    font-weight:{{ $isOverdue ? '600' : '400' }}">
            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
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
