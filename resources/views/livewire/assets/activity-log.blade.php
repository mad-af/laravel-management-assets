<x-info-card title="Aktivitas Asset" icon="o-document-text">
    <div class="mb-4">
        <x-button wire:click="toggleShowAll" class="btn-sm">
            {{ $showAll ? 'Tampilkan 10 Terakhir' : 'Tampilkan Semua' }}
        </x-button>
    </div>

    @if($logs->isEmpty())
        <div class="py-8 text-center">
            <p class="text-base-content/50">Belum ada aktivitas untuk asset ini</p>
        </div>
    @else
        @php
            $headers = [
                ['key' => 'action', 'label' => 'Aksi'],
                ['key' => 'notes', 'label' => 'Catatan'],
                ['key' => 'user', 'label' => 'User'],
                ['key' => 'created_at', 'label' => 'Waktu'],
            ];
        @endphp

        <x-table :headers="$headers" :rows="$logs" wire:model="expanded" class="table-sm" striped expandable>
            @scope('cell_action', $log)
            <x-badge :value="$log->action->label()" :class="'whitespace-nowrap badge-sm badge-' . $log->action->color()" />
            @endscope

            @scope('cell_notes', $log)
            <div class="text-sm">
                @if($log->notes)
                    {{ $log->notes }}
                @else
                    <span class="text-base-content/50">-</span>
                @endif

                @if($log->changed_fields && count($log->changed_fields) > 0)
                    <div class="mt-1">
                        <span class="text-xs text-base-content/60">
                            Perubahan: {{ implode(', ', array_keys($log->changed_fields)) }}
                        </span>
                    </div>
                @endif
            </div>
            @endscope

            @scope('cell_user', $log)
            <div class="tooltip">
                <div class="text-xs tooltip-content">
                    <div class="font-medium">{{ $log->user->name }}</div>
                    <div class="text-xs">{{ $log->user->email }}</div>
                </div>
                <x-avatar placeholder="{{ strtoupper(substr($log->user->name, 0, 2)) }}"
                    class="!w-9 !bg-primary !font-bold border-2 border-base-100" />
            </div>
            @endscope

            @scope('cell_created_at', $log)
            <div class="text-sm">
                <div class="font-medium">{{ $log->created_at->format('d M Y') }}</div>
                <div class="text-xs text-base-content/60">{{ $log->created_at->format('H:i') }}</div>
            </div>
            @endscope

            @scope('expansion', $log)
            @if($log->changed_fields && count($log->changed_fields) > 0)
                <div class="flex gap-1 w-full">
                    <div class="space-y-1 w-1/2">
                        <h3 class="font-bold">Sebelum</h3>
                        <div class="p-2 rounded border border-base-300/80 bg-base-200/60 text-base-content/80">
                            <span class="block">{</span>
                            @foreach($log->changed_fields as $field => $value)
                                <div class="flex ml-4">
                                    <div>"{{ $field }}"</div>
                                    <span class="mx-1"> : </span>
                                    <div>
                                        <span class="text-error-content bg-error/80">"{{ $value['old'] }}"</span>
                                    </div>
                                </div>
                            @endforeach
                            <span class="block">}</span>
                        </div>
                    </div>
                    <div class="space-y-1 w-1/2">
                        <h3 class="font-bold">Sesudah</h3>
                        <div class="p-2 rounded border border-base-300/80 bg-base-200/60 text-base-content/80">
                            <span class="block">{</span>
                            @foreach($log->changed_fields as $field => $value)
                                <div class="flex ml-4">
                                    <span>"{{ $field }}"</span>
                                    <span class="mx-1"> : </span>
                                    <div>
                                        <span class="text-success-content bg-success/80">"{{ $value['new'] }}"</span>
                                    </div>
                                </div>
                            @endforeach
                            <span class="block">}</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="p-4 text-sm text-center text-base-content/50">
                    Tidak ada detail perubahan untuk aktivitas ini
                </div>
            @endif
            @endscope
        </x-table>

        @if($showAll && method_exists($logs, 'links'))
            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        @endif
    @endif
</x-info-card>