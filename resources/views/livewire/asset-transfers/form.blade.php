<form wire:submit="save" class="space-y-4">

    <!-- Reason -->
    <x-textarea label="Alasan Transfer" wire:model="reason" placeholder="Masukkan alasan transfer aset" rows="3" required />

    <!-- Status -->
    <x-select label="Status" wire:model="status" :options="$statusOptions" option-value="value" option-label="label" placeholder="Pilih status" required />

    <!-- Scheduled Date -->
    <x-input label="Dijadwalkan" wire:model="scheduled_at" type="datetime-local" />

    <!-- Locations - Side by Side -->
    <div class="grid grid-cols-2 gap-4">
        <x-select label="Dari Lokasi" class="select-sm"  wire:model="from_location_id" :options="$locations" option-value="id" option-label="name" placeholder="Pilih lokasi asal" required />
        <x-select label="Ke Lokasi" class="select-sm" wire:model="to_location_id" :options="$locations" option-value="id" option-label="name" placeholder="Pilih lokasi tujuan" required />
    </div>

    <x-select label="Inline label" wire:model="selectedUser" icon="o-user"  inline />


    <!-- Asset Items Section -->
    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Asset Items</h3>
            <span class="text-sm text-gray-500">Minimal 1 item</span>
        </div>
        
        <!-- Add New Item -->
        <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
            <div class="flex gap-3 items-end">
                <div class="flex-1">
                    <x-select wire:model="newItem.asset_id" :options="$assets" option-value="id" option-label="display_name" placeholder="Pilih aset" class="bg-white" />
                </div>
                <div class="flex-1">
                    <x-input wire:model="newItem.notes" placeholder="Catatan (opsional)" class="bg-white" />
                </div>
                <x-button wire:click="addItem" class="btn-circle btn-primary btn-sm" icon="o-plus" />
            </div>
        </div>

        <!-- Items List -->
        @if(count($items) > 0)
            <div class="space-y-3">
                @foreach($items as $index => $item)
                    <div class="flex gap-3 items-center p-4 bg-gray-50 rounded-lg border">
                        <div class="flex-1">
                            @php
                                $asset = $assets->find($item['asset_id']);
                            @endphp
                            <div class="font-medium text-gray-900">{{ $asset?->name ?? 'Asset not found' }}</div>
                            <div class="text-sm text-gray-500">{{ $asset?->asset_tag ?? '' }}</div>
                        </div>
                        @if(!empty($item['notes']))
                            <div class="flex-1 text-sm text-gray-600">
                                {{ $item['notes'] }}
                            </div>
                        @endif
                        <x-button 
                            wire:click="removeItem({{ $index }})" 
                            class="btn-circle btn-error btn-outline btn-sm" 
                            icon="o-trash" 
                            {{ count($items) <= 1 ? 'disabled' : '' }}
                        />
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-6 text-center bg-gray-50 rounded-lg border border-gray-300 border-dashed">
                <p class="text-gray-500">Belum ada item yang ditambahkan</p>
                <p class="mt-1 text-sm text-gray-400">Minimal 1 item harus ditambahkan</p>
            </div>
        @endif
    </div>

    <!-- Notes -->
    <x-textarea label="Catatan" wire:model="notes" placeholder="Catatan tambahan (opsional)" rows="2" />

    <!-- Submit Button -->
    <div class="flex gap-3 justify-end pt-6 border-t">
        <x-button label="Batal" class="btn-ghost" wire:click="$dispatch('closeDrawer')" />
        <x-button 
            label="{{ $isEdit ? 'Update' : 'Simpan' }}" 
            class="btn-primary" 
            type="submit" 
            spinner="save" 
            {{ count($items) < 1 ? 'disabled' : '' }}
        />
    </div>
</form>