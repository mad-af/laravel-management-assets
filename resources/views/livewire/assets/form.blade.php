<form wire:submit="save" class="space-y-4">
    <!-- Asset Code -->
    <div>
        <x-input label="Kode Asset" wire:model="code" placeholder="Masukkan kode asset" required />
    </div>

    <!-- Tag Code -->
    <div>
        <x-input label="Tag Code (Opsional)" wire:model="tag_code" placeholder="Masukkan tag code" />
    </div>

    <!-- Asset Name -->
    <div>
        <x-input label="Nama Asset" wire:model="name" placeholder="Masukkan nama asset" required />
    </div>

    <!-- Category -->
    <div>
        <x-select label="Kategori" wire:model="category_id" :options="$categories" option-value="id" option-label="name"
            placeholder="Pilih kategori" required />
    </div>

    <!-- Location -->
    <div>
        <x-select label="Lokasi" wire:model="location_id" :options="$locations" option-value="id" option-label="name"
            placeholder="Pilih lokasi" required />
    </div>

    <!-- Status -->
    <div>
        <x-select label="Status" wire:model="status" :options="$statuses" option-value="value" option-label="label"
            placeholder="Pilih status" required />
    </div>

    <!-- Condition -->
    <div>
        <x-select label="Kondisi" wire:model="condition" :options="$conditions" option-value="value"
            option-label="label" placeholder="Pilih kondisi" required />
    </div>

    <!-- Value -->
    <div>
        <x-input label="Nilai Asset (Rp)" wire:model="value" placeholder="Masukkan nilai asset" type="number"
            step="0.01" min="0" />
    </div>

    <!-- Purchase Date -->
    <div>
        <x-input label="Tanggal Pembelian" wire:model="purchase_date" type="date" />
    </div>

    <!-- Description -->
    <div>
        <x-textarea label="Deskripsi" wire:model="description" placeholder="Masukkan deskripsi asset" rows="3" />
    </div>

    <!-- Submit Button -->
    <div class="flex gap-2 justify-end pt-4">
        <x-button label="Batal" class="btn-ghost" wire:click="$dispatch('close-drawer')" />
        <x-button label="{{ $isEdit ? 'Update' : 'Simpan' }}" class="btn-primary" type="submit" spinner="save" />
    </div>
</form>