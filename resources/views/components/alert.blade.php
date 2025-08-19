@props([
    'type' => 'info',
    'dismissible' => false,
    'icon' => null,
    'title' => null,
    'class' => ''
])

@php
    $alertClasses = ['alert'];
    
    // Type classes
    if ($type !== 'default') {
        $alertClasses[] = 'alert-' . $type;
    }
    
    // Custom classes
    if ($class) $alertClasses[] = $class;
    
    // Default icons for each type
    $defaultIcons = [
        'info' => '<i data-lucide="info" class="stroke-current shrink-0 w-6 h-6"></i>',
        'success' => '<i data-lucide="check-circle" class="stroke-current shrink-0 h-6 w-6"></i>',
        'warning' => '<i data-lucide="alert-triangle" class="stroke-current shrink-0 h-6 w-6"></i>',
        'error' => '<i data-lucide="x-circle" class="stroke-current shrink-0 h-6 w-6"></i>'
    ];
    
    $displayIcon = $icon ?: ($defaultIcons[$type] ?? $defaultIcons['info']);
@endphp

<div class="{{ implode(' ', $alertClasses) }}" {{ $attributes }}>
    @if($displayIcon)
        {!! $displayIcon !!}
    @endif
    
    <div class="flex-1">
        @if($title)
            <div class="font-semibold">{{ $title }}</div>
        @endif
        
        <div>
            {{ $slot }}
        </div>
    </div>
    
    @if($dismissible)
        <button class="btn btn-sm btn-ghost" onclick="this.parentElement.style.display='none'">
            <i data-lucide="x" class="h-4 w-4"></i>
        </button>
    @endif
</div>