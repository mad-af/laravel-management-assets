<x-info-card title="Pajak Kendaraan Belum Valid" icon="o-shield-exclamation">
    @if($count === 0)
        <div class="alert alert-success">
            <x-icon name="o-check-circle" class="w-5 h-5" />
            <span>Semua kendaraan memiliki jenis pajak terdaftar.</span>
        </div>
    @else
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
                                <span class="badge badge-warning">Tidak ada jenis pajak</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-info-card>