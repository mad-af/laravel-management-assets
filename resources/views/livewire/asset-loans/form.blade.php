<form wire:submit="save" class="space-y-4">
    <!-- Asset Selection -->
    <div>
        <x-select label="Asset" wire:model="asset_id" :options="$assets" option-value="id" option-label="name"
            placeholder="Pilih asset" required />
    </div>

    <!-- Borrower Name -->
    <div>
        <x-input label="Nama Peminjam" wire:model="borrower_name" placeholder="Masukkan nama peminjam" required />
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

    <!-- Condition Out -->
    <div>
        {{-- <x-select label="Kondisi Saat Dipinjam" wire:model="condition_out" :options="$conditions" option-value="value"
            option-label="label" placeholder="Pilih kondisi" required /> --}}
    </div>

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