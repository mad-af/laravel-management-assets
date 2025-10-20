<x-info-grid title="Informasi Profil Kendaraan" icon="o-truck" :items="[
        [
            'label' => 'Merek',
            'value' => $vehicle->vehicleProfile?->brand ?? '-'
        ],
        [
            'label' => 'Model',
            'value' => $vehicle->vehicleProfile?->model ?? '-'
        ],
        [
            'label' => 'Nomor Plat',
            'value' => $vehicle->vehicleProfile?->plate_no ?? '-',
            'mono' => true
        ],
        [
            'label' => 'VIN',
            'value' => $vehicle->vehicleProfile?->vin ?? '-',
            'mono' => true
        ],
        [
            'label' => 'Pemilik',
            'value' => $vehicle->vehicleProfile?->owner ?? '-'
        ],
        [
            'label' => 'Tipe Kendaraan',
            'value' => $vehicle->vehicleProfile?->type?->label() ?? '-',
            'badge' => true,
            'badge_class' =>  'badge-'.$vehicle->status->color().' badge-soft',
        ],
        [
            'label' => 'Tahun Pembelian',
            'value' => $vehicle->vehicleProfile?->year_purchase ?? '-'
        ],
        [
            'label' => 'Tahun Produksi',
            'value' => $vehicle->vehicleProfile?->year_manufacture ?? '-'
        ],
        [
            'label' => 'Odometer Saat Ini (KM)',
            'value' => $vehicle->vehicleProfile?->current_odometer_km ? number_format($vehicle->vehicleProfile->current_odometer_km) . ' km' : '-',
            'class' => 'font-medium'
        ],
        [
            'label' => 'Target Odometer Servis (KM)',
            'value' => $vehicle->vehicleProfile?->service_target_odometer_km ? number_format($vehicle->vehicleProfile->service_target_odometer_km) . ' km' : '-'
        ],
        [
            'label' => 'Tanggal Servis Terakhir',
            'value' => $vehicle->vehicleProfile?->last_service_date?->format('d M Y') ?? '-'
        ],
        [
            'label' => 'Tanggal Servis Berikutnya',
            'value' => $vehicle->vehicleProfile?->next_service_date?->format('d M Y') ?? '-'
        ],
    ]" />
