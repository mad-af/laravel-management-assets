<form wire:submit="save" class="space-y-2">
    {{-- Polis --}}
    <div>
        <livewire:components.combobox
            name="policies"
            wire:model.live="policy_id"
            :options="$policyOptions"
            option-value="id"
            option-label="asset_name"
            option-sub-label="insurance_name"
            option-meta="policy_no"
            label="Polis"
            placeholder="Pilih polis"
            class="input-sm"
            :required="true"
            wire:key="combobox-policy-{{ $claimId ?? 'new' }}"
        />
    </div>

    {{-- Nomor Klaim --}}
    <div>
        <x-input label="Nomor Klaim" wire:model="claim_no" placeholder="Masukkan nomor klaim" class="input-sm"
            required />
    </div>

    {{-- Sumber Klaim --}}
    <div>
        <x-select label="Sumber Klaim" wire:model="source" :options="$sourceOptions" option-value="value"
            option-label="label" placeholder="Pilih sumber klaim" class="select-sm" required disabled />
    </div>

    <div>
        <x-input label="Jumlah Pembayaran" wire:model="amount_paid" placeholder="Belum ditentukan" prefix="RP" type="number"
        class="input-sm" required :disabled="$isEdit || $source === \App\Enums\InsuranceClaimSource::MAINTENANCE->value" 
        hint="{{ $source === \App\Enums\InsuranceClaimSource::MAINTENANCE->value ? 'Catatan: Pengeluaran otomatis mengikuti perawatan' : '' }}"
        />
    </div>

    <fieldset class="fieldset bg-base-200 border-base-300 rounded-box w-full border p-4 pt-2">
        <legend class="fieldset-legend">Detail Insiden</legend>

        {{-- Tanggal Insiden --}}
        <div>
            <x-input label="Tanggal Insiden" wire:model="incident_date" type="date" class="input-sm" required />
        </div>

        {{-- Jenis Insiden --}}
        <div>
            <x-select label="Jenis Insiden" wire:model.live="incident_type" :options="$incidentTypeOptions"
                option-value="value" option-label="label" placeholder="Pilih jenis insiden" class="select-sm"
                required />
        </div>

        {{-- Detail Insiden Lainnya --}}
        @if($incident_type === 'other')
            <div>
                <x-input label="Detail Insiden Lainnya" wire:model="incident_other" placeholder="Sebutkan detail insiden"
                    class="input-sm" required />
            </div>
        @endif

        {{-- Deskripsi --}}
        <div>
            <x-textarea label="Deskripsi" wire:model="description" placeholder="Masukkan deskripsi klaim" rows="3"
                class="textarea-sm" />
        </div>

        <div>
            <livewire:components.image-upload
                :current-image="$currentClaimImage"
                label="Foto Bukti"
                hint="Format: JPG, PNG, WebP. Maksimal 2MB. Gambar akan dikompresi."
                directory="claims"
                wire:key="claim-image-upload-{{ $claimId ?? 'new' }}"
            />
        </div>

    </fieldset>

    <!-- Submit Button -->
    <div class="flex gap-2 justify-end pt-4">
        <x-button label="Batal" class="btn-ghost btn-sm" wire:click="$dispatch('close-drawer')" />
        <x-button
            label="{{ $isEdit ? 'Update' : 'Simpan' }}"
            class="btn-primary btn-sm"
            type="submit"
            spinner="save"
            :disabled="!$policy_id
                || !$claim_no
                || !$incident_date
                || !$incident_type
                || (($incident_type === 'other') && !$incident_other)
                || ((!$isEdit && ($source !== \App\Enums\InsuranceClaimSource::MAINTENANCE->value)) && !$amount_paid)"
        />
    </div>
</form>