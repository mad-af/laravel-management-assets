@props([
    'title' => '',
    'value' => '',
    'description' => '',
    'icon' => null,
    'iconPosition' => 'left', // 'left' or 'right'
    'iconColor' => 'text-primary',
    'valueColor' => 'text-primary',
    'iconBgColor' => 'bg-base-200',
    'class' => ''
])

<div class="stat {{ $class }}">
    @if($iconPosition === 'left' && $icon)
        <div class="stat-figure {{ $iconColor }}">
            <div class="w-12 h-12 rounded-lg {{ $iconBgColor }} flex items-center justify-center">
                <x-icon name="{{ $icon }}" class="w-6 h-6 {{ $iconColor }}" />
            </div>
        </div>
    @endif
    
    <div class="stat-title">{{ $title }}</div>
    <div class="stat-value {{ $valueColor }}">{{ $value }}</div>
    
    @if($description)
        <div class="stat-desc">{{ $description }}</div>
    @endif
    
    @if($iconPosition === 'right' && $icon)
        <div class="stat-figure {{ $iconColor }}">
            <div class="w-12 h-12 rounded-lg {{ $iconBgColor }} flex items-center justify-center">
                <x-icon name="{{ $icon }}" class="w-6 h-6 {{ $iconColor }}" />
            </div>
        </div>
    @endif
</div>