<button {{ $attributes->merge(['type' => 'submit', 'class' => 'ws-btn-primary']) }}>
    {{ $slot }}
</button>
