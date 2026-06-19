<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full gap-3">
            <span>Task Board</span>
            <div class="flex items-center gap-2">
                {{-- Board / List toggle --}}
                <div class="ts-toggle-group">
                    <button id="view-btn-board" class="ts-toggle-btn active" aria-label="Board view">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                        </svg>
                        Board
                    </button>
                    <button id="view-btn-list" class="ts-toggle-btn" aria-label="List view">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        List
                    </button>
                </div>
                <a href="{{ route('tasks.create') }}" class="ts-btn-primary">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Task
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $board = [
            'Todo'        => $tasks->where('status', 'Pending'),
            'In Progress' => $tasks->where('status', 'In Progress'),
            'On Hold'     => $tasks->where('status', 'On Hold'),
            'Completed'   => $tasks->where('status', 'Completed'),
        ];
        $overdue = $tasks->filter(fn($t) =>
            $t->status !== 'Completed' && $t->status !== 'On Hold' && $t->deadline &&
            \Carbon\Carbon::parse($t->deadline)->isBefore(now()->startOfDay())
        )->count();
    @endphp

    {{-- KPI strip --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
        <x-workspace.metric-card value="{{ $tasks->count() }}"                                                                     label="Total Tasks" />
        <x-workspace.metric-card value="{{ $board['Todo']->count() + $board['In Progress']->count() }}" label="Active"            statusColor="accent" />
        <x-workspace.metric-card value="{{ $board['Completed']->count() }}"                             label="Completed"         statusColor="success" />
        <x-workspace.metric-card value="{{ $overdue }}"                                                  label="Overdue"
            statusColor="{{ $overdue > 0 ? 'danger' : 'success' }}"
            trend="{{ $overdue > 0 ? '⚠ Overdue' : '' }}" />
    </div>

    {{-- ── Board view ─────────────────────────────────────────────────── --}}
    <div data-view="board">
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

    {{-- ── List view ──────────────────────────────────────────────────── --}}
    <div data-view="list" class="hidden">
        @include('tasks.partials._list', ['tasks' => $tasks])
    </div>

    <script>document.addEventListener('DOMContentLoaded', initTaskViewToggle);</script>
</x-app-layout>
