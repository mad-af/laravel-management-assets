<!-- Dashboard Content Header Livewire Component -->
<div class="flex justify-between items-center space-y-4">
    <div class="flex gap-3 items-center">
        <!-- Back Button -->
        @if($showBackButton)
            <button onclick="history.back()" class="btn {{ $backButtonClass }}">
                <x-icon name="{{ $backButtonIcon }}" class="w-4 h-4" />
            </button>
        @endif

        <div>
            <h1 class="text-2xl font-bold text-base-content">{{ $title }}</h1>
            @if($description)
                <p class="text-base-content/70">{{ $description }}</p>
            @endif
        </div>
    </div>

    <div class="flex gap-2 items-center">
        <!-- Additional Buttons (Livewire v3 workaround) -->
        @if(!empty($additionalButtons))
            @foreach($additionalButtons as $button)
                @if(isset($button['action']))
                    <x-button icon="{{ $button['icon'] ?? 'o-plus' }}" class="{{ $button['class'] ?? ' btn-sm' }}"
                        wire:click="executeAdditionalButtonAction('{{ $button['action'] }}')">
                        {{ $button['text'] }}
                    </x-button>
                @else
                    <x-button icon="{{ $button['icon'] ?? 'o-plus' }}" class="{{ $button['class'] ?? ' btn-sm' }}">
                        {{ $button['text'] }}
                    </x-button>
                @endif
            @endforeach
        @endif

        <!-- Default Button -->
        @if($buttonText)
            @if($buttonAction)
                <x-button icon="{{ $buttonIcon }}" class="{{ $buttonClass }}" wire:click="executeButtonAction">
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