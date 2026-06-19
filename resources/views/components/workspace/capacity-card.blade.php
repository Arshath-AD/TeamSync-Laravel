@props(['userStat'])

@php
    $capacityPercentage = $userStat->total_tasks > 0 ? min(100, (int) round(($userStat->active_tasks / 10) * 100)) : 0;
    $capacityBarClass = match(true) {
        $capacityPercentage > 80 => 'bg-workspace-danger',
        $capacityPercentage > 50 => 'bg-workspace-warning',
        default => 'bg-workspace-success',
    };
    $loadLabel = match(true) {
        $capacityPercentage > 80 => 'Overloaded',
        $capacityPercentage > 50 => 'High',
        $capacityPercentage > 0 => 'Normal',
        default => 'Idle',
    };
    $loadClass = match(true) {
        $capacityPercentage > 80 => 'text-workspace-danger',
        $capacityPercentage > 50 => 'text-workspace-warning',
        default => 'text-workspace-success',
    };
@endphp

<div {{ $attributes->merge(['class' => 'ws-panel px-3 py-2 flex items-center gap-3']) }}>
    <div class="h-7 w-7 rounded bg-workspace-elevated flex items-center justify-center text-workspace-text text-[10px] font-bold flex-shrink-0 border border-workspace-border">
        {{ strtoupper(substr($userStat->name, 0, 1)) }}
    </div>

    <div class="flex-1 min-w-0">
        <div class="flex items-center justify-between gap-2">
            <span class="text-xs font-medium text-workspace-text truncate">{{ $userStat->name }}</span>
            <span class="text-[10px] font-medium {{ $loadClass }} flex-shrink-0">{{ $loadLabel }}</span>
        </div>
        <div class="text-[10px] text-workspace-secondary tabular-nums">
            {{ $userStat->active_tasks }} active · {{ $userStat->completed_tasks }} done
        </div>
        <div class="mt-1.5 flex items-center gap-2">
            <div class="flex-1 bg-workspace-elevated rounded-full h-1">
                <div class="{{ $capacityBarClass }} h-1 rounded-full transition-all" style="width: {{ $capacityPercentage }}%"></div>
            </div>
            <span class="text-[9px] text-workspace-secondary tabular-nums w-7 text-right">{{ $capacityPercentage }}%</span>
        </div>
    </div>
</div>
