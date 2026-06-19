@php
    $overloaded  = $members->filter(fn($m) => $m->active_tasks > 8)->count();
    $available   = $members->filter(fn($m) => $m->active_tasks <= 4)->count();
    $totalActive = $members->sum('active_tasks');
@endphp

<div class="space-y-5 fade-in">

    {{-- Resource KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <x-workspace.metric-card value="{{ $members->count() }}" label="Team Members" />
        <x-workspace.metric-card value="{{ $totalActive }}"      label="Active Tasks"  statusColor="accent" />
        <x-workspace.metric-card value="{{ $overloaded }}"       label="Overloaded"
            statusColor="{{ $overloaded > 0 ? 'danger' : 'success' }}"
            trend="{{ $overloaded > 0 ? '⚠ Review' : '' }}" />
        <x-workspace.metric-card value="{{ $available }}"        label="Available"     statusColor="success" />
    </div>

    {{-- Workforce load bar --}}
    <div class="ts-panel p-3">
        <div class="flex items-center justify-between mb-2">
            <span class="ts-section-title">Workforce Load Distribution</span>
            <span class="text-[10px]" style="color:var(--secondary)">{{ $members->count() }} resources tracked</span>
        </div>

        {{-- Segmented bar --}}
        <div class="flex gap-0.5 h-2 rounded overflow-hidden">
            @foreach($members as $member)
                @php
                    $w = max(4, ($member->active_tasks / max(1, $totalActive)) * 100);
                    $c = match(true) {
                        $member->active_tasks > 8 => 'var(--danger)',
                        $member->active_tasks > 4 => 'var(--warning)',
                        $member->active_tasks > 0 => 'var(--accent)',
                        default                   => 'var(--elevated)',
                    };
                @endphp
                <div style="width:{{ $w }}%;background:{{ $c }};transition:width 0.5s"
                     title="{{ $member->name }}: {{ $member->active_tasks }} tasks"></div>
            @endforeach
        </div>

        {{-- Legend --}}
        <div class="flex gap-4 mt-2 text-[10px]" style="color:var(--secondary)">
            @foreach([
                ['Overloaded', 'var(--danger)'],
                ['Busy',       'var(--warning)'],
                ['Active',     'var(--accent)'],
                ['Idle',       'var(--elevated)'],
            ] as [$label, $color])
                <span class="flex items-center gap-1">
                    <span class="h-2 w-2 rounded-sm inline-block" style="background:{{ $color }};border:1px solid var(--border)"></span>
                    {{ $label }}
                </span>
            @endforeach
        </div>
    </div>

    {{-- Member grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
        @foreach($members as $member)
            <x-workspace.member-card
                :member="$member"
                :metrics="[
                    'active_tasks'      => $member->active_tasks,
                    'completed_tasks'   => $member->completed_tasks,
                    'assigned_projects' => $member->project_count,
                ]"
            />
        @endforeach
    </div>
</div>
