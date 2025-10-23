<!-- Drawer -->
<div class="z-50 drawer drawer-end" x-data="{ open: @entangle('showDrawer') }"
    x-init="$watch('open', value => { if (!value) { document.getElementById('category-drawer').checked = false; } })">
    <input id="category-drawer" type="checkbox" class="drawer-toggle" x-model="open" />
    <div class="drawer-content">
        <!-- This content is handled by the main content above -->
    </div>
    <div class="drawer-side">
        <label for="category-drawer" class="drawer-overlay" wire:click="closeDrawer"></label>
        <div class="p-4 w-96 min-h-full bg-base-100 text-base-content">
            <!-- Drawer Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">
                    {{ $editingCategoryId ? 'Edit Category' : 'Add New Category' }}
                </h2>
                <button wire:click="closeDrawer" class="btn btn-sm btn-circle btn-ghost">
                    <x-icon name="o-x-mark" class="w-5 h-5" />
                </button>
            </div>

            <!-- Category Form -->
            <livewire:insurance-claims.form :categoryId="$editingCategoryId" :key="'category-form-' . ($editingCategoryId ?? 'new')" />
        </div>
    </div>
</div>