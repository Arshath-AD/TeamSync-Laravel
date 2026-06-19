<div class="space-y-5 fade-in">

    {{-- ── KPI Strip ─────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-3">
        <x-workspace.metric-card value="{{ $metrics['total_projects'] }}"  label="Workspaces" />
        <x-workspace.metric-card value="{{ $metrics['active_projects'] }}" label="Active"      statusColor="accent" />
        <x-workspace.metric-card value="{{ $metrics['total_users'] }}"     label="Team Size" />
        <x-workspace.metric-card value="{{ $metrics['total_tasks'] }}"     label="Total Tasks" />
        <x-workspace.metric-card value="{{ $metrics['completed_tasks'] }}" label="Completed"   statusColor="success" />
        <x-workspace.metric-card value="{{ $metrics['overdue_tasks'] }}"   label="Overdue"
            statusColor="{{ $metrics['overdue_tasks'] > 0 ? 'danger' : 'success' }}"
            trend="{{ $metrics['overdue_tasks'] > 0 ? '⚠ Action' : '' }}" />
    </div>

    {{-- ── Mid row: Active Workspaces + Upcoming Deadlines ──────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

        {{-- Active Workspaces --}}
        <div class="lg:col-span-8 space-y-2">
            <div class="flex justify-between items-center">
                <h3 class="ts-section-title">Active Workspaces</h3>
                <a href="{{ route('projects.index') }}" class="ts-section-link">View all →</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                @forelse($widgets['recent_projects'] as $project)
                    <x-workspace.project-card :project="$project" :url="route('projects.show', $project)" />
                @empty
                    <div class="col-span-full ts-empty">No active workspaces yet.</div>
                @endforelse
            </div>
        </div>

        {{-- Upcoming Deadlines --}}
        <div class="lg:col-span-4 space-y-2">
            <div class="flex justify-between items-center">
                <h3 class="ts-section-title">Upcoming Deadlines</h3>
                <a href="{{ route('tasks.index') }}" class="ts-section-link">Board →</a>
            </div>
            <div class="space-y-1.5 max-h-[360px] overflow-y-auto ts-scroll pr-0.5">
                @forelse($widgets['upcoming_deadlines'] as $task)
                    <x-workspace.task-card :task="$task" />
                @empty
                    <div class="ts-empty">No upcoming deadlines.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── Bottom row: Capacity + Activity ──────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

        {{-- Capacity Overview --}}
        <div class="lg:col-span-8 space-y-2">
            <div class="flex justify-between items-center">
                <h3 class="ts-section-title">Workforce Load Distribution</h3>
                <a href="{{ route('dashboard', ['section' => 'members']) }}" class="ts-section-link">Resource directory →</a>
            </div>

            {{-- Status legend --}}
            <div class="flex items-center gap-4 text-[10px]" style="color:var(--secondary)">
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full inline-block" style="background:var(--danger)"></span>Overloaded</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full inline-block" style="background:var(--warning)"></span>Busy</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full inline-block" style="background:var(--success)"></span>Active</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full inline-block" style="background:var(--border)"></span>Idle</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach($widgets['capacity_overview']->take(6) as $userModel)
                    <x-workspace.member-card
                        :member="$userModel"
                        :metrics="[
                            'active_tasks'    => $userModel->active_tasks,
                            'completed_tasks' => $userModel->completed_tasks,
                            'project_count'   => null,
                        ]"
                    />
                @endforeach
            </div>
        </div>

        {{-- Activity Feed --}}
        <div class="lg:col-span-4">
            <x-workspace.activity-card />
        </div>
    </div>

</div>
