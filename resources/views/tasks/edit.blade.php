<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 min-w-0">
            <a href="{{ url()->previous() }}" class="flex-shrink-0 transition-colors"
               style="color:var(--secondary)"
               onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--secondary)'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <span class="text-[10px] mx-1" style="color:var(--muted)">/</span>
            <span class="truncate">{{ $task->task_name }}</span>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto fade-in">
        <div class="ts-panel p-5">
            <div class="mb-4 pb-4" style="border-bottom:1px solid var(--border)">
                <h2 class="text-sm font-semibold" style="color:var(--text)">Edit Task</h2>
                <p class="text-xs mt-0.5" style="color:var(--secondary)">Update task details, assignment, and status.</p>
            </div>

            @php
                $projectOptions = [['value' => '', 'label' => 'Standalone']];
                foreach($projects as $p) {
                    $projectOptions[] = ['value' => $p->id, 'label' => $p->project_name];
                }
                
                $userOptions = [];
                foreach($users as $u) {
                    $userOptions[] = ['value' => $u->id, 'label' => $u->name, 'avatarUser' => $u];
                }

                $statusOptions = [
                    ['value' => 'Pending', 'label' => 'Pending', 'color' => '#6b7280'],
                    ['value' => 'In Progress', 'label' => 'In Progress', 'color' => '#3b82f6'],
                    ['value' => 'On Hold', 'label' => 'On Hold', 'color' => '#f59e0b'],
                    ['value' => 'Completed', 'label' => 'Completed', 'color' => '#22c55e'],
                ];

                $priorityOptions = [
                    ['value' => 'Low', 'label' => 'Low', 'color' => '#22c55e'],
                    ['value' => 'Medium', 'label' => 'Medium', 'color' => '#f59e0b'],
                    ['value' => 'High', 'label' => 'High', 'color' => '#ef4444'],
                    ['value' => 'Critical', 'label' => 'Critical', 'color' => '#dc2626'],
                ];
            @endphp

            <form action="{{ route('tasks.update', $task) }}" method="POST" class="space-y-4">
                @csrf @method('PUT')

                <div>
                    <label class="ts-label" for="task_name">Task Name</label>
                    <input id="task_name" type="text" name="task_name"
                           value="{{ old('task_name', $task->task_name) }}"
                           class="ts-input" required autofocus>
                    @error('task_name')
                        <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="ts-label" for="description">Description</label>
                    <textarea id="description" name="description" class="ts-input" rows="3"
                              placeholder="Task description…">{{ old('description', $task->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="ts-label" for="project_id">Workspace</label>
                        <x-workspace.dropdown 
                            name="project_id" 
                            id="project_id" 
                            :value="old('project_id', $task->project_id)" 
                            :options="$projectOptions" 
                            placeholder="Standalone" />
                        @error('project_id')
                            <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="ts-label" for="assigned_to">Assign To</label>
                        <x-workspace.dropdown 
                            name="assigned_to" 
                            id="assigned_to" 
                            :value="old('assigned_to', $task->assigned_to)" 
                            :options="$userOptions" 
                            placeholder="Select member…" 
                            required="true" />
                        @error('assigned_to')
                            <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="ts-label" for="status">Status</label>
                        <x-workspace.dropdown 
                            name="status" 
                            id="status" 
                            :value="old('status', $task->status)" 
                            :options="$statusOptions" 
                            required="true" />
                    </div>
                    <div>
                        <label class="ts-label" for="priority">Priority</label>
                        <x-workspace.dropdown 
                            name="priority" 
                            id="priority" 
                            :value="old('priority', $task->priority)" 
                            :options="$priorityOptions" 
                            required="true" />
                    </div>
                    <div>
                        <label class="ts-label" for="deadline">Deadline</label>
                        <input id="deadline" type="date" name="deadline" class="ts-input"
                               value="{{ old('deadline', $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('Y-m-d') : '') }}" required>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3" style="border-top:1px solid var(--border)">
                    <button type="button" class="ts-btn-danger"
                            onclick="if(confirm('Delete this task?')) document.getElementById('delete-form').submit();">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </button>
                    <div class="flex items-center gap-3">
                        <a href="{{ url()->previous() }}" class="ts-btn-ghost">Cancel</a>
                        <button type="submit" class="ts-btn-primary">Save Changes</button>
                    </div>
                </div>
            </form>

            <form id="delete-form" action="{{ route('tasks.destroy', $task) }}" method="POST" class="hidden">
                @csrf @method('DELETE')
            </form>
        </div>
    </div>
</x-app-layout>
