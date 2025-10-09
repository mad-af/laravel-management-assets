<form wire:submit="save" class="space-y-2">

    <!-- Asset Selection -->
    <div>
        <x-select label="Aset" wire:model.live="asset_id" :options="$assets" option-value="value" option-label="label"
            placeholder="Pilih aset..." searchable required class="select-sm" />
    </div>

    <!-- Vehicle Profile Confirmation Dialog -->
    @if ($showVehicleProfileConfirmation)
        <div class="p-4 rounded-lg border border-warning bg-warning/10">
            <div class="flex items-start gap-3">
                <x-icon name="o-exclamation-triangle" class="w-5 h-5 text-warning flex-shrink-0 mt-0.5" />
                <div class="flex-1">
                    <h4 class="font-semibold text-sm text-warning">Profil Kendaraan Diperlukan</h4>
                    <p class="text-sm text-base-content/80 mt-1">
                        Kendaraan yang dipilih belum memiliki profil kendaraan. Profil kendaraan diperlukan untuk membuat maintenance record. 
                        Apakah Anda ingin mengisi profil kendaraan sekarang?
                    </p>
                    <div class="flex gap-2 mt-3">
                        <x-button 
                            label="Ya, Isi Profil" 
                            class="btn-warning btn-sm" 
                            wire:click="confirmCreateVehicleProfile"
                        />
                        <x-button 
                            label="Batal" 
                            class="btn-ghost btn-sm" 
                            wire:click="cancelVehicleProfileCreation"
                        />
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($asset_id)
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
            <x-datetime label="Tanggal Mulai" wire:model="started_at" class="input-sm" required />
        </div>

        <!-- Estimated Completed At -->
        <div>
            <x-datetime label="Estimasi Tanggal Selesai" wire:model="estimated_completed_at"
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
        @endif

        <!-- Service Tasks -->
        <div class="pt-2">
            <fieldset class="p-4 rounded-lg border border-base-300">
                <legend class="px-2 text-xs font-semibold">Tugas Layanan</legend>
                
                <div class="space-y-2 w-full">
                    @if(count($service_tasks) > 0)
                        @foreach($service_tasks as $index => $task)
                            <x-input 
                                wire:model="service_tasks.{{ $index }}" 
                                placeholder="Masukkan tugas layanan..." 
                                class="flex-1 input-sm" 
                            >
                            <x-slot:append>
                                <x-button 
                                    icon="o-trash" 
                                    class="btn-sm btn-square text-error" 
                                    wire:click="removeServiceTask({{ $index }})"
                                    title="Hapus tugas"
                                />
                            </x-slot:append>
                            </x-input>
                        @endforeach
                    @else
                        <p class="text-xs italic text-center text-base-content/60">Belum ada tugas layanan ditambahkan</p>
                    @endif
                    
                    <div class="w-full">
                        <x-button 
                            icon="o-plus" 
                            label="Tambah Tugas" 
                            class="w-full btn-sm" 
                            wire:click="addServiceTask"
                        />
                    </div>
                </div>
            </fieldset>
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