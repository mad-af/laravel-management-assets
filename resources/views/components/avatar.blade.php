@props([
    'src' => null,
    'alt' => 'Avatar',
    'initials' => null,
    'size' => 'md', // xs, sm, md, lg, xl
    'shape' => 'circle', // circle, square, squircle
    'status' => null, // online, offline
    'placeholder' => false,
    'class' => ''
])
@php
    $sizeClasses = [
        'xs' => 'w-6 h-6',
        'sm' => 'w-8 h-8',
        'md' => 'w-10 h-10',
        'lg' => 'w-12 h-12',
        'xl' => 'w-16 h-16',
        '2xl' => 'w-20 h-20',
        '3xl' => 'w-24 h-24'
    ];

    $shapeClasses = [
        'circle' => 'rounded-full',
        'square' => 'rounded-lg',
        'squircle' => 'mask mask-squircle'
    ];

    $statusClasses = [
        'online' => 'online',
        'offline' => 'offline'
    ];

    $avatarClasses = 'avatar';
    if ($status) {
        $avatarClasses .= ' ' . $statusClasses[$status];
    }
    if ($placeholder) {
        $avatarClasses .= ' placeholder';
    }
    if ($class) {
        $avatarClasses .= ' ' . $class;
    }

    $innerClasses = $sizeClasses[$size] . ' ' . $shapeClasses[$shape] . ' flex items-center justify-center';
@endphp

           
<div class="{{ $avatarClasses }}">
    <div class="{{ $innerClasses }}{{ $placeholder ? ' bg-neutral text-neutral-content' : '' }}">
        @if($src)
            <img src="{{ $src }}" alt="{{ $alt }}" />
        @elseif($initials)
            <span class="{{ $size === 'xs' ? 'text-xs' : ($size === 'sm' ? 'text-xs' : ($size === 'md' ? 'text-sm' : 'text-base')) }}">{{ $initials }}</span>
        @else
            <span class="{{ $size === 'xs' ? 'text-xs' : ($size === 'sm' ? 'text-xs' : ($size === 'md' ? 'text-sm' : 'text-base')) }}">?</span>
        @endif
    </div>
</div>