@props(['title' => null, 'description' => null])

<div {{ $attributes->merge(['class' => 'ws-form-panel']) }}>
    @if($title)
        <div class="mb-4 pb-3 border-b border-workspace-border">
            <h3 class="text-sm font-semibold text-workspace-text">{{ $title }}</h3>
            @if($description)
                <p class="text-xs text-workspace-secondary mt-0.5">{{ $description }}</p>
            @endif
        </div>
    @endif
    {{ $slot }}
</div>
