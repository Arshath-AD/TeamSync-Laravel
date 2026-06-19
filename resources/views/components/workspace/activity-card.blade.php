@props(['items' => []])

<div {{ $attributes->merge(['class' => 'ws-panel h-full flex flex-col min-h-[180px]']) }}>
    <div class="ws-panel-header">
        <span class="ws-section-title">Recent Activity</span>
        <span class="text-[10px] text-workspace-secondary">Live feed pending</span>
    </div>
    <div class="flex-1 p-3 space-y-2">
        @forelse($items as $item)
            <div class="flex gap-2 text-xs">
                <div class="h-1.5 w-1.5 rounded-full bg-workspace-accent mt-1.5 flex-shrink-0"></div>
                <div>
                    <p class="text-workspace-text">{{ $item['text'] ?? '' }}</p>
                    <p class="text-[10px] text-workspace-secondary">{{ $item['time'] ?? '' }}</p>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center h-full py-6 text-center">
                <div class="h-8 w-8 rounded-full bg-workspace-elevated border border-workspace-border flex items-center justify-center mb-2">
                    <svg class="w-4 h-4 text-workspace-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <p class="text-xs text-workspace-secondary">Activity stream will appear here</p>
                <p class="text-[10px] text-workspace-secondary/60 mt-1">Task updates, assignments, completions</p>
            </div>
        @endforelse
    </div>
</div>
