@props([
    'type' => 'text',
    'label' => null,
    'placeholder' => null,
    'value' => null,
    'name' => null,
    'id' => null,
    'size' => 'md',
    'variant' => 'bordered',
    'state' => null, // success, warning, error, info
    'disabled' => false,
    'required' => false,
    'readonly' => false,
    'helper' => null,
    'error' => null,
    'icon' => null,
    'iconPosition' => 'left',
    'class' => ''
])

@php
    $inputId = $id ?: ($name ? $name . '_' . uniqid() : 'input_' . uniqid());
    
    $inputClasses = ['input'];
    
    // Variant classes
    if ($variant) {
        $inputClasses[] = 'input-' . $variant;
    }
    
    // Size classes
    if ($size !== 'md') {
        $inputClasses[] = 'input-' . $size;
    }
    
    // State classes
    if ($state) {
        $inputClasses[] = 'input-' . $state;
    }
    
    // Error state override
    if ($error) {
        $inputClasses[] = 'input-error';
    }
    
    // Custom classes
    if ($class) $inputClasses[] = $class;
    
    $wrapperClasses = ['form-control', 'w-full'];
@endphp

<div class="{{ implode(' ', $wrapperClasses) }}">
    @if($label)
        <label class="label" for="{{ $inputId }}">
            <span class="label-text">
                {{ $label }}
                @if($required)
                    <span class="text-error ml-1">*</span>
                @endif
            </span>
        </label>
    @endif
    
    <div class="relative">
        @if($icon && $iconPosition === 'left')
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                {!! $icon !!}
            </div>
        @endif
        
        <input 
            type="{{ $type }}"
            id="{{ $inputId }}"
            @if($name) name="{{ $name }}" @endif
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($value !== null) value="{{ $value }}" @endif
            class="{{ implode(' ', $inputClasses) }} @if($icon && $iconPosition === 'left') pl-10 @endif @if($icon && $iconPosition === 'right') pr-10 @endif"
            @if($disabled) disabled @endif
            @if($required) required @endif
            @if($readonly) readonly @endif
            {{ $attributes }}
        />
        
        @if($icon && $iconPosition === 'right')
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                {!! $icon !!}
            </div>
        @endif
    </div>
    
    @if($helper || $error)
        <label class="label">
            @if($error)
                <span class="label-text-alt text-error">
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $error }}
                </span>
            @elseif($helper)
                <span class="label-text-alt">{{ $helper }}</span>
            @endif
        </label>
    @endif
</div>