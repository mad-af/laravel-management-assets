@props([
    'title' => null,
    'icon' => null,
    'items' => [],
    'longTextItems' => [],
    'columns' => 'md:grid-cols-2'
])

<x-info-card :title="$title" :icon="$icon">
    @if(count($items) > 0)
        <div class="grid grid-cols-1 gap-4 {{ $columns }}">
            @foreach($items as $item)
                <div>
                    <label class="block text-sm font-medium text-base-content/70">{{ $item['label'] }}</label>
                    @if(isset($item['badge']) && $item['badge'])
                        <x-badge 
                            value="{{ $item['value'] }}" 
                            class="{{ $item['badge_class'] ?? 'badge-neutral' }}" 
                        />
                    @elseif(isset($item['mono']) && $item['mono'])
                        <p class="font-mono">{{ $item['value'] ?? '-' }}</p>
                    @else
                        <p class="{{ $item['class'] ?? '' }}">{{ $item['value'] ?? '-' }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
    
    @if(count($longTextItems) > 0)
        @foreach($longTextItems as $item)
            @if($item['value'])
            <div class="mt-4">
                <label class="text-sm font-medium text-base-content/70">{{ $item['label'] }}</label>
                <p class="p-3 mt-1 text-sm rounded-lg bg-base-200 text-base-content">{{ $item['value'] }}</p>
            </div>
            @endif
        @endforeach
    @endif
    
    {{ $slot }}
</x-info-card>