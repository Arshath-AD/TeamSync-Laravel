<div class="space-y-4">
    <!-- Top: 6 KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3">
        <x-workspace.metric-card value="{{ $metrics['total_projects'] }}" label="Workspaces" />
        <x-workspace.metric-card value="{{ $metrics['active_projects'] }}" label="Active" statusColor="accent" />
        <x-workspace.metric-card value="{{ $metrics['total_users'] }}" label="Team Size" />
        <x-workspace.metric-card value="{{ $metrics['total_tasks'] }}" label="Total Tasks" />
        <x-workspace.metric-card value="{{ $metrics['completed_tasks'] }}" label="Completed" statusColor="success" />
        <x-workspace.metric-card value="{{ $metrics['overdue_tasks'] }}" label="Overdue" statusColor="danger" trend="{{ $metrics['overdue_tasks'] > 0 ? '+Action' : '' }}" />
    </div>

    <!-- Middle: Projects + Deadlines -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        <div class="lg:col-span-8 space-y-2">
            <div class="flex justify-between items-center">
                <h3 class="ws-section-title">Active Workspaces</h3>
                <a href="{{ route('projects.index') }}" class="ws-section-link">View all →</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                @forelse($widgets['recent_projects'] as $project)
                    <x-workspace.project-card :project="$project" :url="route('projects.show', $project)" />
                @empty
                    <div class="col-span-full ws-empty-state">No active workspaces.</div>
                @endforelse
            </div>
        </div>

        <div class="lg:col-span-4 space-y-2">
            <div class="flex justify-between items-center">
                <h3 class="ws-section-title">Upcoming Deadlines</h3>
                <a href="{{ route('tasks.board') }}" class="ws-section-link">Board →</a>
            </div>
            <div class="space-y-2 max-h-[340px] overflow-y-auto custom-scrollbar pr-1">
                @forelse($widgets['upcoming_deadlines'] as $task)
                    <x-workspace.task-card :task="$task" />
                @empty
                    <div class="ws-empty-state">No upcoming deadlines.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Bottom: Capacity + Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        <div class="lg:col-span-8 space-y-2">
            <div class="flex justify-between items-center">
                <h3 class="ws-section-title">Capacity Overview</h3>
                <a href="{{ route('dashboard', ['section' => 'members']) }}" class="ws-section-link">Resource directory →</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach($widgets['capacity_overview']->take(6) as $userStat)
                    <x-workspace.capacity-card :userStat="$userStat" />
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-4">
            <x-workspace.activity-card />
        </div>
    </div>
</div>
