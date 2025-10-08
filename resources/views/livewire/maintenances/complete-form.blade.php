<form wire:submit="save" class="space-y-2">
    
    <!-- Invoice No -->
    <div>
        <x-input label="No. Invoice" wire:model="invoice_no" placeholder="Masukkan nomor invoice..." class="input-sm" />
    </div>
    
    <!-- Cost -->
    <div>
        <x-input label="Biaya (Rp)" prefix="Rp" wire:model="cost" placeholder="Masukkan biaya perawatan" type="number"
            step="0.01" min="0" class="input-sm" required />
    </div>

    @if($this->isVehicle)
        <!-- Odometer KM at Service -->
        <div>
            <x-input label="Odometer saat Service (KM)" wire:model="odometer_km_at_service"
                placeholder="Masukkan odometer..." type="number"
                min="{{ $this->asset?->vehicleProfile?->current_odometer_km ?? 0 }}" class="input-sm" required />
        </div>

        <!-- Next Service Target Odometer KM -->
        <div>
            <x-input label="Target Odometer Service Berikutnya (KM)" wire:model="next_service_target_odometer_km"
                placeholder="Masukkan target odometer..." type="number" min="{{ $this->asset?->vehicleProfile?->current_odometer_km ?? 0 }}" class="input-sm" />
        </div>
    @endif
    
    <!-- Next Service Date -->
    <div>
        <x-input label="Tanggal Service Berikutnya" wire:model="next_service_date" type="date" class="input-sm" />
    </div>

    <!-- Notes -->
    <div>
        <x-textarea label="Catatan" wire:model="notes" placeholder="Catatan tambahan..." rows="2" class="textarea-sm" />
    </div>

        <!-- Submit Button -->
    <div class="flex gap-2 justify-end pt-4">
        <x-button label="Batal" class="btn-ghost btn-sm" wire:click="$dispatch('close-completed-drawer')" />
        <x-button label="Selesaikan" class="btn-success btn-sm" type="submit" spinner="save" />
    </div>

</form>