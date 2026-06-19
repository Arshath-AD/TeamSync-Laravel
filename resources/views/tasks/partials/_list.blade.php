<div class="ts-panel overflow-hidden">
    {{-- Header --}}
    <div class="flex items-center justify-between px-3 py-2.5" style="border-bottom:1px solid var(--border)">
        <span class="ts-section-title">All Tasks</span>
        <span class="text-[10px]" style="color:var(--secondary)">{{ $tasks->count() }} tasks</span>
    </div>

    {{-- Column headers --}}
    <div class="grid px-3 py-1.5 text-[10px] font-semibold uppercase tracking-wider"
         style="grid-template-columns:60px 1fr 120px 80px 80px 64px;color:var(--secondary);border-bottom:1px solid var(--border)">
        <span>Priority</span>
        <span>Task</span>
        <span class="hidden sm:block">Project</span>
        <span class="hidden md:block">Assignee</span>
        <span>Status</span>
        <span class="text-right">Due</span>
    </div>

    <div style="divide-y: var(--border)">
        @forelse($tasks as $task)
            @php
                $isOverdue = $task->deadline && \Carbon\Carbon::parse($task->deadline)->isBefore(now()->startOfDay()) && $task->status !== 'Completed' && $task->status !== 'On Hold';
                $priorityColor = match($task->priority) {
                    'Critical','High' => 'var(--danger)',
                    'Medium'          => 'var(--warning)',
                    'Low'             => 'var(--success)',
                    default           => 'var(--secondary)',
                };
                $priorityBg = match($task->priority) {
                    'Critical','High' => 'rgba(239,68,68,0.1)',
                    'Medium'          => 'rgba(245,158,11,0.1)',
                    'Low'             => 'rgba(34,197,94,0.1)',
                    default           => 'var(--elevated)',
                };
                $priorityBorder = match($task->priority) {
                    'Critical','High' => 'rgba(239,68,68,0.3)',
                    'Medium'          => 'rgba(245,158,11,0.3)',
                    'Low'             => 'rgba(34,197,94,0.3)',
                    default           => 'var(--border)',
                };
            @endphp
            <div class="grid items-center px-3 py-2 transition-colors"
                 style="grid-template-columns:60px 1fr 120px 80px 80px 64px;border-bottom:1px solid var(--border)"
                 onmouseover="this.style.background='var(--elevated)'" onmouseout="this.style.background=''">

                {{-- Priority --}}
                <div class="pr-2">
                    <span class="ts-badge"
                          style="color:{{ $priorityColor }};background:{{ $priorityBg }};border-color:{{ $priorityBorder }}">
                        {{ $task->priority }}
                    </span>
                </div>

                {{-- Task name --}}
                <a href="{{ route('tasks.edit', $task) }}"
                   class="text-xs font-medium truncate transition-colors pr-3"
                   style="color:var(--text)"
                   onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text)'">
                    {{ $task->task_name }}
                </a>

                {{-- Project --}}
                <div class="pr-3 hidden sm:block">
                    @if($task->project)
                        <span class="text-[10px] truncate block" style="color:var(--secondary)">
                            {{ $task->project->project_name }}
                        </span>
                    @else
                        <span class="ts-badge text-[9px]" style="color:var(--secondary);background:var(--elevated);border-color:var(--border)">
                            No Project
                        </span>
                    @endif
                </div>

                {{-- Assignee --}}
                <span class="text-[10px] truncate pr-3 hidden md:block" style="color:var(--secondary)">
                    {{ $task->assignee->name ?? '—' }}
                </span>

                {{-- Status quick-update --}}
                <form action="{{ route('tasks.updateStatus', $task) }}" method="POST" data-status-form>
                    @csrf @method('PATCH')
                    <select name="status"
                            class="text-[10px] rounded py-0.5 px-1 border transition-colors cursor-pointer"
                            style="background:var(--elevated);border-color:var(--border);color:var(--text);outline:none">
                        <option value="Pending"     {{ $task->status === 'Pending'     ? 'selected' : '' }}>Todo</option>
                        <option value="In Progress" {{ $task->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="On Hold"     {{ $task->status === 'On Hold'     ? 'selected' : '' }}>On Hold</option>
                        <option value="Completed"   {{ $task->status === 'Completed'   ? 'selected' : '' }}>Done</option>
                    </select>
                </form>

                {{-- Due date --}}
                <span class="text-[10px] text-right tabular"
                      style="color:{{ $isOverdue ? 'var(--danger)' : 'var(--secondary)' }};font-weight:{{ $isOverdue ? '600' : '400' }}">
                    {{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('M j') : '—' }}
                </span>
            </div>
        @empty
            <div class="ts-empty m-3">No tasks found.</div>
        @endforelse
    </div>
</div>
