<x-info-grid 
    title="Informasi Dasar"
    :items="[
        [
            'label' => 'Nama Kendaraan',
            'value' => $vehicle->name,
            'class' => 'font-medium'
        ],
        [
            'label' => 'Kode Aset',
            'value' => $vehicle->code,
            'mono' => true
        ],
        [
            'label' => 'Kategori',
            'value' => $vehicle->category->name
        ],
        [
            'label' => 'Status',
            'value' => ucfirst($vehicle->status->label()),
            'badge' => true,
            'badge_class' =>  'badge-'.$vehicle->status->color(),
        ],
        [
            'label' => 'Kondisi',
            'value' => ucfirst($vehicle->condition->label() ?? 'Tidak Diketahui'),
            'badge' => true,
            'badge_class' => 'badge-outline badge-'.$vehicle->condition->color(),
        ]
    ]"
    :longTextItems="[
        [
            'label' => 'Deskripsi',
            'value' => $vehicle->description
        ]
    ]"
    :description="$vehicle->description"
/>