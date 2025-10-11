<x-info-card title="Aksi Cepat" icon="o-bolt">
    <div class="space-y-2">
        <button wire:click="addOdometerLog" class="w-full btn  btn-sm btn-primary">
            <x-icon name="o-calculator" class="w-4 h-4" />
            Tambah Odometer
        </button>

        <button wire:click="editProfile" class="w-full btn  btn-sm">
            <x-icon name="o-truck" class="w-4 h-4" />
            Simpan Profil Kendaraan
        </button>
    </div>
</x-info-card>