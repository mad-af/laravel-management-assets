<div class="h-full">
    <!-- Info Banner -->
    <div class="flex gap-3 items-center px-5 py-1 mb-2 rounded-md border bg-base-100 border-info">
        <div class="flex justify-center items-center w-8 h-8 rounded-full bg-info/20">
            <x-icon name="o-information-circle" class="w-5 h-5 text-info" />
        </div>
        <div>
            <p class="text-xs font-medium">Data Completed dan Cancelled hanya menampilkan 1 bulan terakhir</p>
            <p class="text-xs text-base-content/70">Jika ada keperluan silakan <strong>hubungi admin</strong>.</p>
        </div>
    </div>
    <!-- Kanban Board Container -->
    <div class="flex overflow-x-auto gap-3 h-full">
        @foreach($statusColumns as $column)
            <div class="flex flex-col flex-shrink-0 w-64 min-h-0 rounded-lg border min-w-64 border-base-300"
                wire:key="column-{{ $column['status']->value }}">
                <livewire:maintenances.kanban-column :status="$column['status']" :key="'column-' . $column['status']->value"
                    :is-last="$loop->last" />
            </div>
        @endforeach
    </div>
</div>