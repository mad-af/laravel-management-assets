@if($this->canComplete)
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

            <!-- Submit Button -->
        <div class="flex gap-2 justify-end pt-4">
            <x-button label="Batal" class="btn-ghost btn-sm" wire:click="$dispatch('close-completed-drawer')" />
            <x-button label="Selesaikan" class="btn-success btn-sm" type="submit" spinner="save" />
        </div>

    </form>
@else
    <div class="p-4 text-center">
        <div class="alert alert-warning">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 stroke-current shrink-0" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            <span>Maintenance ini tidak dapat diselesaikan karena statusnya bukan "In Progress".</span>
        </div>
    </div>
@endif