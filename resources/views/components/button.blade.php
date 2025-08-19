@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'outline' => false,
    'ghost' => false,
    'link' => false,
    'active' => false,
    'disabled' => false,
    'loading' => false,
    'wide' => false,
    'block' => false,
    'circle' => false,
    'square' => false,
    'class' => ''
])

@php
    $classes = ['btn'];
    
    // Variant classes
    if (!$ghost && !$link && !$outline) {
        $classes[] = 'btn-' . $variant;
    }
    
    // Style modifiers
    if ($outline) $classes[] = 'btn-outline';
    if ($ghost) $classes[] = 'btn-ghost';
    if ($link) $classes[] = 'btn-link';
    if ($active) $classes[] = 'btn-active';
    
    // Size classes
    if ($size !== 'md') {
        $classes[] = 'btn-' . $size;
    }
    
    // Layout modifiers
    if ($wide) $classes[] = 'btn-wide';
    if ($block) $classes[] = 'btn-block';
    if ($circle) $classes[] = 'btn-circle';
    if ($square) $classes[] = 'btn-square';
    
    // Loading state
    if ($loading) $classes[] = 'loading';
    
    // Custom classes
    if ($class) $classes[] = $class;
    
    $classString = implode(' ', $classes);
@endphp

<button 
    type="{{ $type }}"
    class="{{ $classString }}"
    @if($disabled) disabled @endif
    {{ $attributes }}
>
    @if($loading)
        <span class="loading loading-spinner loading-sm"></span>
    @endif
    {{ $slot }}
</button>