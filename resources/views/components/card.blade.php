@props([
    'title' => null,
    'subtitle' => null,
    'actions' => null,
    'compact' => false,
    'shadow' => 'shadow-xl',
    'class' => ''
])

<div class="card bg-base-100 {{ $shadow }} {{ $class }}">
    <div class="card-body {{ $compact ? 'p-4' : '' }}">
        @if($title || $subtitle || $actions)
            <div class="flex justify-between items-start mb-4">
                <div>
                    @if($title)
                        <h2 class="card-title text-lg font-semibold">{{ $title }}</h2>
                    @endif
                    @if($subtitle)
                        <p class="text-base-content/70 mt-1">{{ $subtitle }}</p>
                    @endif
                </div>
                @if($actions)
                    <div class="card-actions">
                        {{ $actions }}
                    </div>
                @endif
            </div>
        @endif
        
        {{ $slot }}
    </div>
</div>