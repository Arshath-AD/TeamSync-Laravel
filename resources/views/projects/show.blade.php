<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full gap-3">
            <div class="flex items-center gap-2 min-w-0">
                <a href="{{ route('projects.index') }}"
                   class="flex-shrink-0 transition-colors"
                   style="color:var(--secondary)"
                   onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--secondary)'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <span class="text-[10px] mx-1" style="color:var(--muted)">/</span>
                <span class="truncate">{{ $project->project_name }}</span>

                {{-- Priority badge --}}
                @php
                    $p = $project->priority ?? 'Medium';
                    $pBadge = match($p) {
                        'Critical' => ['color' => 'var(--danger)',  'bg' => 'rgba(239,68,68,0.1)',  'border' => 'rgba(239,68,68,0.3)'],
                        'High'     => ['color' => 'var(--warning)', 'bg' => 'rgba(245,158,11,0.1)', 'border' => 'rgba(245,158,11,0.3)'],
                        'Medium'   => ['color' => 'var(--accent)',  'bg' => 'rgba(26,107,138,0.1)', 'border' => 'rgba(26,107,138,0.3)'],
                        default    => ['color' => 'var(--success)', 'bg' => 'rgba(34,197,94,0.08)', 'border' => 'rgba(34,197,94,0.3)'],
                    };
                @endphp
                <span class="ts-badge ml-1 flex-shrink-0"
                      style="color:{{ $pBadge['color'] }};background:{{ $pBadge['bg'] }};border-color:{{ $pBadge['border'] }}">
                    {{ $p }}
                </span>
            </div>

            <div class="flex items-center gap-2">
                {{-- Admin Priority Quick-Actions --}}
                @if(Auth::user()->role === 'admin')
                    <div class="flex items-center gap-1">
                        <form action="{{ route('projects.updatePriority', $project) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="direction" value="down">
                            <button type="submit" class="ts-btn-ghost"
                                    title="Lower Priority"
                                    style="padding:4px 8px;font-size:11px;"
                                    {{ ($project->priority ?? 'Medium') === 'Low' ? 'disabled' : '' }}>
                                ▼ Lower
                            </button>
                        </form>
                        <form action="{{ route('projects.updatePriority', $project) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="direction" value="up">
                            <button type="submit" class="ts-btn-ghost"
                                    title="Increase Priority"
                                    style="padding:4px 8px;font-size:11px;"
                                    {{ ($project->priority ?? 'Medium') === 'Critical' ? 'disabled' : '' }}>
                                ▲ Raise
                            </button>
                        </form>
                    </div>
                @endif

                <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}" class="ts-btn-secondary">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Task
                </a>
                <a href="{{ route('projects.edit', $project) }}" class="ts-btn-secondary">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Settings
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4 fade-in">

        {{-- Tabs --}}
        <div style="border-bottom:1px solid var(--border)">
            <nav class="-mb-px flex gap-6">
                @foreach(['overview' => 'Overview', 'tasks' => 'Tasks', 'members' => 'Members', 'activity' => 'Activity'] as $tab => $label)
                    <a href="{{ route('projects.show', [$project, 'tab' => $tab]) }}"
                       class="ts-tab {{ $activeTab === $tab ? 'active' : '' }} {{ $tab === 'activity' ? 'opacity-60' : '' }}">
                        {{ $label }}
                        @if($tab === 'tasks')
                            <span class="ml-1.5 text-[10px] tabular px-1.5 py-0.5 rounded"
                                  style="background:var(--elevated);border:1px solid var(--border);color:var(--secondary)">
                                {{ $metrics['task_count'] - $metrics['completed_task_count'] }}
                            </span>
                        @elseif($tab === 'members')
                            <span class="ml-1.5 text-[10px] tabular px-1.5 py-0.5 rounded"
                                  style="background:var(--elevated);border:1px solid var(--border);color:var(--secondary)">
                                {{ $metrics['member_count'] }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </nav>
        </div>

        @include("projects.partials._{$activeTab}")
    </div>
</x-app-layout>
