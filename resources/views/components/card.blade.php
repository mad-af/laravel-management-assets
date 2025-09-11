@props([
    'title' => null,
    'subtitle' => null,
    'actions' => null,
    'compact' => false,
    'shadow' => true,
    'class' => ''
])

<x-card 
    :title="$title" 
    :subtitle="$subtitle" 
    :shadow="$shadow" 
    :class="$class"
    body-class="{{ $compact ? 'p-4' : '' }}"
>
    @if($actions)
        <x-slot:actions>
            {{ $actions }}
        </x-slot:actions>
    @endif
    
    {{ $slot }}
</x-card>