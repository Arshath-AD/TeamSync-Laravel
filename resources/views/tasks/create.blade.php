<x-app-layout>
    <x-slot name="header">Create Task</x-slot>

    <x-workspace.form-panel title="New Task" description="Add a task to a workspace and assign it to a team member.">
        <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="task_name" :value="__('Task Name')" />
                <x-text-input id="task_name" class="mt-0.5" type="text" name="task_name" :value="old('task_name')" required autofocus />
                <x-input-error :messages="$errors->get('task_name')" />
            </div>

            <div>
                <x-input-label for="description" :value="__('Description')" />
                <textarea id="description" name="description" class="ws-textarea mt-0.5" rows="3">{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <x-input-label for="project_id" :value="__('Workspace')" />
                    <select id="project_id" name="project_id" class="ws-select mt-0.5" required>
                        <option value="" disabled selected>Select workspace</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', request('project_id')) == $project->id ? 'selected' : '' }}>{{ $project->project_name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('project_id')" />
                </div>
                <div>
                    <x-input-label for="assigned_to" :value="__('Assign To')" />
                    <select id="assigned_to" name="assigned_to" class="ws-select mt-0.5" required>
                        <option value="" disabled selected>Select member</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('assigned_to')" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" class="ws-select mt-0.5" required>
                        @foreach(['Pending', 'In Progress', 'Completed'] as $status)
                            <option value="{{ $status }}" {{ old('status', 'Pending') == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('status')" />
                </div>
                <div>
                    <x-input-label for="priority" :value="__('Priority')" />
                    <select id="priority" name="priority" class="ws-select mt-0.5" required>
                        @foreach(['Low', 'Medium', 'High', 'Critical'] as $priority)
                            <option value="{{ $priority }}" {{ old('priority', 'Medium') == $priority ? 'selected' : '' }}>{{ $priority }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('priority')" />
                </div>
                <div>
                    <x-input-label for="deadline" :value="__('Deadline')" />
                    <x-text-input id="deadline" class="mt-0.5" type="date" name="deadline" :value="old('deadline')" required />
                    <x-input-error :messages="$errors->get('deadline')" />
                </div>
            </div>

            <div class="ws-form-actions">
                <a href="{{ url()->previous() }}" class="ws-link-muted">Cancel</a>
                <x-primary-button>Create Task</x-primary-button>
            </div>
        </form>
    </x-workspace.form-panel>
</x-app-layout>
