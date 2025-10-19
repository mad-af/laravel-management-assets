<form wire:submit="save" class="space-y-4">
    <!-- Asset Selection -->
    <div>
        <livewire:components.combobox name="assets" wire:model="asset_id" :options="$assets" option-value="id" option-label="name"
            option-sub-label="tag_code" option-meta="code" option-avatar="image" label="Asset" placeholder="Pilih asset" multiple required class="input-sm" />
    </div>

    <!-- Borrower (Employee) Combobox -->
    <div>
        <livewire:components.combobox name="employees" wire:model="employee_id" :options="$employees" option-value="id"
            option-label="full_name" option-sub-label="email" label="Peminjam" placeholder="Pilih karyawan" required
            class="input-sm" />
    </div>

    <!-- Checkout Date -->
    <div>
        <x-input label="Tanggal Pinjam" wire:model="checkout_at" type="date" required />
    </div>

    <!-- Due Date -->
    <div>
        <x-input label="Tanggal Jatuh Tempo" wire:model="due_at" type="date" required />
    </div>

    <!-- Checkin Date (for edit mode) -->
    @if($isEdit)
        <div>
            <x-input label="Tanggal Kembali" wire:model="checkin_at" type="date" />
            @if(!$checkin_at)
                <div class="mt-2">
                    <x-button label="Tandai Dikembalikan" class="btn-sm btn-success" wire:click="returnAsset" />
                </div>
            @endif
        </div>
    @endif

    <!-- Condition In (for edit mode or when returned) -->
    @if($isEdit && $checkin_at)
        <div>
            <x-select label="Kondisi Saat Dikembalikan" wire:model="condition_in" :options="$conditions"
                option-value="value" option-label="label" placeholder="Pilih kondisi" />
        </div>
    @endif

    <!-- Notes -->
    <div>
        <x-textarea label="Catatan" wire:model="notes" placeholder="Masukkan catatan tambahan" rows="3" />
    </div>

    <!-- Submit Button -->
    <div class="flex gap-2 justify-end pt-4">
        <x-button label="Batal" class="btn-ghost" wire:click="$dispatch('close-drawer')" />
        <x-button label="{{ $isEdit ? 'Update' : 'Simpan' }}" class="btn-primary" type="submit" spinner="save" />
    </div>
</form>