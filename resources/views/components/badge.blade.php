@props([
    'variant' => 'default',
    'size' => 'md',
    'outline' => false,
    'class' => ''
])

@php
    $badgeClasses = ['badge'];
    
    // Variant classes
    if ($variant !== 'default') {
        $badgeClasses[] = 'badge-' . $variant;
    }
    
    // Size classes
    if ($size !== 'md') {
        $badgeClasses[] = 'badge-' . $size;
    }
    
    // Outline modifier
    if ($outline) {
        $badgeClasses[] = 'badge-outline';
    }
    
    // Custom classes
    if ($class) $badgeClasses[] = $class;
@endphp

<span class="{{ implode(' ', $badgeClasses) }}" {{ $attributes }}>
    {{ $slot }}
</span>