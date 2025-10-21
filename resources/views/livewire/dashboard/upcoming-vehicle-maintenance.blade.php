<x-info-card title="Perawatan Kendaraan Selanjutnya & Target Odometer" icon="o-wrench-screwdriver">
    @if($count === 0)
        <div class="alert alert-info">
            <x-icon name="o-information-circle" class="w-5 h-5" />
            <span>Tidak ada jadwal atau target odometer yang tercatat.</span>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kendaraan</th>
                        <th>Odometer Saat Ini</th>
                        <th>Target Odometer</th>
                        <th>Selisih Km</th>
                        <th>Jadwal Berikutnya</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vehicles as $i => $asset)
                        @php $vp = $asset->vehicleProfile; @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="font-medium">{{ $asset->name }}</td>
                            <td>{{ number_format($vp->current_odometer_km ?? 0) }} km</td>
                            <td>{{ $vp->service_target_odometer_km ? number_format($vp->service_target_odometer_km) . ' km' : '-' }}</td>
                            <td>
                                @php
                                    $delta = ($vp->service_target_odometer_km && $vp->current_odometer_km)
                                        ? $vp->service_target_odometer_km - $vp->current_odometer_km
                                        : null;
                                @endphp
                                @if($delta !== null)
                                    <span class="badge {{ $delta <= 0 ? 'badge-error' : 'badge-warning' }}">{{ number_format(max(0, $delta)) }} km</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($vp->next_service_date)
                                    <span class="badge badge-info">{{ $vp->next_service_date->format('d M Y') }}</span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-info-card>