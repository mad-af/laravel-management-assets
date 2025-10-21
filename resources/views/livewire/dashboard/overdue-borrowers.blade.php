<x-info-card title="Peminjam Aset Terlambat" icon="o-clock">
    @if($count === 0)
        <div class="alert alert-success">
            <x-icon name="o-check-circle" class="w-5 h-5" />
            <span>Tidak ada peminjam yang terlambat.</span>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Asset</th>
                        <th>Peminjam</th>
                        <th>Jatuh Tempo</th>
                        <th>Telat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($loans as $i => $loan)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="font-medium">{{ $loan->asset?->name ?? '-' }}</td>
                            <td>{{ $loan->employee?->full_name ?? '-' }}</td>
                            <td>{{ $loan->due_at?->format('d M Y') }}</td>
                            <td>
                                @php $days = $loan->due_at ? now()->diffInDays($loan->due_at, false) * -1 : 0; @endphp
                                <span class="badge badge-error">{{ $days }} hari</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-info-card>