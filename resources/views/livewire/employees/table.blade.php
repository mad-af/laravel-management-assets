<div class="shadow card bg-base-100">
    <div class="space-y-4 card-body">
        {{-- Tabs Perusahaan --}}
        <div class="overflow-x-auto">
            <div class="gap-1 items-center min-w-max tabs tabs-box tabs-sm w-fit">
                @foreach ($this->companies as $company)
                <div class="lg:tooltip" data-tip="{{ $company->name }} ({{ $company->code }})">
                    <input
                    type="radio"
                    name="company_tabs"
                    class="tab checked:bg-base-100 checked:shadow"
                    aria-label="[{{ $company->code }}] {{ \Illuminate\Support\Str::limit($company->name, 15) }}"
                    wire:model.live="selectedCompanyId"
                    value="{{ $company->id }}"
                    />
                </div>
                @endforeach
            </div>
        </div>

        {{-- Header with Search and Action Buttons --}}
        <div class="flex flex-col gap-4 mb-4 sm:flex-row">
            {{-- Search Input --}}
            <div class="flex-1">
                <x-input wire:model.live="search" placeholder="Cari pegawai (nama, email, phone, nomor)..."
                    icon="o-magnifying-glass" class="input-sm" />
            </div>

            {{-- Filter Dropdown --}}
            <div class="flex gap-2">
                <x-dropdown>
                    <x-slot:trigger>
                        <x-button icon="o-funnel" class="btn-sm">
                            Filter Status
                        </x-button>
                    </x-slot:trigger>

                    <x-menu-item title="Semua Status" wire:click="$set('statusFilter', '')" />
                    <x-menu-item title="Aktif" wire:click="$set('statusFilter', 'active')" />
                    <x-menu-item title="Tidak Aktif" wire:click="$set('statusFilter', 'inactive')" />
                </x-dropdown>
            </div>
        </div>

        {{-- Table --}}
        <div>
            @php
                $headers = [
                    ['key' => 'full_name', 'label' => 'Nama Pegawai'],
                    ['key' => 'employee_number', 'label' => 'Nomor Pegawai'],
                    ['key' => 'contact', 'label' => 'Kontak'],
                    ['key' => 'branch', 'label' => 'Cabang'],
                    ['key' => 'is_active', 'label' => 'Status'],
                    ['key' => 'created_at', 'label' => 'Dibuat'],
                    ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-24'],
                ];
            @endphp

            <x-table :headers="$headers" :rows="$employees" striped show-empty-text>
                {{-- Nama --}}
                @scope('cell_full_name', $employee)
                <span class="font-medium">{{ $employee->full_name }}</span>
                @endscope

                {{-- Nomor Pegawai --}}
                @scope('cell_employee_number', $employee)
                {{ $employee->employee_number ?? '—' }}
                @endscope

                {{-- Kontak (email / phone) --}}
                @scope('cell_contact', $employee)
                <div class="flex flex-col">
                    <span>{{ $employee->email ?? '—' }}</span>
                    <span class="text-xs text-base-content/70">{{ $employee->phone ?? '' }}</span>
                </div>
                @endscope

                {{-- Cabang --}}
                @scope('cell_branch', $employee)
                @if($employee->branch)
                    <x-badge value="{{ $employee->branch->name }}" class="whitespace-nowrap badge-outline badge-sm" />
                @else
                    <span class="text-base-content/50">-</span>
                @endif
                @endscope

                {{-- Status --}}
                @scope('cell_is_active', $employee)
                @if($employee->is_active)
                    <x-badge value="Aktif" class="badge-success badge-sm" />
                @else
                    <x-badge value="Tidak Aktif" class="badge-error badge-sm" />
                @endif
                @endscope

                {{-- Tanggal dibuat --}}
                @scope('cell_created_at', $employee)
                {{ optional($employee->created_at)->format('d M Y') }}
                @endscope

                {{-- Actions --}}
                @scope('cell_actions', $employee)
                <x-action-dropdown :model="$employee" :disabled="!$this->isAdmin" id="dropdown-menu-{{ $employee->id }}">
                    <li>
                        <button wire:click="openEditDrawer('{{ $employee->id }}')"
                            class="flex gap-2 items-center p-2 text-sm rounded"
                            onclick="document.getElementById('dropdown-menu-{{ $employee->id }}')?.hidePopover?.()">
                            <x-icon name="o-pencil" class="w-4 h-4" />
                            Edit
                        </button>
                    </li>
                    <li>
                        <button wire:click="delete('{{ $employee->id }}')"
                            wire:confirm="Are you sure you want to delete this employee?"
                            class="flex gap-2 items-center p-2 text-sm rounded text-error">
                            <x-icon name="o-trash" class="w-4 h-4" />
                            Delete
                        </button>
                    </li>
                </x-action-dropdown>
                @endscope
            </x-table>
        </div>

        {{-- Pagination Info --}}
        @if($employees->count() > 0)
            <div class="flex flex-col gap-4 mt-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="text-sm text-base-content/70">
                    Menampilkan {{ $employees->firstItem() }}–{{ $employees->lastItem() }} dari {{ $employees->total() }}
                    pegawai
                </div>
                <div class="mt-4">
                    {{ $employees->links() }}
                </div>
            </div>
        @endif
    </div>
</div>