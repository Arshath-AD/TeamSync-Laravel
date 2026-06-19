<x-app-layout>
    <x-slot name="header">Workspace Settings</x-slot>

    <div class="max-w-2xl fade-in">
        <div class="ts-panel p-5">
            <div class="mb-4 pb-4" style="border-bottom:1px solid var(--border)">
                <h2 class="text-sm font-semibold" style="color:var(--text)">{{ $project->project_name }}</h2>
                <p class="text-xs mt-0.5" style="color:var(--secondary)">Update workspace details and project lead.</p>
            </div>

            <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-4">
                @csrf @method('PUT')

                <div>
                    <label class="ts-label" for="project_name">Workspace Name</label>
                    <input id="project_name" type="text" name="project_name"
                           value="{{ old('project_name', $project->project_name) }}"
                           class="ts-input" required autofocus>
                    @error('project_name')
                        <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="ts-label" for="description">Description</label>
                    <textarea id="description" name="description" class="ts-input" rows="3"
                              placeholder="What is this workspace for?">{{ old('description', $project->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="ts-label" for="project_lead_id">Project Lead</label>
                        <select id="project_lead_id" name="project_lead_id" class="ts-input" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ old('project_lead_id', $project->project_lead_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_lead_id')
                            <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p>
                        @enderror
                    </div>

                    @if(Auth::user()->role === 'admin')
                        <div>
                            <label class="ts-label" for="priority">Priority</label>
                            <select id="priority" name="priority" class="ts-input" required>
                                @foreach(['Low', 'Medium', 'High', 'Critical'] as $p)
                                    <option value="{{ $p }}" {{ old('priority', $project->priority ?? 'Medium') === $p ? 'selected' : '' }}>{{ $p }}</option>
                                @endforeach
                            </select>
                            @error('priority')
                                <p class="mt-1 text-[11px]" style="color:var(--danger)">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-between pt-3" style="border-top:1px solid var(--border)">
                    <button type="button" class="ts-btn-danger"
                            onclick="if(confirm('Delete this workspace and all its tasks?')) document.getElementById('delete-form').submit();">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Workspace
                    </button>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('projects.show', $project) }}" class="ts-btn-ghost">Cancel</a>
                        <button type="submit" class="ts-btn-primary">Save Changes</button>
                    </div>
                </div>
            </form>

            <form id="delete-form" action="{{ route('projects.destroy', $project) }}" method="POST" class="hidden">
                @csrf @method('DELETE')
            </form>
        </div>
    </div>
</x-app-layout>
