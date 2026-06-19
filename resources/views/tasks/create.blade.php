<x-app-layout>
    <x-slot name="header">Create Task</x-slot>

    <div class="max-w-2xl fade-in">
        <div class="ts-panel p-5">
            <div class="mb-4 pb-4" style="border-bottom:1px solid var(--border)">
                <h2 class="text-sm font-semibold" style="color:var(--text)">New Task</h2>
                <p class="text-xs mt-0.5" style="color:var(--secondary)">Add a task to a workspace and assign it to a team member.</p>
            </div>

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
                        <select id="project_id" name="project_id" class="ts-input">
                            <option value="">No Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}"
                                    {{ old('project_id', request('project_id')) == $project->id ? 'selected' : '' }}>
                                    {{ $project->project_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="ts-label" for="assigned_to">Assign To</label>
                        <select id="assigned_to" name="assigned_to" class="ts-input" required>
                            <option value="" disabled selected>Select member…</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                            <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Status + Priority + Deadline --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="ts-label" for="status">Status</label>
                        <select id="status" name="status" class="ts-input" required>
                            @foreach(['Pending', 'In Progress', 'On Hold', 'Completed'] as $s)
                                <option value="{{ $s }}" {{ old('status', 'Pending') === $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="ts-label" for="priority">Priority</label>
                        <select id="priority" name="priority" class="ts-input" required>
                            @foreach(['Low', 'Medium', 'High', 'Critical'] as $p)
                                <option value="{{ $p }}" {{ old('priority', 'Medium') === $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
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
