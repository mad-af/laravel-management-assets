@props(['model'])

<div>
    <!-- Dropdown Trigger -->
    <button class="w-full h-full text-left" popovertarget="dropdown-card-{{ $model->id }}"
        style="anchor-name: --anchor-{{ $model->id }}">
        {{ $trigger }}
    </button>

    <!-- Dropdown Menu -->
    <ul class="shadow-sm dropdown dropdown-right menu rounded-box bg-base-100" popover id="dropdown-card-{{ $model->id }}"
        style="position-anchor: --anchor-{{ $model->id }};">
        {{ $slot }}
    </ul>
</div>