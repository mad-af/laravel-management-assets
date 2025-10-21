<form wire:submit="save" class="mb-24 space-y-2">

    <!-- Reason -->
    <x-textarea name="reason" class="textarea-sm" label="Alasan Transfer" wire:model="reason" rows="3"
        placeholder="Masukkan alasan transfer asset" required />

    <!-- Locations - Side by Side -->
    <div class="grid grid-cols-2 gap-4">
        <x-select name="from_branch_id" label="Dari Cabang" class="select-sm" wire:model="from_branch_id"
            :options="$fromBranches" option-value="id" option-label="name" disabled required />

        <x-select-group label="Ke Cabang" class="select-sm" wire:model="to_branch_id"
            :options="$toGroupedBranches" placeholder="Pilih cabang" required />
    </div>

    <!-- Notes -->
    <x-textarea name="notes" class="textarea-sm" label="Catatan" wire:model="notes" rows="3"
        placeholder="Masukkan catatan tambahan jika ada" />

    <fieldset class="p-2 w-full border fieldset bg-base-200 border-base-300 rounded-box">
        <legend class="fieldset-legend">Asset Items</legend>

        <div class="space-y-2">
            @foreach($items as $index => $item)
                <div wire:key="item-{{ $item['id'] ?? $item['uid'] }}">
                    <fieldset class="p-2 border fieldset bg-base-100 border-base-300 rounded-box">
                        <legend class="flex justify-between items-center w-full fieldset-legend">
                            <span>Asset Item {{ $index + 1 }}</span>
                            @if(count($items) > 1)
                                <button type="button" wire:click="removeItem({{ $index }})"
                                    class="ml-2 btn btn-xs btn-circle text-error">
                                    <x-icon name="o-x-mark" class="w-3 h-3" />
                                </button>
                            @endif
                        </legend>

                        <div class="space-y-2">
                            <livewire:components.combobox
                                name="assets"
                                label="Asset"
                                class="input-sm"
                                wire:key="combobox-{{ $item['id'] ?? $item['uid'] }}"
                                wire:model.live="items.{{ $index }}.asset_id"
                                :options="$assets"
                                option-value="id"
                                option-label="name"
                                option-sub-label="tag_code"
                                option-meta="code"
                                option-avatar="image"
                                placeholder="Pilih asset"
                                :required="true"
                                :onfocusload="true"
                            />

                            @if(false)
                            <div class="grid grid-cols-2 gap-2">
                                <!-- Select-nya disabled (tidak terkirim) → kirim via hidden -->
                                <x-select name="items[{{ $index }}][from_branch_id]" label="From Branch"
                                    class="select-sm" wire:model="items.{{ $index }}.from_branch_id" :options="$branches"
                                    option-value="id" option-label="name" />

                                <x-select name="items[{{ $index }}][to_branch_id]" label="To Branch" class="select-sm"
                                    wire:model="items.{{ $index }}.to_branch_id" :options="$branches" option-value="id"
                                    option-label="name" />
                            </div>
                            @endif
                        </div>
                    </fieldset>
                </div>
            @endforeach
        </div>

        <button type="button" wire:click="addItem" class="mt-3 w-full btn btn-sm">
            <x-icon name="o-plus" class="w-3 h-3" />
            Tambah Asset Item
        </button>
    </fieldset>

    <div class="flex gap-3 justify-end pt-4">
        <x-button label="Batal" class="btn-ghost btn-sm" type="button" wire:click="$dispatch('close-drawer')" />
        <button class="btn btn-sm btn-primary" type="submit">
            {{ $isEdit ? 'Update' : 'Simpan' }}
        </button>
    </div>
</form>