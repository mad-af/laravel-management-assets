<x-info-grid title="Vehicle Profile Information" icon="o-truck" :items="[
        [
            'label' => 'Brand',
            'value' => $vehicle->vehicleProfile?->brand ?? '-'
        ],
        [
            'label' => 'Model',
            'value' => $vehicle->vehicleProfile?->model ?? '-'
        ],
        [
            'label' => 'Plate Number',
            'value' => $vehicle->vehicleProfile?->plate_no ?? '-',
            'mono' => true
        ],
        [
            'label' => 'VIN',
            'value' => $vehicle->vehicleProfile?->vin ?? '-',
            'mono' => true
        ],
        [
            'label' => 'Year Purchase',
            'value' => $vehicle->vehicleProfile?->year_purchase ?? '-'
        ],
        [
            'label' => 'Year Manufacture',
            'value' => $vehicle->vehicleProfile?->year_manufacture ?? '-'
        ],
        [
            'label' => 'Current Odometer (KM)',
            'value' => $vehicle->vehicleProfile?->current_odometer_km ? number_format($vehicle->vehicleProfile->current_odometer_km) . ' km' : '-',
            'class' => 'font-medium'
        ],
        [
            'label' => 'Service Interval (KM)',
            'value' => $vehicle->vehicleProfile?->service_interval_km ? number_format($vehicle->vehicleProfile->service_interval_km) . ' km' : '-'
        ],
        [
            'label' => 'Service Interval (Days)',
            'value' => $vehicle->vehicleProfile?->service_interval_days ? $vehicle->vehicleProfile->service_interval_days . ' days' : '-'
        ],
        [
            'label' => 'Service Target Odometer (KM)',
            'value' => $vehicle->vehicleProfile?->service_target_odometer_km ? number_format($vehicle->vehicleProfile->service_target_odometer_km) . ' km' : '-'
        ],
        [
            'label' => 'Last Service Date',
            'value' => $vehicle->vehicleProfile?->last_service_date?->format('d M Y') ?? '-'
        ],
        [
            'label' => 'Next Service Date',
            'value' => $vehicle->vehicleProfile?->next_service_date?->format('d M Y') ?? '-'
        ],
        [
            'label' => 'Annual Tax Due Date',
            'value' => $vehicle->vehicleProfile?->annual_tax_due_date?->format('d M Y') ?? '-'
        ],
    ]" :longTextItems="[
        [
            'label' => 'Description',
            'value' => $vehicle->description
        ]
    ]" />
