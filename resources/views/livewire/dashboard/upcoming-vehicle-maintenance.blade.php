<x-info-card title="Perawatan Kendaraan" icon="o-wrench-screwdriver" class="overflow-auto h-72 max-h-72"
    link="{{ route('vehicles.index') }}">
    @if($count === 0)
        <div class="alert alert-info">
            <x-icon name="o-information-circle" class="w-5 h-5" />
            <span>Tidak ada jadwal atau target odometer yang tercatat.</span>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kendaraan</th>
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
                            <td>
                                @php
                                    $odometerInfo = $this->formatOdometerTargetInfo(
                                        $vp?->current_odometer_km,
                                        $vp?->service_target_odometer_km
                                    );
                                @endphp
                                @if($odometerInfo)
                                    <div class="{{ $odometerInfo['is_overdue'] ? 'text-error' : 'text-base-content/60' }}">
                                        {{ $odometerInfo['distance_info'] }}
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @php
                                    $serviceInfo = $vp ? $this->formatNextServiceDate($vp->next_service_date) : null;
                                @endphp
                                @if($serviceInfo)
                                    <div class="{{ $serviceInfo['is_overdue'] ? 'text-error' : 'text-base-content/60' }}">
                                        {{ $serviceInfo['time_info'] }}
                                    </div>
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