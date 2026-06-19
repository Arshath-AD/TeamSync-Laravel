<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full gap-4">
            <span>My Tasks</span>
            <a href="{{ route('tasks.create') }}" class="ws-btn-primary">+ New Task</a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 bg-workspace-elevated border border-workspace-success text-workspace-success px-3 py-2 rounded text-xs font-medium">{{ session('success') }}</div>
    @endif

    @php
        $activeCount = $tasks->where('status', '!=', 'Completed')->count();
    @endphp

    <div class="space-y-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <x-workspace.metric-card value="{{ $tasks->count() }}" label="Total" />
            <x-workspace.metric-card value="{{ $activeCount }}" label="Active" statusColor="accent" />
            <x-workspace.metric-card value="{{ count($overdue) }}" label="Overdue" statusColor="{{ count($overdue) > 0 ? 'danger' : 'success' }}" />
            <x-workspace.metric-card value="{{ count($upcoming) }}" label="Upcoming" statusColor="warning" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
            @if(count($overdue) > 0)
                <div class="lg:col-span-4">
                    <div class="ws-panel border-workspace-danger/40">
                        <div class="ws-panel-header bg-workspace-danger/5">
                            <h3 class="text-xs font-semibold text-workspace-danger uppercase tracking-wider flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Overdue
                            </h3>
                            <span class="text-[10px] bg-workspace-danger text-white px-1.5 py-0.5 rounded tabular-nums">{{ count($overdue) }}</span>
                        </div>
                        <div class="p-2 space-y-2 max-h-72 overflow-y-auto custom-scrollbar">
                            @foreach($overdue as $task)
                                <x-workspace.task-card :task="$task" />
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="{{ count($overdue) > 0 ? 'lg:col-span-4' : 'lg:col-span-6' }}">
                <div class="ws-panel h-full">
                    <div class="ws-panel-header">
                        <h3 class="ws-section-title">Upcoming</h3>
                        <span class="text-[10px] bg-workspace-elevated border border-workspace-border px-1.5 py-0.5 rounded tabular-nums">{{ count($upcoming) }}</span>
                    </div>
                    <div class="p-2 space-y-2 max-h-96 overflow-y-auto custom-scrollbar">
                        @forelse($upcoming as $task)
                            <x-workspace.task-card :task="$task" />
                        @empty
                            <div class="ws-empty-state py-6">No upcoming tasks.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="{{ count($overdue) > 0 ? 'lg:col-span-4' : 'lg:col-span-6' }}">
                <div class="ws-panel h-full">
                    <div class="ws-panel-header">
                        <h3 class="ws-section-title">All Active</h3>
                        <span class="text-[10px] bg-workspace-elevated border border-workspace-border px-1.5 py-0.5 rounded tabular-nums">{{ $activeCount }}</span>
                    </div>
                    <div class="p-2 space-y-2 max-h-96 overflow-y-auto custom-scrollbar">
                        @forelse($tasks->where('status', '!=', 'Completed')->sortBy('deadline') as $task)
                            <x-workspace.task-card :task="$task" />
                        @empty
                            <div class="ws-empty-state py-6">All caught up!</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
