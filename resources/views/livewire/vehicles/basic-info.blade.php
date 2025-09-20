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
            'label' => 'Location',
            'value' => $vehicle->location->name
        ],
        [
            'label' => 'Status',
            'value' => ucfirst($vehicle->status->value),
            'badge' => true,
            'badge_class' => $this->getStatusBadgeClass($vehicle->status->value)
        ],
        [
            'label' => 'Condition',
            'value' => ucfirst($vehicle->condition->value ?? 'Unknown'),
            'badge' => true,
            'badge_class' => $this->getConditionBadgeClass($vehicle->condition->value ?? 'unknown')
        ]
    ]"
    :description="$vehicle->description"
/>