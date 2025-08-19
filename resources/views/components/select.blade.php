@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'size' => 'md',
    'variant' => 'bordered',
    'state' => null, // success, warning, error, info
    'disabled' => false,
    'required' => false,
    'multiple' => false,
    'helper' => null,
    'error' => null,
    'class' => ''
])

@php
    $selectId = $id ?: ($name ? $name . '_' . uniqid() : 'select_' . uniqid());
    
    $selectClasses = ['select'];
    
    // Variant classes
    if ($variant) {
        $selectClasses[] = 'select-' . $variant;
    }
    
    // Size classes
    if ($size !== 'md') {
        $selectClasses[] = 'select-' . $size;
    }
    
    // State classes
    if ($state) {
        $selectClasses[] = 'select-' . $state;
    }
    
    // Error state override
    if ($error) {
        $selectClasses[] = 'select-error';
    }
    
    // Custom classes
    if ($class) $selectClasses[] = $class;
    
    $wrapperClasses = ['form-control', 'w-full'];
@endphp

<div class="{{ implode(' ', $wrapperClasses) }}">
    @if($label)
        <label class="label" for="{{ $selectId }}">
            <span class="label-text">
                {{ $label }}
                @if($required)
                    <span class="text-error ml-1">*</span>
                @endif
            </span>
        </label>
    @endif
    
    <select 
        id="{{ $selectId }}"
        @if($name) name="{{ $name }}{{ $multiple ? '[]' : '' }}" @endif
        class="{{ implode(' ', $selectClasses) }}"
        @if($disabled) disabled @endif
        @if($required) required @endif
        @if($multiple) multiple @endif
        {{ $attributes }}
    >
        @if($placeholder && !$multiple)
            <option value="" disabled @if(!$selected) selected @endif>
                {{ $placeholder }}
            </option>
        @endif
        
        @if($slot->isNotEmpty())
            {{ $slot }}
        @else
            @foreach($options as $value => $text)
                @if(is_array($text))
                    {{-- Optgroup --}}
                    <optgroup label="{{ $value }}">
                        @foreach($text as $optValue => $optText)
                            <option value="{{ $optValue }}" 
                                @if(
                                    ($multiple && is_array($selected) && in_array($optValue, $selected)) ||
                                    (!$multiple && $selected == $optValue)
                                ) selected @endif
                            >
                                {{ $optText }}
                            </option>
                        @endforeach
                    </optgroup>
                @else
                    <option value="{{ $value }}" 
                        @if(
                            ($multiple && is_array($selected) && in_array($value, $selected)) ||
                            (!$multiple && $selected == $value)
                        ) selected @endif
                    >
                        {{ $text }}
                    </option>
                @endif
            @endforeach
        @endif
    </select>
    
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