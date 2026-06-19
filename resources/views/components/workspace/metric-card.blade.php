@props(['value', 'label', 'trend' => null, 'statusColor' => 'accent', 'icon' => null])

@php
    $accentStyles = match($statusColor) {
        'success' => ['border' => 'rgba(34,197,94,0.4)',  'text' => 'var(--success)', 'bg' => 'rgba(34,197,94,0.08)'],
        'warning' => ['border' => 'rgba(245,158,11,0.4)', 'text' => 'var(--warning)', 'bg' => 'rgba(245,158,11,0.08)'],
        'danger'  => ['border' => 'rgba(239,68,68,0.4)',  'text' => 'var(--danger)',  'bg' => 'rgba(239,68,68,0.08)'],
        default   => ['border' => 'rgba(26,107,138,0.4)', 'text' => 'var(--accent)',  'bg' => 'rgba(26,107,138,0.08)'],
    };
    $trendClass = $trend && str_starts_with($trend, '+') ? 'var(--danger)' : (
                  $trend && str_starts_with($trend, '-') ? 'var(--success)' : 'var(--secondary)');
@endphp

<div {{ $attributes->merge(['class' => 'ts-metric']) }}
     style="border-top: 2px solid {{ $accentStyles['border'] }}; position:relative; overflow:hidden;">
    {{-- Subtle bg glow --}}
    <div style="position:absolute;top:0;right:0;width:48px;height:48px;border-radius:50%;background:{{ $accentStyles['bg'] }};filter:blur(16px);pointer-events:none;"></div>

    <div class="flex items-center justify-between gap-2 mb-1.5 relative">
        <span class="text-[10px] font-medium uppercase tracking-wider truncate" style="color:var(--secondary)">{{ $label }}</span>
        @if($trend)
            <span class="text-[10px] font-medium flex-shrink-0 ts-badge ts-badge-danger" style="color:{{ $trendClass }}">{{ $trend }}</span>
        @endif
    </div>
    <div class="text-2xl font-bold tabular leading-none" style="color:var(--text)">{{ $value }}</div>
</div>
