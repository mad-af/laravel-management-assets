<x-info-card title="Klaim Asuransi Draft" icon="o-document-text"
    class="overflow-y-auto border-dashed max-h-96 border-neutral border-2 {{ empty($count) ? 'hidden' : '' }}"
    link="{{ route('insurance-claims.index', ['statusFilter' => 'draft']) }}">
    <div class="overflow-x-auto">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>#</th>
                    <th>No Klaim</th>
                    <th>Aset</th>
                    <th>Plat</th>
                    <th>Tanggal Kejadian</th>
                    <th>Sumber</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($claims as $i => $claim)
                    @php $vp = $claim->asset?->vehicleProfile; @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="font-medium">{{ $claim->claim_no ?? '-' }}</td>
                        <td class="font-medium">{{ $claim->asset?->name ?? '-' }}</td>
                        <td>{{ $vp?->plate_no ?? '-' }}</td>
                        <td>{{ $claim->incident_date ? \Carbon\Carbon::parse($claim->incident_date)->format('d M Y') : '-' }}</td>
                        <td>{{ $claim->source?->label() ?? '-' }}</td>
                        <td>
                            <x-badge :value="$label = $claim->status?->label()" :class="'badge-' . $claim->status?->color() . ' badge-sm badge-outline badge-soft'" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</x-info-card>
