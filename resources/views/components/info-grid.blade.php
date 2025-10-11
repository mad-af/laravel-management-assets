@props([
    'title' => null,
    'icon' => null,
    'items' => [],
    'longTextItems' => [],
    'imageItems' => [],
    'columns' => 'md:grid-cols-2'
])

<x-info-card :title="$title" :icon="$icon">
    <div class="space-y-4">
        
    @if (count($imageItems) > 0)
        @foreach($imageItems as $item)
            <div>
                <label class="block text-sm font-medium text-base-content/70">{{ $item['label'] }}</label>
                @if ($item['path'])
                    <x-avatar :image="asset('storage/'.$item['path'])"
                        class="!w-18 !rounded-lg !bg-base-300 !font-bold border-2 border-base-100">
                    </x-avatar>
                @else
                    <div
                        class="flex justify-center items-center font-bold rounded-lg border-2 size-18 bg-base-300 border-base-100">
                        <x-icon name="o-photo" class="w-9 h-9 text-base-content/60" />
                    </div>
                @endif
            </div>
        @endforeach  
    @endif
    
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
            <div>
                <label class="text-sm font-medium text-base-content/70">{{ $item['label'] }}</label>
                <p class="p-3 mt-1 text-sm rounded-lg bg-base-200 text-base-content">{{ $item['value'] }}</p>
            </div>
            @endif
        @endforeach
    @endif
    
    {{ $slot }}
    </div>
</x-info-card>