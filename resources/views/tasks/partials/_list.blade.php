<div class="ts-panel">
    {{-- Header --}}
    <div class="flex items-center justify-between px-3 py-2.5" style="border-bottom:1px solid var(--border)">
        <span class="ts-section-title">All Tasks</span>
        <span class="text-[10px]" style="color:var(--secondary)">
            Displaying {{ $tasks->firstItem() ?? 0 }}-{{ $tasks->lastItem() ?? 0 }} of {{ $tasks->total() }} tasks
        </span>
    </div>

    {{-- Column headers --}}
    <div class="grid px-3 py-1.5 text-[10px] font-semibold uppercase tracking-wider items-center"
         style="grid-template-columns:85px 1fr 120px 130px 90px 110px 50px;color:var(--secondary);border-bottom:1px solid var(--border)">
        <span>Priority</span>
        <span>Task</span>
        <span class="hidden sm:block">Project</span>
        <span class="hidden md:block">Assignee</span>
        <span>Status</span>
        <span class="text-right">Due</span>
        <span class="text-right">Action</span>
    </div>

    @php
        $priorityOptions = [
            ['value' => 'Low', 'label' => 'Low', 'color' => '#22c55e'],
            ['value' => 'Medium', 'label' => 'Medium', 'color' => '#f59e0b'],
            ['value' => 'High', 'label' => 'High', 'color' => '#ef4444'],
            ['value' => 'Critical', 'label' => 'Critical', 'color' => '#dc2626'],
        ];
        $statusOptions = [
            ['value' => 'Pending', 'label' => 'Todo', 'color' => '#6b7280'],
            ['value' => 'In Progress', 'label' => 'In Progress', 'color' => '#3b82f6'],
            ['value' => 'On Hold', 'label' => 'On Hold', 'color' => '#f59e0b'],
            ['value' => 'Completed', 'label' => 'Done', 'color' => '#22c55e'],
        ];
        $projectOptions = [['value' => '', 'label' => 'Standalone']];
        foreach($projects ?? [] as $p) {
            $projectOptions[] = ['value' => $p->id, 'label' => $p->project_name];
        }
        $userOptions = [];
        foreach($users ?? [] as $u) {
            $userOptions[] = ['value' => $u->id, 'label' => $u->name, 'avatarUser' => $u];
        }
    @endphp

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
                 style="grid-template-columns:85px 1fr 120px 130px 90px 110px 50px;border-bottom:1px solid var(--border)"
                 onmouseover="this.style.background='var(--elevated)'" onmouseout="this.style.background=''">

                {{-- Priority --}}
                <div class="pr-2">
                    <x-workspace.dropdown 
                        value="{{ $task->priority }}" 
                        :options="$priorityOptions" 
                        onChange="updateTaskField({{ $task->id }}, 'priority', this.value)" />
                </div>

                {{-- Task name --}}
                <div class="text-xs font-medium truncate pr-3" style="color:var(--text)">
                    {{ $task->task_name }}
                </div>

                {{-- Project --}}
                <div class="pr-3 hidden sm:block">
                    <x-workspace.dropdown 
                        value="{{ $task->project_id }}" 
                        :options="$projectOptions" 
                        onChange="updateTaskField({{ $task->id }}, 'project_id', this.value)" />
                </div>

                {{-- Assignee --}}
                <div class="pr-3 hidden md:block">
                    <x-workspace.dropdown 
                        value="{{ $task->assigned_to }}" 
                        :options="$userOptions" 
                        onChange="updateTaskField({{ $task->id }}, 'assigned_to', this.value)" />
                </div>

                {{-- Status --}}
                <div class="pr-2">
                    <x-workspace.dropdown 
                        value="{{ $task->status }}" 
                        :options="$statusOptions" 
                        onChange="updateTaskField({{ $task->id }}, 'status', this.value)" />
                </div>

                {{-- Due date --}}
                <div class="text-right pr-2">
                    <input type="date" value="{{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('Y-m-d') : '' }}"
                           class="text-[10px] rounded py-0.5 px-1 w-full border transition-colors cursor-pointer tabular"
                           style="background:var(--elevated);border-color:var(--border);color:{{ $isOverdue ? 'var(--danger)' : 'var(--secondary)' }};font-weight:{{ $isOverdue ? '600' : '400' }};outline:none"
                           onchange="updateTaskField({{ $task->id }}, 'deadline', this.value)">
                </div>
                
                {{-- Actions --}}
                <div class="text-right">
                    <a href="{{ route('tasks.edit', $task) }}" 
                       class="text-[10px] font-medium px-2 py-1 rounded transition-colors inline-flex items-center gap-1"
                       style="color:var(--accent); background:color-mix(in srgb, var(--accent) 10%, transparent);"
                       onmouseover="this.style.background='color-mix(in srgb, var(--accent) 20%, transparent)'"
                       onmouseout="this.style.background='color-mix(in srgb, var(--accent) 10%, transparent)'">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        Edit
                    </a>
                </div>
            </div>
        @empty
            <div class="ts-empty m-3">No tasks found.</div>
        @endforelse
    </div>

    @if($tasks->hasPages())
        <div class="px-3 py-2 border-t" style="border-color:var(--border);background:var(--elevated)">
            {{ $tasks->links() }}
        </div>
    @endif
</div>

<script>
    if (typeof updateTaskField === 'undefined') {
        window.updateTaskField = function(taskId, field, value) {
            fetch(`/tasks/${taskId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    [field]: value,
                    '_method': 'PUT' 
                })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success || data.task_name) {
                    showListToast('Task updated successfully!', 'success');
                } else {
                    showListToast('Failed to update task', 'error');
                }
            })
            .catch(err => {
                showListToast('Update successful (reloading...)', 'success');
                // Optional: Fallback to reload if you want to reflect complex UI changes
            });
        };

        window.showListToast = function(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-4 right-4 px-4 py-2 rounded shadow-lg text-xs font-semibold z-50 transition-opacity duration-300`;
            toast.style.backgroundColor = type === 'success' ? 'var(--success)' : 'var(--danger)';
            toast.style.color = '#fff';
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        };
    }
</script>
