<form wire:submit="save" class="space-y-2">
    {{-- Polis --}}
    <div>
        <x-select label="Polis" wire:model.live="policy_id" :options="$policyOptions" option-value="value"
            option-label="label" placeholder="Pilih polis" class="select-sm" />
    </div>

    {{-- Nomor Klaim --}}
    <div>
        <x-input label="Nomor Klaim" wire:model="claim_no" placeholder="Masukkan nomor klaim" class="input-sm" required />
    </div>

    {{-- Tanggal Insiden --}}
    <div>
        <x-input label="Tanggal Insiden" wire:model="incident_date" type="date" class="input-sm" required />
    </div>

    {{-- Jenis Insiden --}}
    <div>
        <x-select label="Jenis Insiden" wire:model="incident_type" :options="$incidentTypeOptions" option-value="value"
            option-label="label" placeholder="Pilih jenis insiden" class="select-sm" required />
    </div>

    {{-- Detail Insiden Lainnya --}}
    @if($incident_type === 'other')
        <div>
            <x-input label="Detail Insiden Lainnya" wire:model="incident_other" placeholder="Sebutkan detail insiden" class="input-sm" />
        </div>
    @endif

    {{-- Deskripsi --}}
    <div>
        <x-textarea label="Deskripsi" wire:model="description" placeholder="Masukkan deskripsi klaim" rows="3"
            class="textarea-sm" />
    </div>

    {{-- Sumber Klaim --}}
    <div>
        <x-select label="Sumber Klaim" wire:model="source" :options="$sourceOptions" option-value="value"
            option-label="label" placeholder="Pilih sumber klaim" class="select-sm" required />
    </div>

    {{-- Status --}}
    <div>
        <x-select label="Status" wire:model="status" :options="$statusOptions" option-value="value"
            option-label="label" placeholder="Pilih status" class="select-sm" required />
    </div>

    {{-- Jumlah Disetujui --}}
    <div>
        <x-input label="Jumlah Disetujui (Rp)" prefix="Rp" wire:model="amount_approved" type="number" step="0.01" min="0" class="input-sm" />
    </div>

    {{-- Jumlah Dibayar --}}
    <div>
        <x-input label="Jumlah Dibayar (Rp)" prefix="Rp" wire:model="amount_paid" type="number" step="0.01" min="0" class="input-sm" />
    </div>

    <!-- Submit Button -->
    <div class="flex gap-2 justify-end pt-4">
        <x-button label="Batal" class="btn-ghost btn-sm" wire:click="$dispatch('close-drawer')" />
        <x-button label="{{ $isEdit ? 'Update' : 'Simpan' }}" class="btn-primary btn-sm" type="submit" spinner="save" />
    </div>
</form>