@php
    $board = [
        'Todo' => $project->tasks->where('status', 'Pending')->sortBy('deadline'),
        'In Progress' => $project->tasks->where('status', 'In Progress')->sortBy('deadline'),
        'Completed' => $project->tasks->where('status', 'Completed')->sortByDesc('updated_at'),
    ];
@endphp

<div class="space-y-3">
    <div class="flex justify-between items-center">
        <h3 class="ws-section-title">Task Board</h3>
        <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}" class="ws-btn-secondary">+ Add Task</a>
    </div>

    <div class="flex gap-3 overflow-x-auto board-container pb-2">
        @foreach($board as $column => $tasks)
            <div class="ws-kanban-column">
                <div class="ws-kanban-header">
                    <h4 class="text-xs font-semibold text-workspace-text uppercase tracking-wider">{{ $column }}</h4>
                    <span class="text-[10px] bg-workspace-background text-workspace-secondary px-1.5 py-0.5 rounded border border-workspace-border tabular-nums">{{ $tasks->count() }}</span>
                </div>
                <div class="ws-kanban-body">
                    @forelse($tasks as $task)
                        <x-workspace.task-card :task="$task" />
                    @empty
                        <div class="ws-empty-state py-4">Empty</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
