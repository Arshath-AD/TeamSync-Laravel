<label {{ $attributes->merge(['class' => 'ws-label']) }}>
    {{ $value ?? $slot }}
</label>
