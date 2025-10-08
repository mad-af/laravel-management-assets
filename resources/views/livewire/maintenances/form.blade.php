<form wire:submit="save" class="space-y-2">

    <!-- Asset Selection -->
    <div>
        <x-select label="Aset" wire:model.live="asset_id" :options="$assets" option-value="value" option-label="label"
            placeholder="Pilih aset..." searchable required class="select-sm" />
    </div>

    @if ($asset_id)
    <!-- Invoice No -->
    {{-- <div>
        <x-input label="No. Invoice" wire:model="invoice_no" placeholder="Masukkan nomor invoice..." class="input-sm" />
    </div> --}}

    <!-- Title -->
    <div>
        <x-input label="Judul Perawatan" wire:model="title" placeholder="Masukkan judul perawatan..." class="input-sm"
            required />
    </div>

    <!-- Type -->
    <div>
        <x-select label="Jenis Perawatan" wire:model="type" :options="$maintenanceTypes" option-value="value"
            option-label="label" placeholder="Pilih jenis..." required class="select-sm" />
    </div>

    <!-- Status -->
    <div>
        <x-select label="Status" wire:model="status" :options="$maintenanceStatuses" option-value="value"
            option-label="label" placeholder="Pilih status..." required class="select-sm" disabled />
    </div>

    <!-- Priority -->
    <div>
        <x-select label="Prioritas" wire:model="priority" :options="$maintenancePriorities" option-value="value"
            option-label="label" placeholder="Pilih prioritas..." required class="select-sm" />
    </div>

    <!-- Started At -->
    <div>
        <x-input label="Tanggal Mulai" wire:model="started_at" type="datetime-local" class="input-sm" />
    </div>

    <!-- Estimated Completed At -->
    <div>
        <x-input label="Estimasi Tanggal Selesai" wire:model="estimated_completed_at" type="datetime-local"
            class="input-sm" />
    </div>

    <!-- Completed At -->
    {{-- <div>
        <x-input label="Tanggal Selesai" wire:model="completed_at" type="datetime-local" class="input-sm" />
    </div> --}}

    <!-- Cost -->
    <div>
        <x-input label="Biaya (Rp)" prefix="Rp" wire:model="cost" placeholder="Masukkan biaya perawatan" type="number"
            step="0.01" min="0" class="input-sm" required />
    </div>

    <!-- Technician Name -->
    <div>
        <x-input label="Nama Teknisi" wire:model="technician_name" placeholder="Masukkan nama teknisi..."
            class="input-sm" />
    </div>



    <!-- Employee Selection -->
    <div>
        <x-choices label="PIC (Karyawan)" wire:model="employee_id" :options="$employees" option-value="id"
            placeholder="Cari karyawan..." search-function="searchEmployees" debounce="500ms"
            no-result-text="Tidak ada karyawan ditemukan" class="input-sm" searchable clearable single>
            {{-- Item slot --}}
            @scope('item', $employee)
            <x-list-item :item="$employee" value="employee_number" sub-value="full_name">
                <x-slot:value>
                    <span class="text-xs">{{ $employee['full_name'] }}</span>
                </x-slot:value>
                <x-slot:sub-value>
                    <span class="text-sm">{{ $employee['employee_number'] }}</span>
                </x-slot:sub-value>
            </x-list-item>
            @endscope

            {{-- Selection slot--}}
            @scope('selection', $employee)
            {{ $employee['full_name'] }} ({{ $employee['employee_number'] }})
            @endscope
        </x-choices>
    </div>

    <!-- Vendor Name -->
    <div>
        <x-input label="Nama Vendor" wire:model="vendor_name" placeholder="Masukkan nama vendor..." class="input-sm" />
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
        <x-button label="Batal" class="btn-ghost btn-sm" wire:click="$dispatch('close-drawer')" />
        <x-button label="{{ $isEdit ? 'Update' : 'Simpan' }}" class="btn-primary btn-sm" type="submit" spinner="save" />
    </div>
    @endif
</form>