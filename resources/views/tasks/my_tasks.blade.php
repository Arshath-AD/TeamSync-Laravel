<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full gap-3">
            <span>My Tasks</span>
            <a href="{{ route('tasks.create') }}" class="ts-btn-primary">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Task
            </a>
        </div>
    </x-slot>

    @php $activeCount = $tasks->where('status', '!=', 'Completed')->count(); @endphp

    <div class="space-y-5 fade-in">
        {{-- KPI strip --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <x-workspace.metric-card value="{{ $tasks->count() }}"  label="Total" />
            <x-workspace.metric-card value="{{ $activeCount }}"     label="Active"   statusColor="accent" />
            <x-workspace.metric-card value="{{ count($overdue) }}"  label="Overdue"
                statusColor="{{ count($overdue) > 0 ? 'danger' : 'success' }}" />
            <x-workspace.metric-card value="{{ count($upcoming) }}" label="Upcoming" statusColor="warning" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

            {{-- Overdue (only shown when there are overdue tasks) --}}
            @if(count($overdue) > 0)
                <div class="lg:col-span-4">
                    <div class="ts-panel h-full" style="border-color:rgba(239,68,68,0.3)">
                        <div class="flex items-center justify-between px-3 py-2.5"
                             style="border-bottom:1px solid rgba(239,68,68,0.2);background:rgba(239,68,68,0.04)">
                            <h3 class="text-xs font-semibold uppercase tracking-wider flex items-center gap-1.5"
                                style="color:var(--danger)">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Overdue
                            </h3>
                            <span class="text-[10px] text-white px-1.5 py-0.5 rounded tabular"
                                  style="background:var(--danger)">{{ count($overdue) }}</span>
                        </div>
                        <div class="p-2 space-y-1.5 max-h-72 overflow-y-auto ts-scroll">
                            @foreach($overdue as $task)
                                <x-workspace.task-card :task="$task" />
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Upcoming --}}
            <div class="{{ count($overdue) > 0 ? 'lg:col-span-4' : 'lg:col-span-6' }}">
                <div class="ts-panel h-full">
                    <div class="flex items-center justify-between px-3 py-2.5" style="border-bottom:1px solid var(--border)">
                        <h3 class="ts-section-title">Upcoming</h3>
                        <span class="text-[10px] tabular px-1.5 py-0.5 rounded"
                              style="background:var(--elevated);border:1px solid var(--border);color:var(--secondary)">
                            {{ count($upcoming) }}
                        </span>
                    </div>
                    <div class="p-2 space-y-1.5 max-h-96 overflow-y-auto ts-scroll">
                        @forelse($upcoming as $task)
                            <x-workspace.task-card :task="$task" />
                        @empty
                            <div class="ts-empty py-6">No upcoming tasks.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- All Active --}}
            <div class="{{ count($overdue) > 0 ? 'lg:col-span-4' : 'lg:col-span-6' }}">
                <div class="ts-panel h-full">
                    <div class="flex items-center justify-between px-3 py-2.5" style="border-bottom:1px solid var(--border)">
                        <h3 class="ts-section-title">All Active</h3>
                        <span class="text-[10px] tabular px-1.5 py-0.5 rounded"
                              style="background:var(--elevated);border:1px solid var(--border);color:var(--secondary)">
                            {{ $activeCount }}
                        </span>
                    </div>
                    <div class="p-2 space-y-1.5 max-h-96 overflow-y-auto ts-scroll">
                        @forelse($tasks->where('status', '!=', 'Completed')->sortBy('deadline') as $task)
                            <x-workspace.task-card :task="$task" />
                        @empty
                            <div class="ts-empty py-6">All caught up! 🎉</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
