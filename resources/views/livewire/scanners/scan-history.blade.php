<x-info-card icon="o-clock" title="Riwayat Scan Terakhir">
    @php
        $headers = [
            ['key' => 'time', 'label' => 'Waktu'],
            ['key' => 'tag', 'label' => 'Tag'],
            ['key' => 'asset_name', 'label' => 'Nama Asset'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'action', 'label' => 'Aksi'],
        ];

    @endphp
    <x-table :headers="$headers" :rows="[]" show-empty-text>
        @scope('cell_time', $scan)
        {{ $scan->time->format('d M Y H:i:s') }}
        @endscope

        @scope('cell_tag', $scan)
        <span class="font-mono">{{ $scan->tag }}</span>
        @endscope

        @scope('cell_action', $scan)
        <x-button icon="o-eye" class="btn-ghost btn-xs" />
        @endscope
    </x-table>
</x-info-card>
