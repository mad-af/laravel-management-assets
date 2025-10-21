<x-info-card title="Transfer Aset Perlu Konfirmasi" icon="o-arrows-right-left">
    @if($count === 0)
        <div class="alert alert-info">
            <x-icon name="o-information-circle" class="w-5 h-5" />
            <span>Tidak ada transfer yang perlu konfirmasi.</span>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No. Transfer</th>
                        <th>Dari Cabang</th>
                        <th>Ke Cabang</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transfers as $i => $t)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="font-medium">{{ $t->transfer_no }}</td>
                            <td>{{ \App\Models\Branch::find($t->from_branch_id)?->name ?? '-' }}</td>
                            <td>{{ \App\Models\Branch::find($t->to_branch_id)?->name ?? '-' }}</td>
                            <td>{{ $t->created_at?->format('d M Y') }}</td>
                            <td>
                                <a href="/admin/asset-transfers" class="btn btn-sm btn-primary">Konfirmasi</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-info-card>