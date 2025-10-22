<x-info-card title="Pajak Kendaraan Belum Valid" icon="o-shield-exclamation"
    class="border-dashed max-h-72 border-secondary border-2 {{ empty($count) ? 'hidden' : '' }}"
    link="{{ route('vehicle-taxes.index', ['statusFilter' => 'not_valid']) }}">
    <div class="overflow-x-auto">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kendaraan</th>
                    <th>Plat</th>
                    <th>Odometer</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invalidVehicles as $i => $v)
                    @php $vp = $v->vehicleProfile; @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="font-medium">{{ $v->name }}</td>
                        <td>{{ $vp?->plate_no ?? '-' }}</td>
                        <td>{{ $vp?->current_odometer_km ? number_format($vp->current_odometer_km) . ' km' : '-' }}</td>
                        <td>
                            <span>Pajak kendaraan belum di konfigurasi</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</x-info-card>