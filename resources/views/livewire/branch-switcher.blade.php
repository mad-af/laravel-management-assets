
<x-select-group :options="$grouped" wire:model.live="selectedUser"
    class="select-sm w-full sm:!w-48 border border-base-300 focus:outline-none focus:border-primary whitespace-nowrap">
    <x-slot:prepend>
        <span
            class="flex gap-2 items-center px-3 !text-xs rounded-l-md border join-item bg-base-200 border-base-300">
            {{-- <x-icon name="o-building-office-2" class="w-4 h-4" /> --}}
            <span class="hidden font-semibold tracking-wide uppercase sm:inline text-base-content/60">cabang
                aktif</span>
            <span class="font-semibold tracking-wide uppercase sm:hidden text-base-content/60">cabang</span>
            <div class="inline tooltip tooltip-bottom"
                data-tip="Filter global untuk memilih cabang aktif. Pilihan ini akan mempengaruhi data yang ditampilkan di seluruh aplikasi.">
                <x-icon name="o-information-circle"
                    class="!w-3 !h-3 transition-colors cursor-help text-base-content/50 hover:text-primary" />
            </div>
        </span>
    </x-slot:prepend>
</x-select-group>
