<div class="ws-panel overflow-hidden">
    <div class="ws-panel-header">
        <span class="ws-section-title">All Tasks</span>
        <a href="{{ route('tasks.index') }}" class="ws-section-link">Board view →</a>
    </div>
    <div class="divide-y divide-workspace-border">
        @forelse($tasks as $task)
            <div class="px-3 py-2 hover:bg-workspace-elevated/50 transition-colors flex items-center gap-3">
                <div class="w-16 flex-shrink-0">
                    <span class="ws-badge {{ match($task->priority) {
                        'Critical', 'High' => 'text-workspace-danger border-workspace-danger/30 bg-workspace-danger/10',
                        'Medium' => 'text-workspace-warning border-workspace-warning/30 bg-workspace-warning/10',
                        'Low' => 'text-workspace-success border-workspace-success/30 bg-workspace-success/10',
                        default => 'text-workspace-secondary border-workspace-border bg-workspace-elevated',
                    } }}">{{ $task->priority }}</span>
                </div>
                <a href="{{ route('tasks.edit', $task) }}" class="flex-1 text-xs font-medium text-workspace-text hover:text-workspace-accent truncate">{{ $task->task_name }}</a>
                <span class="text-[10px] text-workspace-secondary w-28 truncate hidden sm:block">{{ $task->project->project_name ?? '—' }}</span>
                <form action="{{ route('tasks.updateStatus', $task) }}" method="POST" class="flex-shrink-0">
                    @csrf @method('PATCH')
                    <select name="status" onchange="this.form.submit()" class="text-[10px] bg-workspace-background border-workspace-border text-workspace-text rounded py-0.5 px-1">
                        <option value="Pending" {{ $task->status === 'Pending' ? 'selected' : '' }}>Todo</option>
                        <option value="In Progress" {{ $task->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Completed" {{ $task->status === 'Completed' ? 'selected' : '' }}>Done</option>
                    </select>
                </form>
                <span class="text-[10px] text-workspace-secondary w-20 truncate hidden md:block">{{ $task->assignee->name ?? '—' }}</span>
                <span class="text-[10px] w-16 text-right flex-shrink-0 {{ $task->deadline && \Carbon\Carbon::parse($task->deadline)->isBefore(now()->startOfDay()) && $task->status !== 'Completed' ? 'text-workspace-danger font-semibold' : 'text-workspace-secondary' }}">
                    {{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('M j') : '—' }}
                </span>
            </div>
        @empty
            <div class="ws-empty-state m-3">No tasks found.</div>
        @endforelse
    </div>
</div>
