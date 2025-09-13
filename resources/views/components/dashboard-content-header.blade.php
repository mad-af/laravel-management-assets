<!-- Dashboard Content Header Blade Component -->
<div class="flex justify-between items-center mb-3">
    <div>
        <h1 class="text-2xl font-bold text-base-content">{{ $title }}</h1>
        @if($description)
            <p class="text-base-content/70">{{ $description }}</p>
        @endif
    </div>
    
    <div class="flex gap-2 items-center">
        <!-- Additional Buttons -->
        @if(!empty($additionalButtons))
            @foreach($additionalButtons as $button)
                @if(isset($button['action']))
                    <x-button 
                        icon="{{ $button['icon'] ?? 'o-plus' }}" 
                        class="{{ $button['class'] ?? 'btn-outline btn-sm' }}"
                        onclick="{{ $button['action'] }}"
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
                    class="{{ $buttonClass ?? 'btn-primary btn-sm' }}" 
                    onclick="{{ $buttonAction }}"
                >
                    {{ $buttonText }}
                </x-button>
            @else
                <label for="maintenance-drawer" class="btn {{ $buttonClass }} cursor-pointer">
                    @if($buttonIcon)
                        <x-icon name="{{ $buttonIcon }}" class="w-4 h-4" />
                    @endif
                    {{ $buttonText }}
                </label>
            @endif
        @endif
    </div>
</div>