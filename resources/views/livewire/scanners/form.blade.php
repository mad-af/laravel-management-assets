<form wire:submit="save" class="space-y-4">
    <!-- Scanner Name -->
    <div>
        <x-input label="Nama Scanner" wire:model="name" placeholder="Masukkan nama scanner" required />
    </div>

    <!-- Scanner Type -->
    <div>
        <x-select label="Tipe Scanner" wire:model="type" :options="[
            ['value' => 'QR Scanner', 'label' => 'QR Scanner'],
            ['value' => 'Barcode Scanner', 'label' => 'Barcode Scanner'],
            ['value' => 'QR/Barcode Scanner', 'label' => 'QR/Barcode Scanner'],
            ['value' => 'RFID Scanner', 'label' => 'RFID Scanner']
        ]" option-value="value" option-label="label" placeholder="Pilih tipe scanner" required />
    </div>

    <!-- Location -->
    <div>
        <x-input label="Lokasi" wire:model="location" placeholder="Masukkan lokasi scanner" required />
    </div>

    <!-- Status -->
    <div>
        <x-select label="Status" wire:model="status" :options="[
            ['value' => 'active', 'label' => 'Aktif'],
            ['value' => 'inactive', 'label' => 'Tidak Aktif'],
            ['value' => 'maintenance', 'label' => 'Maintenance']
        ]" option-value="value" option-label="label" placeholder="Pilih status" required />
    </div>

    <!-- Description -->
    <div>
        <x-textarea label="Deskripsi (Opsional)" wire:model="description" placeholder="Masukkan deskripsi scanner" rows="3" />
    </div>

    <!-- Active Status -->
    <div>
        <x-checkbox label="Scanner Aktif" wire:model="is_active" />
    </div>

    <!-- Submit Button -->
    <div class="flex gap-2 justify-end pt-4">
        <x-button label="Batal" class="btn-ghost" wire:click="$dispatch('close-drawer')" />
        <x-button label="{{ $isEdit ? 'Update' : 'Simpan' }}" class="btn-primary" type="submit" spinner="save" />
    </div>
</form>