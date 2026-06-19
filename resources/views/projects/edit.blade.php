<x-app-layout>
    <x-slot name="header">Workspace Settings</x-slot>

    <x-workspace.form-panel :title="$project->project_name" description="Update workspace details and project lead.">
        <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="project_name" :value="__('Workspace Name')" />
                <x-text-input id="project_name" class="mt-0.5" type="text" name="project_name" :value="old('project_name', $project->project_name)" required autofocus />
                <x-input-error :messages="$errors->get('project_name')" />
            </div>

            <div>
                <x-input-label for="description" :value="__('Description')" />
                <textarea id="description" name="description" class="ws-textarea mt-0.5" rows="3">{{ old('description', $project->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" />
            </div>

            <div>
                <x-input-label for="project_lead_id" :value="__('Project Lead')" />
                <select id="project_lead_id" name="project_lead_id" class="ws-select mt-0.5" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('project_lead_id', $project->project_lead_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('project_lead_id')" />
            </div>

            <div class="flex items-center justify-between pt-4 mt-4 border-t border-workspace-border">
                <button type="button" onclick="if(confirm('Delete this workspace?')) document.getElementById('delete-form').submit();" class="ws-link-danger">Delete Workspace</button>
                <div class="flex items-center gap-3">
                    <a href="{{ route('projects.show', $project) }}" class="ws-link-muted">Cancel</a>
                    <x-primary-button>Save Changes</x-primary-button>
                </div>
            </div>
        </form>

        <form id="delete-form" action="{{ route('projects.destroy', $project) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </x-workspace.form-panel>
</x-app-layout>
