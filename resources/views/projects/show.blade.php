<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('projects.index') }}" class="text-workspace-secondary hover:text-workspace-text transition-colors flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <span class="truncate">{{ $project->project_name }}</span>
            </div>
            <a href="{{ route('projects.edit', $project) }}" class="ws-btn-secondary flex-shrink-0">Settings</a>
        </div>
    </x-slot>

    <div class="space-y-4">
        <!-- Workspace Tabs -->
        <div class="border-b border-workspace-border">
            <nav class="-mb-px flex gap-6" aria-label="Tabs">
                @foreach(['overview' => 'Overview', 'tasks' => 'Tasks', 'members' => 'Members', 'activity' => 'Activity'] as $tab => $label)
                    <a href="{{ route('projects.show', [$project, 'tab' => $tab]) }}"
                       class="ws-tab {{ $activeTab === $tab ? 'ws-tab-active' : 'ws-tab-inactive' }} {{ $tab === 'activity' ? 'opacity-60' : '' }}">
                        {{ $label }}
                        @if($tab === 'tasks')
                            <span class="ml-1.5 bg-workspace-elevated text-workspace-text py-0.5 px-1.5 rounded text-[10px] border border-workspace-border tabular-nums">{{ $metrics['task_count'] - $metrics['completed_task_count'] }}</span>
                        @elseif($tab === 'members')
                            <span class="ml-1.5 bg-workspace-elevated text-workspace-text py-0.5 px-1.5 rounded text-[10px] border border-workspace-border tabular-nums">{{ $metrics['member_count'] }}</span>
                        @endif
                    </a>
                @endforeach
            </nav>
        </div>

        @if(session('success'))
            <div class="bg-workspace-elevated border border-workspace-success text-workspace-success px-3 py-2 rounded text-xs font-medium">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-workspace-elevated border border-workspace-danger text-workspace-danger px-3 py-2 rounded text-xs font-medium">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="bg-workspace-elevated border border-workspace-danger text-workspace-danger px-3 py-2 rounded text-xs font-medium">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        @include("projects.partials._{$activeTab}")
    </div>
</x-app-layout>
