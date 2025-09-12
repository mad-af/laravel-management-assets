<?php

namespace App\Livewire;

use App\Models\AssetMaintenance;
use Livewire\Component;

class MaintenanceCard extends Component
{
    public AssetMaintenance $maintenance;

    public function mount(AssetMaintenance $maintenance)
    {
        $this->maintenance = $maintenance;
    }

    public function getTypeColorProperty()
    {
        return match($this->maintenance->type) {
            \App\Enums\MaintenanceType::PREVENTIVE => 'badge-info',
            \App\Enums\MaintenanceType::CORRECTIVE => 'badge-warning',
        };
    }

    public function render()
    {
        return view('livewire.maintenance-card');
    }
}
