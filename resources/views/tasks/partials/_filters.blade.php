<form method="GET" action="{{ url()->current() }}" id="task-filters-form" class="ts-panel p-3 mb-4 flex flex-col xl:flex-row gap-4 items-start xl:items-center justify-between fade-in">
    @php
        $selectedPriorities = request('priority', []);
    @endphp
    
    {{-- Priority Filters (Checkbox group) --}}
    <div class="flex flex-wrap gap-2">
        @foreach(['High', 'Medium', 'Low', 'Overdue'] as $p)
            @php
                $isActive = in_array($p, $selectedPriorities);
                $activeColor = match($p) {
                    'High', 'Overdue' => 'var(--danger)',
                    'Medium'          => 'var(--warning)',
                    'Low'             => 'var(--success)',
                    default           => 'var(--accent)',
                };
            @endphp
            <label class="cursor-pointer">
                <input type="checkbox" name="priority[]" value="{{ $p }}" class="hidden" onchange="document.getElementById('task-filters-form').submit()" {{ $isActive ? 'checked' : '' }}>
                <span class="px-3 py-1.5 rounded-md text-[11px] font-medium transition-all border block" 
                      style="{{ $isActive ? 'background:color-mix(in srgb, '.$activeColor.' 10%, transparent); color:'.$activeColor.'; border-color:color-mix(in srgb, '.$activeColor.' 30%, transparent);' : 'background:transparent; color:var(--secondary); border-color:var(--border);' }}"
                      onmouseover="if(!{{ $isActive ? 'true' : 'false' }}) this.style.background='var(--elevated)'"
                      onmouseout="if(!{{ $isActive ? 'true' : 'false' }}) this.style.background='transparent'">
                    {{ $p }}
                </span>
            </label>
        @endforeach
    </div>

    {{-- Dropdowns & Controls --}}
    <div class="flex flex-wrap gap-3 items-center w-full xl:w-auto">
        
        {{-- Assignee --}}
        @if(isset($users))
        @php
            $filterAssigneeOpts = [['value' => '', 'label' => 'All Users']];
            foreach($users as $u) {
                $filterAssigneeOpts[] = ['value' => $u->id, 'label' => $u->name, 'avatarUser' => $u];
            }
        @endphp
        <div class="relative flex-1 sm:flex-none sm:w-40">
            <x-workspace.dropdown 
                name="assignee" 
                id="filter_assignee" 
                :value="request('assignee')" 
                :options="$filterAssigneeOpts" 
                placeholder="All Users" 
                onChange="this.form.submit()" />
        </div>
        @endif

        {{-- Status --}}
        @php
            $filterStatusOpts = [
                ['value' => '', 'label' => 'All Statuses'],
                ['value' => 'Todo', 'label' => 'Todo', 'color' => '#6b7280'],
                ['value' => 'In Progress', 'label' => 'In Progress', 'color' => '#3b82f6'],
                ['value' => 'On Hold', 'label' => 'On Hold', 'color' => '#f59e0b'],
                ['value' => 'Completed', 'label' => 'Completed', 'color' => '#22c55e'],
            ];
        @endphp
        <div class="relative flex-1 sm:flex-none sm:w-36">
            <x-workspace.dropdown 
                name="status" 
                id="filter_status" 
                :value="request('status')" 
                :options="$filterStatusOpts" 
                placeholder="All Statuses" 
                onChange="this.form.submit()" />
        </div>

        {{-- Due Date --}}
        <div class="relative flex-1 sm:flex-none">
            <input type="date" name="due_date" value="{{ request('due_date') }}" class="ts-input text-xs py-1.5 px-3 w-full sm:w-36 bg-[var(--surface)]" onchange="this.form.submit()" title="Filter by Due Date">
        </div>

        {{-- Clear Filters --}}
        @if(request()->anyFilled(['priority', 'assignee', 'status', 'due_date']))
            <a href="{{ url()->current() }}" class="ts-btn-secondary text-[11px] py-1.5 px-3 whitespace-nowrap flex-shrink-0" title="Clear all filters">
                <svg class="w-3 h-3 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Clear
            </a>
        @endif
    </div>
</form>
