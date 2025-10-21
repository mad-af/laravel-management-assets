@props([
    'class' => '',
    'title' => '',
    'icon' => 'o-information-circle',
    'badgeValue' => null, // prop benar
    'badgeColor' => '',
    'link' => ''
])

<x-card shadow class="{{ !empty($class) ? $class : '' }}">
    <x-slot:title>
        <div class="flex justify-between items-center">
            <h2 class="text-lg card-title">
                <x-icon :name="$icon" class="w-5 h-5" />
                {{ $title }}
                @if(!empty($badgeValue))
                    <x-badge :value="$badgeValue" class="badge-soft {{ !empty($badgeColor) ? $badgeColor : '' }}" />
                @endif
            </h2>

            @if (! empty($link))
            <a href="{{ $link }}" class="btn btn-ghost btn-square btn-sm">
                <x-icon name="o-arrow-top-right-on-square" class="w-5 h-5" />
            </a>
            @endif
        </div>
    </x-slot:title>
    {{ $slot }}
</x-card>