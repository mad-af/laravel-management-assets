<form wire:submit="save" class="space-y-2">
    <!-- Asset -->
    <div>
        <livewire:components.combobox name="assets" label="Asset" placeholder="Pilih asset" wire:model.live="asset_id"
            :options="$assets" option-value="id" option-label="name" option-sub-label="tag_code" option-meta="code"
            option-avatar="image" class="select-sm" required :onfocusload="true" />
    </div>

    <!-- Provider -->
    <div>
        <x-select label="Provider Asuransi" placeholder="Pilih provider" wire:model.live="insurance_id"
            :options="$insurances" option-value="id" option-label="name" class="select-sm" required />
    </div>

    <!-- Policy Number -->
    <div>
        <x-input label="No Polis" wire:model="policy_no" placeholder="Masukkan nomor polis" class="input-sm" required />
    </div>

    <!-- Policy Type -->
    <div>
        <x-select label="Tipe Polis" wire:model="policy_type" :options="$policyTypes" option-value="value"
            option-label="label" placeholder="Pilih tipe polis" class="select-sm" required />
    </div>

    <!-- Start Date -->
    <div>
        <x-input label="Tanggal Mulai" wire:model="start_date" type="date" class="input-sm" required />
    </div>

    <!-- End Date -->
    <div>
        <x-input label="Tanggal Selesai" wire:model="end_date" type="date" class="input-sm" required />
    </div>

    <!-- Notes -->
    <div>
        <x-textarea label="Catatan" wire:model="notes" placeholder="Masukkan catatan (opsional)" rows="3"
            class="textarea-sm" />
    </div>

    <!-- Submit Button -->
    <div class="flex gap-2 justify-end pt-4">
        <x-button label="Batal" class="btn-ghost btn-sm" wire:click="$dispatch('close-drawer')" />
        <x-button label="{{ $isEdit ? 'Update' : 'Simpan' }}" class="btn-primary btn-sm" type="submit" spinner="save"
            :disabled="!$asset_id
        || !$insurance_id
        || !$policy_no
        || !$policy_type
        || !$start_date
        || !$end_date" />
    </div>
</form>