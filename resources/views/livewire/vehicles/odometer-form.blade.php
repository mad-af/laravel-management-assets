<form action="{{ route('vehicles.store-odometer') }}" method="POST" class="space-y-2">
    @csrf

    <!-- Asset Selection -->
    <x-select name="asset_id" label="Kendaraan" class="select-sm" wire:model.live="asset_id" :options="$assets"
        option-value="id" option-label="display_name" placeholder="Pilih kendaraan" required />

    <!-- Odometer Reading -->
    <x-input name="odometer_km" label="Pembacaan Odometer (km)" class="input-sm" wire:model="odometer_km" type="number"
        min="0" placeholder="Masukkan pembacaan odometer" required />

    <!-- Read Date/Time -->
    <x-datetime name="read_at" label="Tanggal & Waktu Pembacaan" class="input-sm" wire:model="read_at" type="datetime-local"
        required />

    <!-- Source -->
    <x-select name="source" label="Sumber Pembacaan" class="select-sm" wire:model="source" :options="$sources"
        option-value="value" option-label="label" placeholder="Pilih sumber pembacaan" required />

    <!-- Notes -->
    <x-textarea name="notes" class="textarea-sm" label="Catatan" wire:model="notes" rows="3"
        placeholder="Masukkan catatan tambahan (opsional)" />

    <div class="flex gap-3 justify-end pt-4">
        <x-button label="Batal" class="btn-ghost btn-sm" type="button" wire:click="$dispatch('close-drawer')" />
        <button class="btn btn-sm btn-primary" type="submit">
            Simpan
        </button>
    </div>
</form>