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
        'info' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
        'success' => '<svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        'warning' => '<svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" /></svg>',
        'error' => '<svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
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
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    @endif
</div>