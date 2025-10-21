<x-info-card title="Peminjam Aset Terlambat" icon="o-clock" class="overflow-auto h-72 max-h-72"
    link="{{ route('asset-loans.index', ['statusFilter' => 'overtime']) }}">
    @if($count === 0)
        <div class="alert alert-success">
            <x-icon name="o-check-circle" class="w-5 h-5" />
            <span>Tidak ada peminjam yang terlambat.</span>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Asset</th>
                        <th>Peminjam</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($loans as $i => $loan)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="font-medium">{{ $loan->asset?->name ?? '-' }}</td>
                            <td>
                                <div class="tooltip" data-tip="{{ $loan->employee?->full_name ?? '-' }}">
                                    <x-avatar placeholder="{{ substr($loan->employee?->full_name, 0, 2) }}"
                                        class="bg-primary" />
                                </div>
                            </td>
                            <td>
                                <span class="{{ $loan->color }}">{{ $loan->due_status_text }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-info-card>