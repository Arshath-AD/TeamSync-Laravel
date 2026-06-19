<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full gap-4">
            <span>Task Board</span>
            <div class="flex gap-2">
                <a href="{{ route('tasks.index', ['view' => 'list']) }}" class="ws-btn-secondary">List</a>
                <a href="{{ route('tasks.create') }}" class="ws-btn-primary">+ New Task</a>
            </div>
        </div>
    </x-slot>

    @php
        $board = [
            'Todo' => $tasks->where('status', 'Pending'),
            'In Progress' => $tasks->where('status', 'In Progress'),
            'Completed' => $tasks->where('status', 'Completed'),
        ];
        $overdue = $tasks->filter(fn($t) => $t->status !== 'Completed' && $t->deadline && \Carbon\Carbon::parse($t->deadline)->isBefore(now()->startOfDay()))->count();
    @endphp

    @if(session('success'))
        <div class="mb-4 bg-workspace-elevated border border-workspace-success text-workspace-success px-3 py-2 rounded text-xs font-medium">{{ session('success') }}</div>
    @endif

    @if(request('view') === 'list')
        @include('tasks.partials._list', ['tasks' => $tasks])
    @else
        <div class="space-y-3">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <x-workspace.metric-card value="{{ $tasks->count() }}" label="Total Tasks" />
                <x-workspace.metric-card value="{{ $board['Todo']->count() + $board['In Progress']->count() }}" label="Active" statusColor="accent" />
                <x-workspace.metric-card value="{{ $board['Completed']->count() }}" label="Completed" statusColor="success" />
                <x-workspace.metric-card value="{{ $overdue }}" label="Overdue" statusColor="{{ $overdue > 0 ? 'danger' : 'success' }}" />
            </div>

            <div class="flex gap-3 overflow-x-auto board-container pb-2">
                @foreach($board as $column => $columnTasks)
                    <div class="ws-kanban-column">
                        <div class="ws-kanban-header">
                            <h4 class="text-xs font-semibold text-workspace-text uppercase tracking-wider">{{ $column }}</h4>
                            <span class="text-[10px] bg-workspace-background text-workspace-secondary px-1.5 py-0.5 rounded border border-workspace-border tabular-nums">{{ $columnTasks->count() }}</span>
                        </div>
                        <div class="ws-kanban-body">
                            @forelse($columnTasks as $task)
                                <x-workspace.task-card :task="$task" />
                            @empty
                                <div class="ws-empty-state py-4">Empty</div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-app-layout>
