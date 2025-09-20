@props([
    'title' => null,
    'icon' => null,
    'items' => [],
    'description' => null,
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
    
    @if($description)
        <div class="mt-4">
            <label class="text-sm font-medium text-base-content/70">Description</label>
            <p class="mt-1">{{ $description }}</p>
        </div>
    @endif
    
    {{ $slot }}
</x-info-card>