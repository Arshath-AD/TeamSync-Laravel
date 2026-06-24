<x-app-layout>
    <x-slot name="header">Create Task</x-slot>

    <div class="max-w-2xl mx-auto fade-in">
        <div class="ts-panel p-5">
            <div class="mb-4 pb-4" style="border-bottom:1px solid var(--border)">
                <h2 class="text-sm font-semibold" style="color:var(--text)">New Task</h2>
                <p class="text-xs mt-0.5" style="color:var(--secondary)">Add a task to a workspace and assign it to a team member.</p>
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

            <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Task name --}}
                <div>
                    <label class="ts-label" for="task_name">Task Name</label>
                    <input id="task_name" type="text" name="task_name" value="{{ old('task_name') }}"
                           class="ts-input" required autofocus placeholder="e.g. Implement auth flow">
                    @error('task_name')
                        <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="ts-label" for="description">Description</label>
                    <textarea id="description" name="description" class="ts-input" rows="3"
                              placeholder="What needs to be done?">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Workspace + Assignee --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="ts-label" for="project_id">Workspace</label>
                        <x-workspace.dropdown 
                            name="project_id" 
                            id="project_id" 
                            :value="old('project_id', request('project_id'))" 
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
                            :value="old('assigned_to')" 
                            :options="$userOptions" 
                            placeholder="Select member…" 
                            required="true" />
                        @error('assigned_to')
                            <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Status + Priority + Deadline --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="ts-label" for="status">Status</label>
                        <x-workspace.dropdown 
                            name="status" 
                            id="status" 
                            :value="old('status', 'Pending')" 
                            :options="$statusOptions" 
                            required="true" />
                        @error('status')
                            <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="ts-label" for="priority">Priority</label>
                        <x-workspace.dropdown 
                            name="priority" 
                            id="priority" 
                            :value="old('priority', 'Medium')" 
                            :options="$priorityOptions" 
                            required="true" />
                        @error('priority')
                            <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="ts-label" for="deadline">Deadline</label>
                        <input id="deadline" type="date" name="deadline" class="ts-input"
                               value="{{ old('deadline') }}" required>
                        @error('deadline')
                            <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-3" style="border-top:1px solid var(--border)">
                    <a href="{{ url()->previous() }}" class="ts-btn-ghost">Cancel</a>
                    <button type="submit" class="ts-btn-primary">Create Task</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
