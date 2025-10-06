@props(['model', 'isLast' => false])

<div>
    <!-- Dropdown Trigger -->
    <button class="w-full h-full text-left" popovertarget="dropdown-card-{{ $model->id }}"
        style="anchor-name: --anchor-{{ $model->id }}">
        {{ $trigger }}
    </button>

    <!-- Dropdown Menu -->
    <ul class="shadow-sm dropdown {{ $isLast ? 'dropdown-left' : 'dropdown-right' }} menu rounded-box bg-base-100" 
        popover 
        id="dropdown-card-{{ $model->id }}"
        style="position-anchor: --anchor-{{ $model->id }};"
        onclick="closeDropdown(event, 'dropdown-card-{{ $model->id }}')">    
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