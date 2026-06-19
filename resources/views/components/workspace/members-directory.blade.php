@php
    $overloaded = $members->filter(fn($m) => $m->active_tasks > 8)->count();
    $available = $members->filter(fn($m) => $m->active_tasks <= 4)->count();
    $totalActive = $members->sum('active_tasks');
@endphp

<div class="space-y-4">
    <!-- Resource KPIs -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <x-workspace.metric-card value="{{ $members->count() }}" label="Team Members" />
        <x-workspace.metric-card value="{{ $totalActive }}" label="Active Tasks" statusColor="accent" />
        <x-workspace.metric-card value="{{ $overloaded }}" label="Overloaded" statusColor="danger" trend="{{ $overloaded > 0 ? '+Review' : '' }}" />
        <x-workspace.metric-card value="{{ $available }}" label="Available" statusColor="success" />
    </div>

    <!-- Capacity summary bar -->
    <div class="ws-panel p-3">
        <div class="flex items-center justify-between mb-2">
            <span class="ws-section-title">Workforce Load Distribution</span>
            <span class="text-[10px] text-workspace-secondary">{{ $members->count() }} resources tracked</span>
        </div>
        <div class="flex gap-0.5 h-2 rounded overflow-hidden">
            @foreach($members as $member)
                @php
                    $segmentWidth = max(4, ($member->active_tasks / max(1, $totalActive)) * 100);
                    $segmentClass = match(true) {
                        $member->active_tasks > 8 => 'bg-workspace-danger',
                        $member->active_tasks > 4 => 'bg-workspace-warning',
                        $member->active_tasks > 0 => 'bg-workspace-accent',
                        default => 'bg-workspace-elevated',
                    };
                @endphp
                <div class="{{ $segmentClass }} transition-all" style="width: {{ $segmentWidth }}%" title="{{ $member->name }}: {{ $member->active_tasks }} tasks"></div>
            @endforeach
        </div>
        <div class="flex gap-4 mt-2 text-[10px] text-workspace-secondary">
            <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-sm bg-workspace-danger"></span> Overloaded</span>
            <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-sm bg-workspace-warning"></span> Busy</span>
            <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-sm bg-workspace-accent"></span> Active</span>
            <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-sm bg-workspace-elevated border border-workspace-border"></span> Idle</span>
        </div>
    </div>

    <!-- Member grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
        @foreach($members as $member)
            <x-workspace.member-card
                :member="$member"
                :metrics="[
                    'active_tasks' => $member->active_tasks,
                    'completed_tasks' => $member->completed_tasks,
                    'assigned_projects' => $member->project_count,
                ]"
            />
        @endforeach
    </div>
</div>
