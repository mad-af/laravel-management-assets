<div class="shadow card bg-base-100">
    <div class="card-body">
        {{-- Header with Search and Action Buttons --}}
        <div class="flex flex-col gap-4 mb-4 sm:flex-row">
            {{-- Search Input --}}
            <div class="flex-1">
                <x-input wire:model.live="search" placeholder="Cari log (asset, user, notes)..."
                    icon="o-magnifying-glass" class="input-sm" />
            </div>

            {{-- Filter Dropdowns --}}
            <div class="flex gap-2">
                <x-dropdown>
                    <x-slot:trigger>
                        <x-button icon="o-cube" class="btn-sm">
                            Filter Asset
                        </x-button>
                    </x-slot:trigger>

                    <x-menu-item title="Semua Asset" wire:click="$set('assetFilter', '')" />
                    @foreach($assets as $asset)
                        <x-menu-item title="{{ $asset->name }} ({{ $asset->code }})"
                            wire:click="$set('assetFilter', '{{ $asset->id }}')" />
                    @endforeach
                </x-dropdown>

                <x-dropdown>
                    <x-slot:trigger>
                        <x-button icon="o-bolt" class="btn-sm">
                            Filter Aksi
                        </x-button>
                    </x-slot:trigger>

                    <x-menu-item title="Semua Aksi" wire:click="$set('actionFilter', '')" />
                    @foreach($actions as $action)
                        <x-menu-item title="{{ $action->label() }}"
                            wire:click="$set('actionFilter', '{{ $action->value }}')" />
                    @endforeach
                </x-dropdown>

                <x-dropdown>
                    <x-slot:trigger>
                        <x-button icon="o-user" class="btn-sm">
                            Filter User
                        </x-button>
                    </x-slot:trigger>

                    <x-menu-item title="Semua User" wire:click="$set('userFilter', '')" />
                    @foreach($users as $user)
                        <x-menu-item title="{{ $user->name }}" wire:click="$set('userFilter', '{{ $user->id }}')" />
                    @endforeach
                </x-dropdown>

                <x-button wire:click="exportLogs" icon="o-arrow-down-tray" class="btn-sm">
                    Export CSV
                </x-button>
            </div>
        </div>

        {{-- Date Range Filters --}}
        <div class="flex gap-4 mb-4">
            <div class="flex-1">
                <x-input wire:model.live="dateFromFilter" type="date" label="Dari Tanggal" class="input-sm" />
            </div>
            <div class="flex-1">
                <x-input wire:model.live="dateToFilter" type="date" label="Sampai Tanggal" class="input-sm" />
            </div>
        </div>

        {{-- Table --}}
        <div>
            @php
                $headers = [
                    ['key' => 'asset', 'label' => 'Asset', 'class' => 'w-48'],
                    ['key' => 'action', 'label' => 'Aksi', 'class' => 'w-32'],
                    ['key' => 'user', 'label' => 'User', 'class' => 'w-36'],
                    ['key' => 'changed_fields', 'label' => 'Perubahan'],
                    ['key' => 'notes', 'label' => 'Catatan'],
                    ['key' => 'created_at', 'label' => 'Waktu', 'class' => 'w-40'],
                ];
            @endphp
            <x-table :headers="$headers" :rows="$logs" striped show-empty-text>
                @scope('cell_asset', $log)
                <div class="flex flex-col">
                    @if($log->asset)
                        <span class="font-medium">{{ $log->asset->name }}</span>
                        <span class="text-xs text-base-content/60">{{ $log->asset->code }}</span>
                    @else
                        <span class="text-base-content/50">Asset dihapus</span>
                    @endif
                </div>
                @endscope

                @scope('cell_action', $log)
                <x-badge value="{{ $log->action->label() }}" class="{{ $log->action_badge_color }} badge-sm" />
                @endscope

                @scope('cell_user', $log)
                @if($log->user)
                    <div class="flex gap-2 items-center">
                        <x-avatar initials="{{ substr($log->user->name, 0, 2) }}" size="xs" placeholder="true" />
                        <span class="text-sm">{{ $log->user->name }}</span>
                    </div>
                @else
                    <span class="text-base-content/50">User dihapus</span>
                @endif
                @endscope

                @scope('cell_changed_fields', $log)
                @if($log->changed_fields && is_array($log->changed_fields))
                    <div class="text-xs">
                        @foreach($log->changed_fields as $field => $changes)
                            <div class="mb-1">
                                <span class="font-medium">{{ ucfirst($field) }}:</span>
                                @if(is_array($changes) && isset($changes['old'], $changes['new']))
                                    <span class="text-error">{{ $changes['old'] }}</span>
                                    →
                                    <span class="text-success">{{ $changes['new'] }}</span>
                                @else
                                    {{ is_array($changes) ? json_encode($changes) : $changes }}
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    -
                @endif
                @endscope

                @scope('cell_notes', $log)
                @if($log->notes)
                    <span class="text-sm">{{ Str::limit($log->notes, 50) }}</span>
                @else
                    -
                @endif
                @endscope

                @scope('cell_created_at', $log)
                <div class="flex flex-col text-xs">
                    <span>{{ $log->created_at->format('d M Y') }}</span>
                    <span class="text-base-content/60">{{ $log->created_at->format('H:i:s') }}</span>
                </div>
                @endscope
            </x-table>
        </div>

        {{-- Pagination Info --}}
        @if($logs->count() > 0)
            <div class="flex flex-col gap-4 mt-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="text-sm text-base-content/70">
                    Menampilkan {{ $logs->firstItem() }}-{{ $logs->lastItem() }} dari {{ $logs->total() }}
                    log aktivitas
                </div>

                {{-- Livewire Pagination --}}
                <div class="mt-4">
                    {{ $assets->links(view: 'components.pagination.simple') }}
                </div>
            </div>
        @endif
    </div>
</div>