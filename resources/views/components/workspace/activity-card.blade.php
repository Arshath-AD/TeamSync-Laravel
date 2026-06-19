@props(['items' => []])

<div {{ $attributes->merge(['class' => 'ts-panel h-full flex flex-col min-h-[200px]']) }}>
    <div class="flex items-center justify-between px-3 py-2.5" style="border-bottom:1px solid var(--border)">
        <span class="ts-section-title">Recent Activity</span>
        <span class="text-[10px]" style="color:var(--secondary)">Live feed pending</span>
    </div>
    <div class="flex-1 p-3 space-y-2">
        @forelse($items as $item)
            <div class="flex gap-2 text-xs">
                <div class="h-1.5 w-1.5 rounded-full mt-1.5 flex-shrink-0" style="background:var(--accent)"></div>
                <div>
                    <p style="color:var(--text)">{{ $item['text'] ?? '' }}</p>
                    <p class="text-[10px]" style="color:var(--secondary)">{{ $item['time'] ?? '' }}</p>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center h-full py-8 text-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center mb-2"
                     style="background:var(--elevated);border:1px solid var(--border)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--secondary)">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <p class="text-xs" style="color:var(--secondary)">Activity stream will appear here</p>
                <p class="text-[10px] mt-1" style="color:var(--secondary);opacity:.5">Task updates, assignments, completions</p>
            </div>
        @endforelse
    </div>
</div>
