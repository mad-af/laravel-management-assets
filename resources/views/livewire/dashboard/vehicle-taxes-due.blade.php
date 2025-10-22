<x-info-card title="Pajak Kendaraan" icon="o-receipt-refund" class="overflow-auto h-72 max-h-72"
    link="{{ route('vehicle-taxes.index') }}">
    <form class="mb-2 filter">
        <input class="btn btn-sm btn-soft btn-primary btn-square" type="reset" value="×"
            wire:click="$set('statusFilter', '')" />
        <input class="btn btn-sm btn-soft btn-primary" type="radio" name="tax_status" aria-label="Terlambat"
            value="overdue" wire:model.live="statusFilter" />
        <input class="btn btn-sm btn-soft btn-primary" type="radio" name="tax_status" aria-label="Jatuh Tempo"
            value="due_soon" wire:model.live="statusFilter" />
    </form>

    <div class="overflow-x-auto">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kendaraan</th>
                    <th>Jenis Pajak</th>
                    <th>Jatuh Tempo</th>
                </tr>
            </thead>
            <tbody>
                @if($count === 0)
                    <tr>
                        <td colspan="4">
                            <div class="text-center">
                                {{-- <x-icon name="o-check-circle" class="w-5 h-5" /> --}}
                                <span class="text-base-content/60">Tidak ada pajak terlambat atau yang akan jatuh tempo.</span>
                            </div>
                        </td>
                    </tr>
                @else
                    @foreach($items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->vehicle_name }}</td>
                            <td>{{ $item->tax_type_label }}</td>
                            <td><span class="{{ $item->due_text_class }}">{{ $item->due_date }}</span></td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</x-info-card>