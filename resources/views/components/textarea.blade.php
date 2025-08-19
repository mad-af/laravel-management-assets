@props([
    'label' => null,
    'placeholder' => null,
    'value' => null,
    'name' => null,
    'id' => null,
    'rows' => 3,
    'size' => 'md',
    'variant' => 'bordered',
    'state' => null, // success, warning, error, info
    'disabled' => false,
    'required' => false,
    'readonly' => false,
    'helper' => null,
    'error' => null,
    'resize' => true,
    'class' => ''
])

@php
    $textareaId = $id ?: ($name ? $name . '_' . uniqid() : 'textarea_' . uniqid());
    
    $textareaClasses = ['textarea'];
    
    // Variant classes
    if ($variant) {
        $textareaClasses[] = 'textarea-' . $variant;
    }
    
    // Size classes
    if ($size !== 'md') {
        $textareaClasses[] = 'textarea-' . $size;
    }
    
    // State classes
    if ($state) {
        $textareaClasses[] = 'textarea-' . $state;
    }
    
    // Error state override
    if ($error) {
        $textareaClasses[] = 'textarea-error';
    }
    
    // Resize control
    if (!$resize) {
        $textareaClasses[] = 'resize-none';
    }
    
    // Custom classes
    if ($class) $textareaClasses[] = $class;
    
    $wrapperClasses = ['form-control', 'w-full'];
@endphp

<div class="{{ implode(' ', $wrapperClasses) }}">
    @if($label)
        <label class="label" for="{{ $textareaId }}">
            <span class="label-text">
                {{ $label }}
                @if($required)
                    <span class="text-error ml-1">*</span>
                @endif
            </span>
        </label>
    @endif
    
    <textarea 
        id="{{ $textareaId }}"
        @if($name) name="{{ $name }}" @endif
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        rows="{{ $rows }}"
        class="{{ implode(' ', $textareaClasses) }}"
        @if($disabled) disabled @endif
        @if($required) required @endif
        @if($readonly) readonly @endif
        {{ $attributes }}
    >{{ $value ?? $slot }}</textarea>
    
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