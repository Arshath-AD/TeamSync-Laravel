@php
    $activeTasks = $project->tasks->where('status', '!=', 'Completed');
    $overdueCount = $activeTasks->filter(fn($t) => $t->deadline && \Carbon\Carbon::parse($t->deadline)->isBefore(now()->startOfDay()))->count();
    $criticalCount = $activeTasks->whereIn('priority', ['Critical', 'High'])->count();
    $nextDeadline = $activeTasks->whereNotNull('deadline')->sortBy('deadline')->first();
@endphp

<div class="space-y-4">
    <!-- Command center KPIs -->
    <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-6 gap-3">
        <x-workspace.metric-card value="{{ $metrics['completion_percentage'] }}%" label="Progress" statusColor="accent" />
        <x-workspace.metric-card value="{{ $metrics['task_count'] - $metrics['completed_task_count'] }}" label="Active Tasks" />
        <x-workspace.metric-card value="{{ $metrics['completed_task_count'] }}" label="Completed" statusColor="success" />
        <x-workspace.metric-card value="{{ $overdueCount }}" label="Overdue" statusColor="{{ $overdueCount > 0 ? 'danger' : 'success' }}" />
        <x-workspace.metric-card value="{{ $criticalCount }}" label="High Priority" statusColor="{{ $criticalCount > 0 ? 'warning' : 'accent' }}" />
        <x-workspace.metric-card value="{{ $metrics['member_count'] }}" label="Team Size" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        <!-- Active tasks -->
        <div class="lg:col-span-8 space-y-2">
            <div class="flex justify-between items-center">
                <h3 class="ws-section-title">Priority Queue</h3>
                <a href="{{ route('projects.show', [$project, 'tab' => 'tasks']) }}" class="ws-section-link">All tasks →</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @forelse($activeTasks->sortBy('deadline')->take(6) as $task)
                    <x-workspace.task-card :task="$task" />
                @empty
                    <div class="col-span-full ws-empty-state">No active tasks in this workspace.</div>
                @endforelse
            </div>
        </div>

        <!-- Sidebar meta -->
        <div class="lg:col-span-4 space-y-3">
            <!-- Health -->
            <div class="ws-panel p-3">
                <h3 class="ws-section-title mb-3">Workspace Health</h3>
                <div class="flex items-end justify-between mb-2">
                    <span class="text-2xl font-bold tabular-nums">{{ $metrics['completion_percentage'] }}%</span>
                    @if($overdueCount > 0)
                        <span class="ws-badge text-workspace-danger border-workspace-danger/30 bg-workspace-danger/10">{{ $overdueCount }} overdue</span>
                    @else
                        <span class="ws-badge text-workspace-success border-workspace-success/30 bg-workspace-success/10">On track</span>
                    @endif
                </div>
                <div class="w-full bg-workspace-elevated rounded-full h-1.5 mb-4">
                    <div class="bg-workspace-accent h-1.5 rounded-full" style="width: {{ $metrics['completion_percentage'] }}%"></div>
                </div>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between"><span class="text-workspace-secondary">Total</span><span class="font-medium tabular-nums">{{ $metrics['task_count'] }}</span></div>
                    <div class="flex justify-between"><span class="text-workspace-secondary">Completed</span><span class="font-medium text-workspace-success tabular-nums">{{ $metrics['completed_task_count'] }}</span></div>
                    <div class="flex justify-between"><span class="text-workspace-secondary">Next deadline</span><span class="font-medium">{{ $nextDeadline ? \Carbon\Carbon::parse($nextDeadline->deadline)->format('M j') : '—' }}</span></div>
                </div>
            </div>

            <!-- Lead + description -->
            <div class="ws-panel p-3">
                <h3 class="ws-section-title mb-3">Details</h3>
                <div class="flex items-center gap-2 mb-3">
                    <div class="h-7 w-7 rounded bg-workspace-elevated flex items-center justify-center text-[10px] font-bold border border-workspace-border">
                        {{ strtoupper(substr($project->lead->name ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <div class="text-xs font-medium">{{ $project->lead->name ?? 'Unassigned' }}</div>
                        <div class="text-[10px] text-workspace-secondary">Project Lead</div>
                    </div>
                </div>
                <p class="text-xs text-workspace-secondary leading-relaxed">
                    @if($project->description)
                        {{ \Illuminate\Support\Str::limit($project->description, 160) }}
                    @else
                        <span class="italic opacity-50">No description provided.</span>
                    @endif
                </p>
                <div class="mt-3 pt-3 border-t border-workspace-border text-[10px] text-workspace-secondary">
                    Created {{ $project->created_at->format('M j, Y') }}
                </div>
            </div>
        </div>
    </div>
</div>
