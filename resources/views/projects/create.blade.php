<x-app-layout>
    <x-slot name="header">New Workspace</x-slot>

    <div class="max-w-2xl fade-in">
        <div class="ts-panel p-5">
            <div class="mb-4 pb-4" style="border-bottom:1px solid var(--border)">
                <h2 class="text-sm font-semibold" style="color:var(--text)">Create Workspace</h2>
                <p class="text-xs mt-0.5" style="color:var(--secondary)">Set up a new project workspace for your team.</p>
            </div>

            @php
                $userOptions = [];
                foreach($users as $u) {
                    $userOptions[] = ['value' => $u->id, 'label' => $u->name, 'avatarUser' => $u];
                }
            @endphp

            <form action="{{ route('projects.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="ts-label" for="project_name">Workspace Name</label>
                    <input id="project_name" type="text" name="project_name"
                           value="{{ old('project_name') }}"
                           class="ts-input" required autofocus
                           placeholder="e.g. Platform Redesign">
                    @error('project_name')
                        <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="ts-label" for="description">Description</label>
                    <textarea id="description" name="description" class="ts-input" rows="3"
                              placeholder="What is this workspace for?">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="ts-label" for="project_lead_id">Project Lead</label>
                    <x-workspace.dropdown 
                        name="project_lead_id" 
                        id="project_lead_id" 
                        :value="old('project_lead_id', Auth::id())" 
                        :options="$userOptions" 
                        placeholder="Select lead…" 
                        required="true" />
                    @error('project_lead_id')
                        <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-3" style="border-top:1px solid var(--border)">
                    <a href="{{ route('projects.index') }}" class="ts-btn-ghost">Cancel</a>
                    <button type="submit" class="ts-btn-primary">Create Workspace</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
