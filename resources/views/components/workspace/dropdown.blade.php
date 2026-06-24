@props([
    'name' => '',
    'id' => '',
    'value' => '',
    'options' => [], // [['value' => 1, 'label' => 'Admin', 'avatarUser' => $user, 'color' => '#f00']]
    'placeholder' => 'Select...',
    'onChange' => null,
    'required' => false,
    'class' => '',
])

@php
    $id = $id ?: $name ?: uniqid('dropdown_');
    $validValues = array_map(function($opt) { return (string)($opt['value'] ?? ''); }, $options);
@endphp

<div x-data="{
        open: false,
        value: '{{ addslashes((string)$value) }}',
        validValues: {{ json_encode($validValues) }},
        selectOption(val) {
            this.value = val;
            this.open = false;
            
            this.$nextTick(() => {
                const input = this.$refs.hiddenInput;
                if(input) {
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    @if($onChange)
                        let onchangeStr = `{!! addslashes($onChange) !!}`;
                        if(onchangeStr.includes('updateTaskField')) {
                            onchangeStr = onchangeStr.replace('this.value', `'${val}'`);
                            eval(onchangeStr);
                        } else if(onchangeStr.includes('this.form.submit()')) {
                            input.form.submit();
                        } else {
                            eval(onchangeStr.replace('this.value', `'${val}'`));
                        }
                    @endif
                }
            });
        }
    }"
    @click.away="open = false"
    class="relative {{ $class }}"
    :class="{'z-50': open}">

    @if($name)
        <input type="hidden" name="{{ $name }}" id="{{ $id }}" x-ref="hiddenInput" x-model="value" {{ $required ? 'required' : '' }}>
    @endif

    <button type="button" 
            @click="open = !open"
            class="flex items-center justify-between w-full px-2.5 py-1.5 rounded border transition-colors focus:outline-none focus:ring-1 focus:ring-[var(--accent)] bg-[var(--elevated)]"
            style="border-color:var(--border);color:var(--text);min-height:30px;">
        
        <div class="flex items-center gap-2 min-w-0 w-full">
            <!-- Show placeholder if no value matches -->
            <span x-show="!validValues.includes(value)" class="text-[10px] truncate opacity-50">{{ $placeholder }}</span>
            
            @foreach($options as $opt)
                <div x-show="value == '{{ addslashes((string)$opt['value']) }}'" style="display:none;" class="flex items-center gap-1.5 min-w-0 w-full">
                    @if(!empty($opt['avatarUser']))
                        <x-workspace.avatar :user="$opt['avatarUser']" sizeClass="w-4 h-4" />
                    @endif
                    @if(!empty($opt['color']))
                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background-color: {{ $opt['color'] }}"></span>
                    @endif
                    <span class="text-[10px] truncate leading-tight">{{ $opt['label'] }}</span>
                </div>
            @endforeach
        </div>

        <svg class="w-3 h-3 opacity-50 flex-shrink-0 ml-1.5 transition-transform duration-200" 
             :class="{'rotate-180': open}"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-cloak
         x-show="open" 
         x-transition.opacity.duration.150ms
         class="absolute z-50 w-full mt-1 rounded shadow-lg py-1 max-h-60 overflow-auto ts-scroll"
         style="background:var(--elevated);border:1px solid var(--border); min-width: 140px;">
        
        @foreach($options as $opt)
            <button type="button" 
                    @click="selectOption('{{ addslashes((string)$opt['value']) }}')"
                    class="w-full text-left flex items-center gap-2 px-2.5 py-1.5 hover:bg-[var(--surface)] transition-colors text-[11px]"
                    :class="value == '{{ addslashes((string)$opt['value']) }}' ? 'bg-[var(--surface)]' : ''">
                
                @if(!empty($opt['avatarUser']))
                    <x-workspace.avatar :user="$opt['avatarUser']" sizeClass="w-5 h-5" />
                @endif
                
                @if(!empty($opt['color']))
                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background-color: {{ $opt['color'] }}"></span>
                @endif

                <span style="color:var(--text)" class="truncate">{{ $opt['label'] }}</span>

                <svg x-show="value == '{{ addslashes((string)$opt['value']) }}'" class="w-3 h-3 ml-auto flex-shrink-0 text-[var(--success)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </button>
        @endforeach
    </div>
</div>
