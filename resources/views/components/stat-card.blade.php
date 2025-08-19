@props([
    'title',
    'value',
    'description' => null,
    'icon' => null,
    'color' => 'primary',
    'trend' => null,
    'trendDirection' => 'up'
])

<div class="stat">
    @if($icon)
        <div class="stat-figure text-{{ $color }}">
            {!! $icon !!}
        </div>
    @endif
    
    <div class="stat-title">{{ $title }}</div>
    <div class="stat-value text-{{ $color }}">{{ $value }}</div>
    
    @if($description || $trend)
        <div class="stat-desc">
            @if($trend)
                <span class="{{ $trendDirection === 'up' ? 'text-success' : 'text-error' }}">
                    @if($trendDirection === 'up')
                        ↗︎
                    @else
                        ↘︎
                    @endif
                    {{ $trend }}
                </span>
            @endif
            {{ $description }}
        </div>
    @endif
</div>