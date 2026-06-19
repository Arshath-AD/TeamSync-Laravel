@props(['value', 'label', 'trend' => null, 'statusColor' => 'accent'])

@php
    $borderClass = match($statusColor) {
        'success' => 'border-t-workspace-success',
        'warning' => 'border-t-workspace-warning',
        'danger' => 'border-t-workspace-danger',
        default => 'border-t-workspace-accent',
    };
    $trendClass = $trend && str_starts_with($trend, '+')
        ? 'text-workspace-danger'
        : ($trend && str_starts_with($trend, '-') ? 'text-workspace-success' : 'text-workspace-secondary');
@endphp

<div {{ $attributes->merge(['class' => "bg-workspace-surface border-t-2 {$borderClass} border-x border-b border-workspace-border rounded-md px-3 py-2.5"]) }}>
    <div class="flex items-center justify-between gap-2 mb-0.5">
        <span class="text-[10px] text-workspace-secondary font-medium uppercase tracking-wider truncate">{{ $label }}</span>
        @if($trend)
            <span class="text-[10px] {{ $trendClass }} font-medium flex-shrink-0">{{ $trend }}</span>
        @endif
    </div>
    <div class="text-xl font-bold text-workspace-text tabular-nums leading-none">{{ $value }}</div>
</div>
