<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full gap-3">
            <span>Workspace Directory</span>
            <a href="{{ route('projects.create') }}" class="ts-btn-primary">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Workspace
            </a>
        </div>
    </x-slot>

    @php
        $atRisk      = $projects->filter(fn($p) => ($p->metrics['completion_percentage'] ?? 0) < 30)->count();
        $activeTotal = $projects->sum(fn($p) => ($p->metrics['task_count'] ?? 0) - ($p->metrics['completed_task_count'] ?? 0));
        $avgProgress = $projects->count()
            ? round($projects->avg(fn($p) => $p->metrics['completion_percentage'] ?? 0))
            : 0;
    @endphp

    <div class="space-y-5 fade-in">
        {{-- KPI strip --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <x-workspace.metric-card value="{{ $projects->count() }}" label="Workspaces" />
            <x-workspace.metric-card value="{{ $activeTotal }}"       label="Active Tasks"  statusColor="accent" />
            <x-workspace.metric-card value="{{ $atRisk }}"            label="At Risk"
                statusColor="{{ $atRisk > 0 ? 'danger' : 'success' }}" />
            <x-workspace.metric-card value="{{ $avgProgress }}%"      label="Avg Progress"  statusColor="success" />
        </div>

        {{-- Project grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
            @forelse($projects as $project)
                <x-workspace.project-card :project="$project" :url="route('projects.show', $project)" />
            @empty
                <div class="col-span-full ts-empty py-16">
                    <svg class="w-8 h-8 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <p class="mb-3">No workspaces yet.</p>
                    <a href="{{ route('projects.create') }}" class="ts-btn-primary">Create your first workspace</a>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
