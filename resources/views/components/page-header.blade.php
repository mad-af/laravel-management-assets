@props([
    'title',
    'description' => null,
    'buttonText' => null,
    'buttonIcon' => 'o-plus',
    'buttonClass' => 'btn-primary btn-sm',
    'buttonAction' => null
])

<!-- Page Header Component -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-base-content">{{ $title }}</h1>
        @if($description)
            <p class="text-base-content/70">{{ $description }}</p>
        @endif
    </div>
    
    @if($buttonText)
        <div>
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
        </div>
    @endif
</div>