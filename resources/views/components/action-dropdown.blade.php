@props(['model'])

<div>
    <!-- Dropdown Button -->
    <button class="p-1 btn btn-ghost btn-sm hover:bg-base-200" popovertarget="dropdown-menu-{{ $model->id }}"
        style="anchor-name: --anchor-{{ $model->id }}">
        <x-icon name="o-ellipsis-vertical" class="w-4 h-4" />
    </button>

    <!-- Dropdown Menu -->
    <ul class="shadow-sm dropdown dropdown-end menu rounded-box bg-base-100" popover id="dropdown-menu-{{ $model->id }}"
        style="position-anchor: --anchor-{{ $model->id }};">
        {{ $slot }}
    </ul>
</div>