<x-info-card title="Transfer Aset Perlu Konfirmasi" icon="o-arrows-right-left" class="overflow-auto h-72 max-h-72"
    link="{{ route('asset-transfers.index', ['actionFilter' => 'confirmation']) }}">

    @if($count === 0)
        <div class="alert alert-info">
            <x-icon name="o-information-circle" class="w-5 h-5" />
            <span>Tidak ada transfer yang perlu konfirmasi.</span>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No. Transfer</th>
                        <th>Dari Cabang</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transfers as $i => $t)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="font-medium">{{ $t->transfer_no }}</td>
                            <td>{{ $t->fromBranch?->name ?? '-' }}</td>
                            <td>{{ $t->created_at?->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-info-card>