<!-- Dashboard Content Header Livewire Component -->
<div class="flex justify-between items-center mb-3">
    <div>
        <h1 class="text-2xl font-bold text-base-content">{{ $title }}</h1>
        @if($description)
            <p class="text-base-content/70">{{ $description }}</p>
        @endif
    </div>
    
    <div class="flex gap-2 items-center">
        <!-- Additional Buttons (Livewire v3 workaround) -->
        @if(!empty($additionalButtons))
            @foreach($additionalButtons as $button)
                @if(isset($button['action']))
                    <x-button 
                        icon="{{ $button['icon'] ?? 'o-plus' }}" 
                        class="{{ $button['class'] ?? 'btn-outline btn-sm' }}"
                        wire:click="{{ $button['action'] }}"
                    >
                        {{ $button['text'] }}
                    </x-button>
                @else
                    <x-button 
                        icon="{{ $button['icon'] ?? 'o-plus' }}" 
                        class="{{ $button['class'] ?? 'btn-outline btn-sm' }}"
                    >
                        {{ $button['text'] }}
                    </x-button>
                @endif
            @endforeach
        @endif
        
        <!-- Default Button -->
        @if($buttonText)
            @if($buttonAction)
                <x-button 
                    icon="{{ $buttonIcon }}" 
                    class="{{ $buttonClass }}" 
                    wire:click="executeButtonAction"
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