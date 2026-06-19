<x-app-layout>
    <x-slot name="header">New Workspace</x-slot>

    <x-workspace.form-panel title="Create Workspace" description="Set up a new project workspace for your team.">
        <form action="{{ route('projects.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="project_name" :value="__('Workspace Name')" />
                <x-text-input id="project_name" class="mt-0.5" type="text" name="project_name" :value="old('project_name')" required autofocus />
                <x-input-error :messages="$errors->get('project_name')" />
            </div>

            <div>
                <x-input-label for="description" :value="__('Description')" />
                <textarea id="description" name="description" class="ws-textarea mt-0.5" rows="3" placeholder="What is this workspace for?">{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" />
            </div>

            <div>
                <x-input-label for="project_lead_id" :value="__('Project Lead')" />
                <select id="project_lead_id" name="project_lead_id" class="ws-select mt-0.5" required>
                    <option value="" disabled>Select lead</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('project_lead_id', Auth::id()) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('project_lead_id')" />
            </div>

            <div class="ws-form-actions">
                <a href="{{ route('projects.index') }}" class="ws-link-muted">Cancel</a>
                <x-primary-button>Create Workspace</x-primary-button>
            </div>
        </form>
    </x-workspace.form-panel>
</x-app-layout>
