<!-- Edit Company Drawer -->
<div class="z-50 drawer drawer-end" x-data="{ open: @entangle('showDrawer') }"
    x-init="$watch('open', value => { if (!value) { document.getElementById('edit-company-drawer').checked = false; } })">
    <input id="edit-company-drawer" type="checkbox" class="drawer-toggle" x-model="open" />
    <div class="drawer-content">
        <!-- This content is handled by the main content above -->
    </div>
    <div class="drawer-side">
        <label for="edit-company-drawer" class="drawer-overlay" wire:click="closeDrawer"></label>
        <div class="p-4 w-80 min-h-full bg-base-100 text-base-content">
            <!-- Drawer Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">
                    Edit Company: {{ $company->name ?? 'Loading...' }}
                </h2>
                <button wire:click="closeDrawer" class="btn btn-sm btn-circle btn-ghost">
                    <x-mary-icon name="o-x-mark" class="w-5 h-5" />
                </button>
            </div>

            <!-- Company Edit Form -->
            @if($company)
                <livewire:companies.form :companyId="$company->id" :key="'edit-form-' . $company->id" />
            @endif
        </div>
    </div>
</div>