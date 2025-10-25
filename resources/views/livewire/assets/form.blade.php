<form wire:submit="save" class="space-y-2">
    <!-- Category -->
    <div>
        <x-select label="Kategori" wire:model.live="category_id" :options="$categories" option-value="id"
            class="select-sm" option-label="name" placeholder="Pilih kategori" required :disabled="$isEdit" />
    </div>

    <!-- Asset Code -->
    <div>
        <x-input label="Kode Asset" wire:model="code" placeholder="Masukkan kode asset" class="input-sm" required
            hint="Kode aset terisi otomatis, bisa diubah manual bila perlu." :readonly="!$codeIsset">
            <x-slot:append>
                @if(!$codeIsset)
                    <x-button icon="o-pencil-square" tooltip-left="Input manual" class="join-item btn-sm"
                        wire:click="$set('codeIsset', true)" />
                @else
                    <x-button icon="o-calculator" tooltip-left="Generate otomatis" class="join-item btn-sm"
                        wire:click="$set('codeIsset', false); $wire.generateCode()" />
                @endif
            </x-slot:append>
        </x-input>
    </div>

    <!-- Upload Gambar Asset -->
    <div>
        <livewire:components.image-upload 
            :current-image="$image" 
            label="Gambar Asset" 
            directory="assets"
            wire:key="image-upload-{{ $asset->id ?? 'new' }}" />
    </div>

    <!-- Asset Name -->
    <div>
        <x-input label="Nama Asset" wire:model="name" placeholder="Masukkan nama asset" class="input-sm" required />
    </div>

    <!-- Status -->
    <div>
        <x-select label="Status" wire:model="status" :options="$statuses" option-value="value" option-label="label"
            placeholder="Pilih status" required class="select-sm" :disabled="$isEdit" />
    </div>

    <!-- Condition -->
    <div>
        <x-select label="Kondisi" wire:model="condition" :options="$conditions" option-value="value"
            option-label="label" placeholder="Pilih kondisi" required class="select-sm" />
    </div>

    <!-- Value -->
    <div>
        <x-input label="Nilai Asset (Rp)" prefix="Rp" wire:model="value" placeholder="Masukkan nilai asset"
            type="number" step="0.01" min="0" class="input-sm" required/>
    </div>

    <!-- Brand -->
    <div>
        <x-input label="Brand/Merek" wire:model="brand" placeholder="Masukkan brand/merek asset" class="input-sm" />
    </div>

    <!-- Model -->
    <div>
        <x-input label="Model/Tipe" wire:model="model" placeholder="Masukkan model/tipe asset" class="input-sm" />
    </div>

    <!-- Purchase Date -->
    <div>
        <x-input label="Tanggal Pembelian" wire:model="purchase_date" type="date" class="input-sm" />
    </div>

    <!-- Description -->
    <div>
        <x-textarea label="Deskripsi" wire:model="description" placeholder="Masukkan deskripsi asset" rows="3"
            class="textarea-sm" />
    </div>

    <!-- Submit Button -->
    <div class="flex gap-2 justify-end pt-4">
        <x-button label="Batal" class="btn-ghost btn-sm" wire:click="$dispatch('close-drawer')" />
        <x-button label="{{ $isEdit ? 'Update' : 'Simpan' }}" class="btn-primary btn-sm" type="submit" spinner="save" />
    </div>
</form>