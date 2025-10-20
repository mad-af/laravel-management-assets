<div class="space-y-4">
    @if($loan)
        <fieldset class="p-4 pt-2 w-full border fieldset bg-base-200 border-base-300 rounded-box">
            <legend class="fieldset-legend">Ringkasan Pinjaman</legend>
            <div class="space-y-1 text-base-content/70">
                <div>
                    <div class="flex gap-2 items-center">
                        @if (!$loan->asset?->image)
                            <div
                                class="flex justify-center items-center font-bold rounded-lg border-2 size-13 bg-base-300 border-base-100">
                                <x-icon name="o-photo" class="w-6 h-6 text-base-content/60" />
                            </div>
                        @else
                            <x-avatar :image="asset('storage/' . $loan->asset?->image)"
                                class="!w-13 !rounded-lg !bg-base-300 !font-bold border-2 border-base-100">
                            </x-avatar>
                        @endif
                        <div>
                            <div class="font-mono text-xs truncate text-base-content/60">{{ $loan->asset?->code }}</div>
                            <div class="font-medium">{{ $loan->asset?->name }}</div>
                            <div class="text-xs text-base-content/60">Tag: {{ $loan->asset?->tag_code }}</div>
                        </div>
                    </div>
                </div>
                <div>
                    <span class="block font-bold">Peminjam:</span>
                    <span class="badge badge-outline badge-sm">{{ $loan->employee?->full_name }}</span>
                </div>
                <div class="flex">
                    <div class="flex-1">
                        <span class="block font-bold">Tanggal Pinjam:</span>
                        <span>{{ optional($loan->checkout_at)->format('d-m-Y') }}</span>
                    </div>
                
                    <div class="flex-1">
                        <span class="block font-bold">Jatuh Tempo:</span>
                        <span>{{ optional($loan->due_at)->format('d-m-Y') }}</span>
                    </div>
                </div>
            </div>
        </fieldset>
    @endif

    <form wire:submit="save" class="space-y-4">
        <div>
            <x-input class="input-sm" label="Tanggal Pengembalian" wire:model="checkin_at" type="date" required />
        </div>

        <div>
            <x-select class="select-sm" label="Kondisi Saat Dikembalikan" wire:model="condition_in"
                :options="$conditions" option-value="value" option-label="label" placeholder="Pilih kondisi" required />
        </div>

        <div>
            <x-textarea class="textarea-sm" label="Catatan" wire:model="notes" placeholder="Masukkan catatan tambahan"
                rows="3" />
        </div>

        <div class="flex gap-2 justify-end pt-4">
            <x-button label="Batal" class="btn-ghost btn-sm" wire:click="$dispatch('close-drawer')" />
            <x-button label="Simpan" class="btn-primary btn-sm" type="submit" spinner="save" />
        </div>
    </form>
</div>