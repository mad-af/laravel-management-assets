<form wire:submit="save" class="space-y-4">

    <!-- Status -->
    <x-select label="Status" class="select-sm" wire:model="status" :options="$statusOptions" option-value="value"
        option-label="label" placeholder="Pilih status" required />

    <!-- Scheduled Date -->
    <x-datetime label="Dijadwalkan" class="input-sm" wire:model="scheduled_at" type="datetime-local" />
    {{-- <x-input label="Dijadwalkan" class="input-sm" wire:model="scheduled_at" type="datetime-local" /> --}}

    <!-- Locations - Side by Side -->
    <div class="grid grid-cols-2 gap-4">
        <x-select label="Dari Lokasi" class="select-sm" wire:model="from_location_id" :options="$locations"
            option-value="id" option-label="name" placeholder="Pilih lokasi asal" />
        <x-select label="Ke Lokasi" class="select-sm" wire:model="to_location_id" :options="$locations"
            option-value="id" option-label="name" placeholder="Pilih lokasi tujuan" />
    </div>

    <!-- Reason -->
    <x-textarea class="textarea-sm" label="Alasan Transfer" wire:model="reason"
        placeholder="Masukkan alasan transfer aset" rows="3" required />

    <!-- Asset Items Section -->
    <fieldset class="p-2 w-full border fieldset bg-base-200 border-base-300 rounded-box">
        <legend class="fieldset-legend">Asset Items</legend>
        
        <div class="space-y-2">
            @if(count($items) > 0)
                @foreach($items as $index => $item)
                    <div wire:key="item-{{ $index }}">
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
                                <div>
                                    <x-select label="Asset" class="select-sm" wire:model="items.{{ $index }}.asset_id"
                                        :options="$assets" option-value="id" option-label="name" placeholder="Pilih asset"
                                        required />
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <x-select label="From Location" class="select-sm" wire:model="items.{{ $index }}.from_location_id"
                                            :options="$locations" option-value="id" option-label="name" placeholder="Dari lokasi" required />
                                    </div>

                                    <div>
                                        <x-select label="To Location" class="select-sm" wire:model="items.{{ $index }}.to_location_id"
                                            :options="$locations" option-value="id" option-label="name" placeholder="Ke lokasi" required />
                                    </div>
                                </div>
                                
                                <div>
                                    <x-textarea label="Notes" class="textarea-sm" wire:model="items.{{ $index }}.notes"
                                        placeholder="Catatan untuk item ini (opsional)" rows="2" />
                                </div>
                            </div>
                        </fieldset>
                    </div>
                @endforeach
            @else
                <div class="py-8 text-center text-base-content/60">
                    <p>Belum ada asset item yang ditambahkan</p>
                    <p class="text-sm">Klik tombol "Tambah Asset Item" untuk memulai</p>
                </div>
            @endif
        </div>

        <button type="button" wire:click="addItem" class="mt-3 w-full btn btn-sm btn-outline">
            Tambah Asset Item
        </button>

        @error('items')
            <div class="mt-2 text-xs text-error">
                {{ $message }}
            </div>
        @enderror
    </fieldset>

    <!-- Submit Button -->
    <div class="flex gap-3 justify-end pt-4">
        <x-button label="Batal" class="btn-ghost" wire:click="$dispatch('closeDrawer')" />
        <x-button label="{{ $isEdit ? 'Update' : 'Simpan' }}" class="btn-primary" type="submit" spinner="save" {{-- {{
            count($items) < 1 ? 'disabled' : '' }} --}} />
    </div>
</form>