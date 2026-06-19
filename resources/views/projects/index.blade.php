<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full gap-4">
            <span>Workspace Directory</span>
            <a href="{{ route('projects.create') }}" class="ws-btn-primary">+ New Workspace</a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 bg-workspace-elevated border border-workspace-success text-workspace-success px-3 py-2 rounded text-xs font-medium">
            {{ session('success') }}
        </div>
    @endif

    @php
        $atRisk = $projects->filter(fn($p) => ($p->metrics['completion_percentage'] ?? 0) < 30)->count();
        $activeTotal = $projects->sum(fn($p) => ($p->metrics['task_count'] ?? 0) - ($p->metrics['completed_task_count'] ?? 0));
    @endphp

    <div class="space-y-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <x-workspace.metric-card value="{{ $projects->count() }}" label="Workspaces" />
            <x-workspace.metric-card value="{{ $activeTotal }}" label="Active Tasks" statusColor="accent" />
            <x-workspace.metric-card value="{{ $atRisk }}" label="At Risk" statusColor="{{ $atRisk > 0 ? 'danger' : 'success' }}" />
            <x-workspace.metric-card value="{{ $projects->avg(fn($p) => $p->metrics['completion_percentage'] ?? 0) ? round($projects->avg(fn($p) => $p->metrics['completion_percentage'] ?? 0)) : 0 }}%" label="Avg Progress" statusColor="success" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
            @forelse($projects as $project)
                <x-workspace.project-card :project="$project" :url="route('projects.show', $project)" />
            @empty
                <div class="col-span-full py-10 ws-empty-state">
                    <p class="mb-2">No workspaces yet.</p>
                    <a href="{{ route('projects.create') }}" class="ws-btn-primary">Create your first workspace</a>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
