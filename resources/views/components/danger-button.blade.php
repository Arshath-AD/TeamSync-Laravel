<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-3 py-1.5 bg-workspace-danger border border-transparent rounded text-[11px] font-semibold text-white uppercase tracking-wide hover:bg-red-500 transition-colors']) }}>
    {{ $slot }}
</button>
