<!-- Drawer -->
<div class="z-50 drawer drawer-end" x-data="{ open: @entangle('showDrawer') }"
    x-init="$watch('open', value => { if (!value) { document.getElementById('vehicle-tax-drawer').checked = false; } })">
    <input id="vehicle-tax-drawer" type="checkbox" class="drawer-toggle" x-model="open" />
    <div class="drawer-content">
        <!-- This content is handled by the main content above -->
    </div>
    <div class="drawer-side">
        <label for="vehicle-tax-drawer" class="drawer-overlay" wire:click="closeDrawer"></label>
        <div class="p-4 w-96 min-h-full bg-base-100 text-base-content">
            <!-- Drawer Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">
                    @if($action === 'tax-type')
                        Konfigurasi Jenis Pajak
                    @elseif($action === 'tax-payment')
                        Pembayaran Pajak Kendaraan
                    @endif
                </h2>
                <button wire:click="closeDrawer" class="btn btn-sm btn-circle btn-ghost">
                    <x-icon name="o-x-mark" class="w-5 h-5" />
                </button>
            </div>

            <!-- Form Content Based on Action -->
            @if($action === 'tax-type')
                <!-- Vehicle Tax Type Form -->
                <livewire:vehicle-taxes.tax-type-form :assetId="$asset_id"
                    :key="'vehicle-tax-type-form-' . ($asset_id ?? 'new')" />
            @elseif($action === 'tax-payment')
                <!-- Vehicle Tax History Form -->
                <livewire:vehicle-taxes.tax-payment-form :vehicleTaxId="$asset_id" :key="'vehicle-tax-form-' . ($asset_id ?? 'new')" />
            @endif
        </div>
    </div>
</div>