<div class="space-y-5 fade-in">

    {{-- ── KPI Strip ─────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
        <x-workspace.metric-card value="{{ $metrics['total_tasks'] }}"      label="My Tasks" />
        <x-workspace.metric-card value="{{ $metrics['active_tasks'] }}"     label="Active"      statusColor="accent" />
        <x-workspace.metric-card value="{{ $metrics['completed_tasks'] }}"  label="Completed"   statusColor="success" />
        <x-workspace.metric-card value="{{ $metrics['due_today'] }}"        label="Due Today"
            statusColor="{{ $metrics['due_today'] > 0 ? 'warning' : 'accent' }}" />
        <x-workspace.metric-card value="{{ $metrics['overdue_tasks'] }}"    label="Overdue"
            statusColor="danger"
            trend="{{ $metrics['overdue_tasks'] > 0 ? '⚠ Urgent' : '' }}" />
    </div>

    {{-- ── Mid: Focus Queue + Completion Ring ───────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

        {{-- Needs Attention --}}
        <div class="lg:col-span-8 space-y-2">
            <div class="flex justify-between items-center">
                <h3 class="ts-section-title">Needs Attention</h3>
                <a href="{{ route('tasks.myTasks') }}" class="ts-section-link">All my tasks →</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @forelse($widgets['my_active_tasks'] as $task)
                    <x-workspace.task-card :task="$task" />
                @empty
                    <div class="col-span-full ts-empty">You're all caught up. 🎉</div>
                @endforelse
            </div>
        </div>

        {{-- Completion Ring --}}
        <div class="lg:col-span-4">
            <div class="ts-panel p-4 h-full flex flex-col items-center justify-center text-center">
                <div class="relative w-24 h-24 mb-3">
                    <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                        <circle stroke-width="6" cx="50" cy="50" r="42" fill="transparent"
                                style="stroke:var(--elevated)"></circle>
                        <circle stroke-width="6" stroke-linecap="round" cx="50" cy="50" r="42" fill="transparent"
                                style="stroke:var(--accent)"
                                stroke-dasharray="263.9"
                                stroke-dashoffset="{{ 263.9 - (263.9 * $metrics['completion_percentage']) / 100 }}">
                        </circle>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-xl font-bold tabular" style="color:var(--text)">
                            {{ $metrics['completion_percentage'] }}%
                        </span>
                    </div>
                </div>
                <h4 class="text-xs font-semibold" style="color:var(--text)">Completion Rate</h4>
                <p class="text-[10px] mt-1" style="color:var(--secondary)">
                    {{ $metrics['completed_tasks'] }}/{{ $metrics['total_tasks'] }} tasks done
                </p>
                <div class="grid grid-cols-2 gap-2 w-full mt-4 text-[10px]">
                    <div class="rounded px-2 py-1.5" style="background:var(--elevated)">
                        <div style="color:var(--secondary)">Projects</div>
                        <div class="font-semibold" style="color:var(--text)">{{ $metrics['assigned_projects'] }}</div>
                    </div>
                    <div class="rounded px-2 py-1.5" style="background:var(--elevated)">
                        <div style="color:var(--secondary)">Active</div>
                        <div class="font-semibold" style="color:var(--text)">{{ $metrics['my_active_projects'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Upcoming Deadlines ─────────────────────────────────────────── --}}
    <div class="space-y-2">
        <div class="flex justify-between items-center">
            <h3 class="ts-section-title">Upcoming Deadlines</h3>
            <a href="{{ route('tasks.index') }}" class="ts-section-link">Board view →</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-2">
            @forelse($widgets['upcoming_deadlines'] as $task)
                <x-workspace.task-card :task="$task" />
            @empty
                <div class="col-span-full ts-empty">No upcoming deadlines.</div>
            @endforelse
        </div>
    </div>

</div>
