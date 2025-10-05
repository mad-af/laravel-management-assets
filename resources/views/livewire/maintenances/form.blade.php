<form wire:submit="save" class="space-y-6">
    {{-- Asset Selection --}}
    <div>
        <x-select 
            label="Aset" 
            wire:model="asset_id" 
            :options="$assets" 
            placeholder="Pilih aset..."
            searchable
            required
        />
    </div>

    {{-- Title --}}
    <div>
        <x-input 
            label="Judul Perawatan" 
            wire:model="title" 
            placeholder="Masukkan judul perawatan..."
            required
        />
    </div>

    {{-- Description --}}
    <div>
        <x-textarea 
            label="Deskripsi" 
            wire:model="description" 
            placeholder="Masukkan deskripsi perawatan..."
            rows="3"
            required
        />
    </div>

    {{-- Type, Status, Priority in a row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <x-select 
                label="Jenis Perawatan" 
                wire:model="type" 
                :options="$maintenanceTypes" 
                placeholder="Pilih jenis..."
                required
            />
        </div>
        
        <div>
            <x-select 
                label="Status" 
                wire:model="status" 
                :options="$maintenanceStatuses" 
                placeholder="Pilih status..."
                required
            />
        </div>
        
        <div>
            <x-select 
                label="Prioritas" 
                wire:model="priority" 
                :options="$maintenancePriorities" 
                placeholder="Pilih prioritas..."
                required
            />
        </div>
    </div>

    {{-- Cost and Assigned To in a row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-input 
                label="Biaya (Rp)" 
                wire:model="cost" 
                type="number"
                step="0.01"
                min="0"
                placeholder="0"
                prefix="Rp"
            />
        </div>
        
        <div>
            <x-select 
                label="Ditugaskan Kepada" 
                wire:model="assigned_to" 
                :options="$users" 
                placeholder="Pilih teknisi..."
                searchable
            />
        </div>
    </div>

    {{-- Dates in a row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-input 
                label="Tanggal Terjadwal" 
                wire:model="scheduled_date" 
                type="date"
            />
        </div>
        
        <div>
            <x-input 
                label="Tanggal Selesai" 
                wire:model="completed_date" 
                type="date"
            />
        </div>
    </div>

    {{-- Notes --}}
    <div>
        <x-textarea 
            label="Catatan" 
            wire:model="notes" 
            placeholder="Catatan tambahan..."
            rows="2"
        />
    </div>

    {{-- Action Buttons --}}
    <div class="flex justify-end space-x-3 pt-4 border-t">
        <x-button 
            label="Batal" 
            wire:click="$dispatch('close-drawer')"
            class="btn-ghost"
        />
        
        <x-button 
            label="{{ $isEdit ? 'Perbarui' : 'Simpan' }}" 
            type="submit" 
            spinner="save"
            class="btn-primary"
        />
    </div>
</form>