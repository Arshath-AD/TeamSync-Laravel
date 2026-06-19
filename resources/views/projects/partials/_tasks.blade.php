@php
    $board = [
        'Todo'        => $project->tasks->where('status', 'Pending')->sortBy('deadline'),
        'In Progress' => $project->tasks->where('status', 'In Progress')->sortBy('deadline'),
        'On Hold'     => $project->tasks->where('status', 'On Hold')->sortBy('deadline'),
        'Completed'   => $project->tasks->where('status', 'Completed')->sortByDesc('updated_at'),
    ];
@endphp

<div class="space-y-3 fade-in">
    {{-- Controls --}}
    <div class="flex justify-between items-center mb-2">
        <h3 class="ts-section-title">Task Board</h3>
        <div class="flex items-center gap-2">
            <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}" class="ts-btn-primary">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Task
            </a>
        </div>
    </div>

    {{-- Board View --}}
    <div class="flex gap-3 overflow-x-auto board-scroll pb-3">
        @foreach($board as $column => $columnTasks)
            @php
                $colStyle = match($column) {
                    'In Progress' => ['accent' => 'var(--accent)',  'dot' => 'var(--accent)'],
                    'On Hold'     => ['accent' => 'var(--warning)', 'dot' => 'var(--warning)'],
                    'Completed'   => ['accent' => 'var(--success)', 'dot' => 'var(--success)'],
                    default       => ['accent' => 'var(--muted)',   'dot' => 'var(--secondary)'],
                };
            @endphp
            <div class="ts-kanban-col">
                <div class="ts-kanban-header">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full flex-shrink-0"
                              style="background:{{ $colStyle['dot'] }}"></span>
                        <h4 class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text)">
                            {{ $column }}
                        </h4>
                    </div>
                    <span class="text-[10px] tabular rounded px-1.5 py-0.5"
                          style="background:var(--elevated);border:1px solid var(--border);color:var(--secondary)">
                        {{ $columnTasks->count() }}
                    </span>
                </div>
                <div class="ts-kanban-body ts-scroll">
                    @forelse($columnTasks as $task)
                        <x-workspace.task-card :task="$task" />
                    @empty
                        <div class="ts-empty text-[11px] py-6">Empty</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
