<x-info-card icon="o-clock" title="Riwayat Scan Terakhir">
    @php
        $headers = [
            ['key' => 'time', 'label' => 'Waktu'],
            ['key' => 'tag', 'label' => 'Tag'],
            ['key' => 'asset_name', 'label' => 'Nama Asset'],
            ['key' => 'category', 'label' => 'Kategori'],
            ['key' => 'location', 'label' => 'Lokasi'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'action', 'label' => 'Aksi'],
        ];

    @endphp
    <x-table :headers="$headers" :rows="$rows" show-empty-text>

        @scope('cell_tag', $scan)
        <span class="font-mono">{{ $scan['tag'] }}</span>
        @endscope

        @scope('cell_action', $scan)
        <x-button icon="o-eye" class="btn-ghost btn-xs" :disabled="empty($scan['id'])" />
        @endscope
    </x-table>
</x-info-card>
