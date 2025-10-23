<div class="shadow card bg-base-100">
    <div class="card-body">
        <!-- Header with Search and Action Buttons -->
        <div class="flex flex-col gap-4 mb-4 sm:flex-row">
            <!-- Search Input -->
            <div class="flex-1">
                <x-input wire:model.live="search" placeholder="Cari provider..." icon="o-magnifying-glass"
                    class="input-sm" />
            </div>

            <div class="flex gap-2">
                <x-button icon="o-plus" class="btn-sm" wire:click="openDrawer">Tambah</x-button>
            </div>
        </div>

        <!-- Table -->
        <div>
            @php
                $headers = [
                    ['key' => 'name', 'label' => 'Provider'],
                    ['key' => 'phone', 'label' => 'Telepon'],
                    ['key' => 'email', 'label' => 'Email'],
                    ['key' => 'created_at', 'label' => 'Dibuat'],
                    ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-20'],
                ];
            @endphp
            <x-table :headers="$headers" :rows="$insurances" striped show-empty-text>
                @scope('cell_name', $insurance)
                <span class="font-medium">{{ $insurance->name }}</span>
                @endscope

                @scope('cell_phone', $insurance)
                {{ $insurance->phone ?? '-' }}
                @endscope

                @scope('cell_email', $insurance)
                {{ $insurance->email ?? '-' }}
                @endscope

                @scope('cell_created_at', $insurance)
                {{ $insurance->created_at->format('d M Y') }}
                @endscope

                @scope('cell_actions', $insurance)
                <x-action-dropdown :model="$insurance">
                    <li>
                        <button wire:click="openEditDrawer('{{ $insurance->id }}')"
                            class="flex gap-2 items-center p-2 text-sm rounded"
                            onclick="document.getElementById('dropdown-menu-{{ $insurance->id }}').hidePopover()">
                            <x-icon name="o-pencil" class="w-4 h-4" />
                            Edit
                        </button>
                    </li>
                    <li>
                        <button wire:click="delete('{{ $insurance->id }}')"
                            wire:confirm="Are you sure you want to delete this provider?"
                            class="flex gap-2 items-center p-2 text-sm rounded text-error">
                            <x-icon name="o-trash" class="w-4 h-4" />
                            Delete
                        </button>
                    </li>
                </x-action-dropdown>
                @endscope
            </x-table>
        </div>

        <!-- Pagination Info -->
        @if($insurances->count() > 0)
            <div class="flex flex-col gap-4 mt-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="text-sm text-base-content/70">
                    Menampilkan {{ $insurances->firstItem() }}-{{ $insurances->lastItem() }} dari {{ $insurances->total() }} provider
                </div>

                <div class="mt-4">
                    {{ $insurances->links() }}
                </div>
            </div>
        @endif
    </div>
</div>