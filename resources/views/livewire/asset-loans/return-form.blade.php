<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-input label="Tanggal Pengembalian" wire:model="checkin_at" type="date" required />
        </div>
        <div>
            <x-select
                label="Kondisi Saat Dikembalikan"
                placeholder="Pilih kondisi"
                :options="$conditions"
                option-label="label"
                option-value="value"
                wire:model="condition_in"
                required
            />
        </div>
    </div>

    <x-textarea label="Catatan" wire:model="notes" placeholder="Catatan pengembalian (opsional)" rows="3" />

    <div class="flex items-center justify-end gap-2">
        <x-button label="Batal" class="btn-ghost" icon="o-x-mark" wire:click="$dispatch('close-drawer')" />
        <x-button label="Simpan" class="btn-primary" icon="o-check" wire:click="save" />
    </div>

    <div class="mt-6 border-t pt-4 text-sm text-gray-600">
        @if($loan)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <div>
                    <span class="font-medium">Asset:</span>
                    <span>{{ $loan->asset?->name }} ({{ $loan->asset?->code }})</span>
                </div>
                <div>
                    <span class="font-medium">Peminjam:</span>
                    <span>{{ $loan->employee?->full_name }}</span>
                </div>
                <div>
                    <span class="font-medium">Tanggal Pinjam:</span>
                    <span>{{ optional($loan->checkout_at)->format('Y-m-d') }}</span>
                </div>
                <div>
                    <span class="font-medium">Jatuh Tempo:</span>
                    <span>{{ optional($loan->due_at)->format('Y-m-d') }}</span>
                </div>
            </div>
        @endif
    </div>
</div>