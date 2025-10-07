@props(['model'])

<div>
    <!-- Dropdown Button -->
    <button class="p-1 btn btn-ghost btn-sm hover:bg-base-200" popovertarget="dropdown-menu-{{ $model->id }}"
        style="anchor-name: --anchor-{{ $model->id }}">
        <x-icon name="o-ellipsis-vertical" class="w-4 h-4" />
    </button>

    <!-- Dropdown Menu -->
    <ul class="shadow-sm dropdown dropdown-end menu rounded-box bg-base-100" popover id="dropdown-menu-{{ $model->id }}"
        style="position-anchor: --anchor-{{ $model->id }};"
        onclick="closeDropdown(event, 'dropdown-menu-{{ $model->id }}')">
        {{ $slot }}
    </ul>
</div>

<script>
    function closeDropdown(event, dropdownId) {
        // Cek apakah yang diklik adalah item menu (button atau anchor)
        const clickedElement = event.target.closest('button, a');
        if (clickedElement) {
            // Tutup popover setelah sedikit delay untuk memungkinkan aksi selesai
            setTimeout(() => {
                const dropdown = document.getElementById(dropdownId);
                if (dropdown) {
                    dropdown.hidePopover();
                }
            }, 100);
        }
    }
</script>