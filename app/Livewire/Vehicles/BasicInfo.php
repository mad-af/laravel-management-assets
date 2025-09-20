<?php

namespace App\Livewire\Vehicles;

use App\Models\Asset;
use Livewire\Component;

class BasicInfo extends Component
{
    public Asset $vehicle;

    public function mount(Asset $vehicle)
    {
        $this->vehicle = $vehicle;
    }

    public function getStatusBadgeClass($status)
    {
        $statusColors = [
            'active' => 'badge-success',
            'damaged' => 'badge-error',
            'lost' => 'badge-error',
            'maintenance' => 'badge-warning',
            'checked_out' => 'badge-info',
        ];
        
        return $statusColors[$status] ?? 'badge-neutral';
    }

    public function getConditionBadgeClass($condition)
    {
        $conditionColors = [
            'excellent' => 'badge-success',
            'good' => 'badge-success',
            'fair' => 'badge-warning',
            'poor' => 'badge-error',
            'damaged' => 'badge-error',
        ];
        
        return $conditionColors[$condition] ?? 'badge-neutral';
    }

    public function render()
    {
        return view('livewire.vehicles.basic-info');
    }
}