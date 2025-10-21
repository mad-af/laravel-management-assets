<!-- Drawer -->
<div class="z-50 drawer drawer-end" x-data="{ open: @entangle('showDrawer') }"
    x-init="$watch('open', value => { if (!value) { document.getElementById('transfer-drawer').checked = false; } })">
    <input id="transfer-drawer" type="checkbox" class="drawer-toggle" x-model="open" />
    <div class="drawer-content">
        <!-- This content is handled by the main content above -->
    </div>
    <div class="drawer-side">
        <label for="transfer-drawer" class="drawer-overlay" wire:click="closeDrawer"></label>
        <div class="p-4 w-96 min-h-full bg-base-100 text-base-content">
            <!-- Drawer Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">
                    @if($detailTransfer)
                        {{ $detailMode === 'confirm' ? 'Konfirmasi Penerimaan Asset' : 'Detail Asset Transfer' }}
                    @else
                        {{ $editingTransferId ? 'Edit Asset Transfer' : 'Add New Asset Transfer' }}
                    @endif
                </h2>
                <button wire:click="closeDrawer" class="btn btn-sm btn-circle btn-ghost">
                    <x-icon name="o-x-mark" class="w-5 h-5" />
                </button>
            </div>

            @if($detailTransfer)
                <livewire:asset-transfers.detail-info :transferData="$detailInfoData" />

                @if($detailMode === 'confirm')
                    <livewire:asset-transfers.confirm-receipt :transferId="$detailTransfer->id" />
                @endif
            @else
                <!-- Asset Transfer Form -->
                <livewire:asset-transfers.form :transferId="$editingTransferId" :key="'transfer-form-' . ($editingTransferId ?? 'new')" />
            @endif
        </div>
    </div>
</div>