<div class="space-y-4">
    <!-- KPI Row -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <x-workspace.metric-card value="{{ $metrics['total_tasks'] }}" label="My Tasks" />
        <x-workspace.metric-card value="{{ $metrics['active_tasks'] }}" label="Active" statusColor="accent" />
        <x-workspace.metric-card value="{{ $metrics['completed_tasks'] }}" label="Completed" statusColor="success" />
        <x-workspace.metric-card value="{{ $metrics['due_today'] }}" label="Due Today" statusColor="{{ $metrics['due_today'] > 0 ? 'warning' : 'accent' }}" />
        <x-workspace.metric-card value="{{ $metrics['overdue_tasks'] }}" label="Overdue" statusColor="danger" trend="{{ $metrics['overdue_tasks'] > 0 ? '+Urgent' : '' }}" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        <!-- Focus queue -->
        <div class="lg:col-span-8 space-y-2">
            <div class="flex justify-between items-center">
                <h3 class="ws-section-title">Needs Attention</h3>
                <a href="{{ route('tasks.myTasks') }}" class="ws-section-link">My tasks →</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @forelse($widgets['my_active_tasks'] as $task)
                    <x-workspace.task-card :task="$task" />
                @empty
                    <div class="col-span-full ws-empty-state">You're all caught up.</div>
                @endforelse
            </div>
        </div>

        <!-- Productivity ring -->
        <div class="lg:col-span-4">
            <div class="ws-panel p-4 h-full flex flex-col items-center justify-center text-center">
                <div class="relative w-24 h-24 mb-3">
                    <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                        <circle class="text-workspace-elevated stroke-current" stroke-width="6" cx="50" cy="50" r="42" fill="transparent"></circle>
                        <circle class="text-workspace-accent stroke-current" stroke-width="6" stroke-linecap="round" cx="50" cy="50" r="42" fill="transparent"
                            stroke-dasharray="263.9"
                            stroke-dashoffset="{{ 263.9 - (263.9 * $metrics['completion_percentage']) / 100 }}"></circle>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-xl font-bold text-workspace-text tabular-nums">{{ $metrics['completion_percentage'] }}%</span>
                    </div>
                </div>
                <h4 class="text-xs font-semibold text-workspace-text">Completion Rate</h4>
                <p class="text-[10px] text-workspace-secondary mt-1">{{ $metrics['completed_tasks'] }}/{{ $metrics['total_tasks'] }} tasks done</p>
                <div class="grid grid-cols-2 gap-2 w-full mt-4 text-[10px]">
                    <div class="bg-workspace-elevated rounded px-2 py-1.5">
                        <div class="text-workspace-secondary">Projects</div>
                        <div class="font-semibold text-workspace-text">{{ $metrics['assigned_projects'] }}</div>
                    </div>
                    <div class="bg-workspace-elevated rounded px-2 py-1.5">
                        <div class="text-workspace-secondary">Active Projects</div>
                        <div class="font-semibold text-workspace-text">{{ $metrics['my_active_projects'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming deadlines -->
    <div class="space-y-2">
        <div class="flex justify-between items-center">
            <h3 class="ws-section-title">Upcoming Deadlines</h3>
            <a href="{{ route('tasks.board') }}" class="ws-section-link">Board view →</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-2">
            @forelse($widgets['upcoming_deadlines'] as $task)
                <x-workspace.task-card :task="$task" />
            @empty
                <div class="col-span-full ws-empty-state">No upcoming deadlines.</div>
            @endforelse
        </div>
    </div>
</div>
