<x-info-grid 
    title="Basic Information"
    :items="[
        [
            'label' => 'Vehicle Name',
            'value' => $vehicle->name,
            'class' => 'font-medium'
        ],
        [
            'label' => 'Asset Code',
            'value' => $vehicle->code,
            'mono' => true
        ],
        [
            'label' => 'Category',
            'value' => $vehicle->category->name
        ],
        [
            'label' => 'Status',
            'value' => ucfirst($vehicle->status->value),
            'badge' => true,
            'badge_class' =>  'badge-'.$vehicle->status->color(),
        ],
        [
            'label' => 'Condition',
            'value' => ucfirst($vehicle->condition->value ?? 'Unknown'),
            'badge' => true,
            'badge_class' => 'badge-outline badge-'.$vehicle->condition->color(),
        ]
    ]"
    :longTextItems="[
        [
            'label' => 'Description',
            'value' => $vehicle->description
        ]
    ]"
    :description="$vehicle->description"
/>