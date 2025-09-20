@props(['title', 'icon' => 'o-information-circle'])

<x-card shadow>
    <x-slot:title>
        <h2 class="text-lg card-title">
            <x-icon :name="$icon" class="w-5 h-5" />
            {{ $title }}
        </h2>
    </x-slot:title>
    {{ $slot }}
</x-card>