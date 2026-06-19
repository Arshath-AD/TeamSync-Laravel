<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full gap-4">
            <span>Global Board</span>
            <div class="flex gap-2">
                <a href="{{ route('tasks.index', ['view' => 'list']) }}" class="ws-btn-secondary">List</a>
                <a href="{{ route('tasks.create') }}" class="ws-btn-primary">+ New Task</a>
            </div>
        </div>
    </x-slot>

    <div class="flex gap-3 overflow-x-auto board-container pb-2">
        @foreach(['Todo', 'In Progress', 'Completed'] as $column)
            <div class="ws-kanban-column">
                <div class="ws-kanban-header">
                    <h4 class="text-xs font-semibold text-workspace-text uppercase tracking-wider">{{ $column }}</h4>
                    <span class="text-[10px] bg-workspace-background text-workspace-secondary px-1.5 py-0.5 rounded border border-workspace-border tabular-nums">{{ count($board[$column] ?? []) }}</span>
                </div>
                <div class="ws-kanban-body">
                    @forelse($board[$column] ?? [] as $task)
                        <x-workspace.task-card :task="$task" />
                    @empty
                        <div class="ws-empty-state py-4">Empty</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>
