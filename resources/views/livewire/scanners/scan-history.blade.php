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
        @if (empty($scan['id']))
        <a href="" class="btn btn-xs btn-ghost" disabled>
        @else
        <a href="" class="btn btn-xs btn-ghost">
        @endif
            <x-icon name="o-eye" class="!w-5 !h-5" />
        </a>
        @endscope
    </x-table>
</x-info-card>
