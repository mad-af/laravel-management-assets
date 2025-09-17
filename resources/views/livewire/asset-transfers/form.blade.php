<form wire:submit="save" class="space-y-4">
    <!-- Transfer Number -->
    <div>
        <x-input label="Transfer No" wire:model="transfer_no" placeholder="Transfer number" required readonly />
    </div>

    <!-- Reason -->
    <div>
        <x-textarea label="Reason" wire:model="reason" placeholder="Alasan transfer aset" rows="3" />
    </div>

    <!-- From Location -->
    <div>
        <x-select label="From Location" wire:model="from_location_id" placeholder="Pilih lokasi asal" :options="$locations" option-value="id" option-label="name" />
    </div>

    <!-- To Location -->
    <div>
        <x-select label="To Location" wire:model="to_location_id" placeholder="Pilih lokasi tujuan" :options="$locations" option-value="id" option-label="name" />
    </div>

    <!-- Status -->
    <div>
        @php
            $statusOptions = collect(\App\Enums\AssetTransferStatus::cases())
                ->map(fn($status) => ['value' => $status->value, 'label' => $status->label()])
                ->toArray();
        @endphp
        <x-select label="Status" wire:model="status" :options="$statusOptions" option-value="value" option-label="label" />
    </div>

    <!-- Scheduled At -->
    <div>
        <x-input label="Scheduled At" wire:model="scheduled_at" type="datetime-local" />
    </div>

    <!-- Asset Transfer Items -->
    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold">Asset Items</h3>
            <x-button label="Add Item" class="btn-sm btn-outline" wire:click="addItem" />
        </div>
        
        @foreach($items as $index => $item)
            <div class="p-4 border rounded-lg space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium">Item {{ $index + 1 }}</span>
                    @if(count($items) > 1)
                        <x-button label="Remove" class="btn-xs btn-error" wire:click="removeItem({{ $index }})" />
                    @endif
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Asset -->
                    <div>
                        <x-select 
                            label="Asset" 
                            wire:model="items.{{ $index }}.asset_id" 
                            :options="$assets" 
                            option-value="id" 
                            option-label="name" 
                            placeholder="Pilih asset" 
                            required 
                        />
                    </div>
                    
                    <!-- From Location -->
                    <div>
                        <x-select 
                            label="From Location" 
                            wire:model="items.{{ $index }}.from_location_id" 
                            :options="$locations" 
                            option-value="id" 
                            option-label="name" 
                            placeholder="Pilih lokasi asal" 
                        />
                    </div>
                    
                    <!-- To Location -->
                    <div>
                        <x-select 
                            label="To Location" 
                            wire:model="items.{{ $index }}.to_location_id" 
                            :options="$locations" 
                            option-value="id" 
                            option-label="name" 
                            placeholder="Pilih lokasi tujuan" 
                        />
                    </div>
                    
                    <!-- Status -->
                    <div>
                        @php
                            $itemStatusOptions = collect(\App\Enums\AssetTransferItemStatus::cases())
                                ->map(fn($status) => ['value' => $status->value, 'label' => $status->label()])
                                ->toArray();
                        @endphp
                        <x-select 
                            label="Status" 
                            wire:model="items.{{ $index }}.status" 
                            :options="$itemStatusOptions" 
                            option-value="value" 
                            option-label="label" 
                        />
                    </div>
                </div>
                
                <!-- Notes -->
                <div>
                    <x-textarea 
                        label="Notes" 
                        wire:model="items.{{ $index }}.notes" 
                        placeholder="Catatan untuk item ini" 
                        rows="2" 
                    />
                </div>
            </div>
        @endforeach
    </div>

    <!-- Submit Button -->
    <div class="flex gap-2 justify-end pt-4">
        <x-button label="Batal" class="btn-ghost" wire:click="$dispatch('close-drawer')" />
        <x-button label="{{ $isEdit ? 'Update' : 'Simpan' }}" class="btn-primary" type="submit" spinner="save" />
    </div>
</form>