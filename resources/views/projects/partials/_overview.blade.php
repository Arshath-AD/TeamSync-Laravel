@php
    $activeTasks   = $project->tasks->where('status', '!=', 'Completed');
    $overdueCount  = $activeTasks->filter(fn($t) => !in_array($t->status, ['Completed', 'On Hold']) && $t->deadline && \Carbon\Carbon::parse($t->deadline)->isBefore(now()->startOfDay()))->count();
    $criticalCount = $activeTasks->whereIn('priority', ['Critical', 'High'])->count();
    $nextDeadline  = $activeTasks->whereNotNull('deadline')->sortBy('deadline')->first();
    $pct           = $metrics['completion_percentage'];
    $barColor      = $pct >= 80 ? 'var(--success)' : ($pct >= 40 ? 'var(--accent)' : 'var(--warning)');
@endphp

<div class="space-y-5 fade-in">
    {{-- KPI strip --}}
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3">
        <x-workspace.metric-card value="{{ $pct }}%" label="Progress" statusColor="accent" />
        <x-workspace.metric-card value="{{ $metrics['task_count'] - $metrics['completed_task_count'] }}" label="Active Tasks" />
        <x-workspace.metric-card value="{{ $metrics['completed_task_count'] }}" label="Completed" statusColor="success" />
        <x-workspace.metric-card value="{{ $overdueCount }}"  label="Overdue"
            statusColor="{{ $overdueCount > 0 ? 'danger' : 'success' }}" />
        <x-workspace.metric-card value="{{ $criticalCount }}" label="High Priority"
            statusColor="{{ $criticalCount > 0 ? 'warning' : 'accent' }}" />
        <x-workspace.metric-card value="{{ $metrics['member_count'] }}" label="Team Size" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

        {{-- Priority queue --}}
        <div class="lg:col-span-8 space-y-2">
            <div class="flex justify-between items-center">
                <h3 class="ts-section-title">Priority Queue</h3>
                <a href="{{ route('projects.show', [$project, 'tab' => 'tasks']) }}" class="ts-section-link">All tasks →</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @forelse($activeTasks->sortBy('deadline')->take(6) as $task)
                    <x-workspace.task-card :task="$task" />
                @empty
                    <div class="col-span-full ts-empty">No active tasks in this workspace.</div>
                @endforelse
            </div>
        </div>

        {{-- Sidebar meta --}}
        <div class="lg:col-span-4 space-y-3">

            {{-- Health panel --}}
            <div class="ts-panel p-3">
                <h3 class="ts-section-title mb-3">Workspace Health</h3>
                <div class="flex items-end justify-between mb-2">
                    <span class="text-2xl font-bold tabular" style="color:var(--text)">{{ $pct }}%</span>
                    @if($overdueCount > 0)
                        <span class="ts-badge ts-badge-danger">{{ $overdueCount }} overdue</span>
                    @else
                        <span class="ts-badge ts-badge-success">On track</span>
                    @endif
                </div>
                <div class="ts-progress-track mb-4" style="height:6px">
                    <div class="ts-progress-fill" style="width:{{ $pct }}%;background:{{ $barColor }};height:6px"></div>
                </div>
                <div class="space-y-2 text-xs">
                    @foreach([
                        ['Total',       $metrics['task_count'],           null],
                        ['Completed',   $metrics['completed_task_count'], 'var(--success)'],
                        ['Next deadline', $nextDeadline ? \Carbon\Carbon::parse($nextDeadline->deadline)->format('M j') : '—', null],
                    ] as [$label, $value, $color])
                        <div class="flex justify-between">
                            <span style="color:var(--secondary)">{{ $label }}</span>
                            <span class="font-medium tabular" style="color:{{ $color ?? 'var(--text)' }}">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Details panel --}}
            <div class="ts-panel p-3">
                <h3 class="ts-section-title mb-3">Details</h3>
                <div class="flex items-center gap-2 mb-3">
                    <div class="ts-avatar w-7 h-7 text-[10px]">
                        {{ strtoupper(substr($project->lead->name ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <div class="text-xs font-medium" style="color:var(--text)">{{ $project->lead->name ?? 'Unassigned' }}</div>
                        <div class="text-[10px]" style="color:var(--secondary)">Project Lead</div>
                    </div>
                </div>
                <p class="text-xs leading-relaxed" style="color:var(--secondary)">
                    @if($project->description)
                        {{ \Illuminate\Support\Str::limit($project->description, 160) }}
                    @else
                        <span class="italic opacity-50">No description provided.</span>
                    @endif
                </p>
                <div class="mt-3 pt-3 text-[10px]" style="border-top:1px solid var(--border);color:var(--secondary)">
                    Created {{ $project->created_at->format('M j, Y') }}
                </div>
            </div>
        </div>
    </div>
</div>
