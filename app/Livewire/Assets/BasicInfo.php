<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use Livewire\Component;

class BasicInfo extends Component
{
    public Asset $asset;

    public function mount(Asset $asset)
    {
        $this->asset = $asset;
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
        return view('livewire.assets.basic-info');
    }
}