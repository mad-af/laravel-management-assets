<!-- Edit Location Drawer -->
<div class="z-50 drawer drawer-end" x-data="{ open: @entangle('showDrawer') }"
    x-init="$watch('open', value => { if (!value) { document.getElementById('edit-location-drawer').checked = false; } })">
    <input id="edit-location-drawer" type="checkbox" class="drawer-toggle" x-model="open" />
    <div class="drawer-content">
        <!-- This content is handled by the main content above -->
    </div>
    <div class="drawer-side">
        <label for="edit-location-drawer" class="drawer-overlay" wire:click="closeDrawer"></label>
        <div class="p-4 w-80 min-h-full bg-base-100 text-base-content">
            <!-- Drawer Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">
                    Edit Location: {{ $location->name ?? 'Loading...' }}
                </h2>
                <button wire:click="closeDrawer" class="btn btn-sm btn-circle btn-ghost">
                    <x-icon name="o-x-mark" class="w-5 h-5" />
                </button>
            </div>

            <!-- Location Edit Form -->
            @if($location)
                <livewire:locations.form :locationId="$location->id" :key="'edit-form-' . $location->id" />
            @endif
        </div>
    </div>
</div>