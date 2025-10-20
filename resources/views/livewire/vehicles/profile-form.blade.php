<form wire:submit="save" class="space-y-2">

    <!-- Asset Selection -->
    <livewire:components.combobox name="assets" wire:model.live="asset_id" placeholder="Pilih aset" :options="$assets"
        option-value="id" option-label="name" option-sub-label="tag_code" option-meta="code" option-avatar="image"
        class="input-sm" label="Kendaraan" required />

    @if (! empty($asset_id))
    <!-- VIN -->
    <x-input name="vin" label="Nomor Rangka" class="input-sm" wire:model="vin" placeholder="Masukkan nomor rangka" />

    <!-- License Plate -->
    <x-input name="plate_no" label="Nomor Plat" class="input-sm" wire:model="plate_no"
        placeholder="Masukkan nomor plat kendaraan" />

    <!-- Owner -->
    <x-input name="owner" label="Pemilik" class="input-sm" wire:model="owner"
        placeholder="Masukkan nama pemilik kendaraan" />

    <!-- Vehicle Type -->
    <x-select name="type" label="Tipe Kendaraan" class="select-sm" wire:model="type" :options="$types"
        option-value="value" option-label="label" placeholder="Pilih tipe kendaraan" required />

    <!-- Purchase Year -->
    <x-input name="year_purchase" label="Tahun Pembelian" class="input-sm" wire:model="year_purchase" type="number"
        min="1900" max="{{ date('Y') + 1 }}" placeholder="Masukkan tahun pembelian" />

    <!-- Manufacture Year -->
    <x-input name="year_manufacture" label="Tahun Produksi" class="input-sm" wire:model="year_manufacture" type="number"
        min="1900" max="{{ date('Y') + 1 }}" placeholder="Masukkan tahun produksi" />

    <!-- Current Odometer -->
    <x-input name="current_odometer_km" label="Odometer Saat Ini (km)" class="input-sm" wire:model="current_odometer_km"
        type="number" min="0" placeholder="Masukkan pembacaan odometer saat ini" :readonly=$isEdit required />

    <!-- Last Service Date -->
    <x-datetime name="last_service_date" label="Tanggal Service Terakhir" class="input-sm"
        wire:model="last_service_date" type="date" />

    <!-- Service Target Odometer -->
    <x-input name="service_target_odometer_km" label="Target Odometer Service (km)" class="input-sm"
        wire:model="service_target_odometer_km" type="number" min="0"
        placeholder="Masukkan target odometer untuk service berikutnya"
        :disabled="!empty($this->service_target_odometer_km)" />

    <!-- Next Service Date -->
    <x-datetime name="next_service_date" label="Tanggal Service Berikutnya" class="input-sm"
        wire:model="next_service_date" type="date" :disabled="!empty($this->next_service_date)" />

    <!-- Annual Tax Due Date -->
    {{-- <x-datetime name="annual_tax_due_date" label="Tanggal Jatuh Tempo Pajak Tahunan" class="input-sm"
        wire:model="annual_tax_due_date" type="date" hint="Diisi untuk kelengkapan data pajak kendaraan" /> --}}

    <div class="flex gap-3 justify-end pt-4">
        <x-button label="Batal" class="btn-ghost btn-sm" type="button" wire:click="$dispatch('close-drawer')" />
        <button class="btn btn-sm btn-primary" type="submit">
            {{ $assetId ? 'Perbarui' : 'Simpan' }}
        </button>
    </div>
    @endif
</form>