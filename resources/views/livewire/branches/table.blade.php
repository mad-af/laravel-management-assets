<div class="shadow card bg-base-100">
  <div class="space-y-4 card-body">

    {{-- Tabs Perusahaan --}}
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

    {{-- Header with Search and Action Buttons --}}
    <div class="flex flex-col gap-4 sm:flex-row">
      {{-- Search Input --}}
      <div class="flex-1">
        <x-input wire:model.live="search" placeholder="Cari cabang..." icon="o-magnifying-glass" class="input-sm" />
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
          ['key' => 'name', 'label' => 'Cabang'],
          ['key' => 'address', 'label' => 'Alamat'],
          ['key' => 'is_active', 'label' => 'Status'],
          ['key' => 'created_at', 'label' => 'Dibuat'],
          ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-20'],
        ];
      @endphp
      <x-table :headers="$headers" :rows="$branches" striped show-empty-text>
        @scope('cell_name', $branch)
        <span class="font-medium">{{ $branch->name }}</span>
        @endscope

        @scope('cell_is_active', $branch)
        
        @if($branch->is_active)
          <x-badge value="Aktif" class="badge-success badge-sm" />
        @else
          <x-badge value="Tidak Aktif" class="badge-error badge-sm" />
        @endif
        @endscope

        @scope('cell_created_at', $branch)
        {{ $branch->created_at->format('d M Y') }}
        @endscope

        @scope('cell_actions', $branch)
        <x-action-dropdown :model="$branch">
          <li>
            <button wire:click="openEditDrawer('{{ $branch->id }}')"
              class="flex gap-2 items-center p-2 text-sm rounded"
              onclick="document.getElementById('dropdown-menu-{{ $branch->id }}').hidePopover()">
              <x-icon name="o-pencil" class="w-4 h-4" />
              Edit
            </button>
          </li>
          <li>
            <button wire:click="delete('{{ $branch->id }}')"
              wire:confirm="Are you sure you want to delete this branch?"
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
    @if($branches->count() > 0)
      <div class="flex flex-col gap-4 mt-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="text-sm text-base-content/70">
          Menampilkan {{ $branches->firstItem() }}-{{ $branches->lastItem() }} dari {{ $branches->total() }}
          cabang
        </div>

        {{-- Livewire Pagination --}}
        <div class="mt-4">
          {{ $branches->links() }}
        </div>
      </div>
    @endif
  </div>
</div>