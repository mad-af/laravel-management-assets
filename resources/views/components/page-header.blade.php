@props([
    'title',
    'description' => null,
    'buttonText' => null,
    'buttonIcon' => 'o-plus',
    'buttonClass' => 'btn-primary btn-sm',
    'buttonAction' => null
])

<!-- Page Header Component -->
<div class="flex justify-between items-center mb-3">
    <div>
        <h1 class="text-2xl font-bold text-base-content">{{ $title }}</h1>
        @if($description)
            <p class="text-base-content/70">{{ $description }}</p>
        @endif
    </div>
    
    <div class="flex gap-2 items-center">
        <!-- Custom Actions Slot -->
        @if(isset($additionalButton))
            {{ $additionalButton }}
        @endif
        
        <!-- Default Button -->
        @if($buttonText)
            @if($buttonAction)
                <x-button 
                    icon="{{ $buttonIcon }}" 
                    class="{{ $buttonClass }}" 
                    wire:click="{{ $buttonAction }}"
                >
                    {{ $buttonText }}
                </x-button>
            @else
                <x-button 
                    icon="{{ $buttonIcon }}" 
                    class="{{ $buttonClass }}"
                >
                    {{ $buttonText }}
                </x-button>
            @endif
        @endif
    </div>
</div>