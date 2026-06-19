<div class="space-y-4 fade-in">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h3 class="ts-section-title">Project Resources</h3>
            <p class="text-[10px] mt-0.5" style="color:var(--secondary)">{{ count($workload ?? []) }} members · workload & capacity</p>
        </div>

        {{-- Add member --}}
        <form action="{{ route('projects.members.store', $project) }}" method="POST" class="flex gap-2 w-full sm:w-auto">
            @csrf
            <select name="user_id" required class="ts-input w-full sm:w-52 text-xs">
                <option value="" disabled selected>Add resource…</option>
                @foreach($allUsers as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="ts-btn-primary flex-shrink-0">Assign</button>
        </form>
    </div>

    @if(count($workload ?? []) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
            @foreach($workload as $userId => $data)
                <div class="relative group">
                    <x-workspace.member-card
                        :member="$data['user']"
                        :metrics="array_merge($data, ['assigned_projects' => 1])"
                    />
                    @if($project->project_lead_id !== $userId)
                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <form action="{{ route('projects.members.destroy', [$project, $userId]) }}" method="POST"
                                  onsubmit="return confirm('Remove from workspace?');">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="flex items-center justify-center w-6 h-6 rounded-full"
                                        style="background:var(--surface);border:1px solid var(--border);color:var(--danger)"
                                        title="Remove member">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="ts-empty py-10">
            <p class="font-medium mb-1" style="color:var(--text)">No resources assigned</p>
            <p>Only the project lead is mapped to this workspace.</p>
        </div>
    @endif
</div>
