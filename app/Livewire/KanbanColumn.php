<?php

namespace App\Livewire;

use App\Enums\MaintenanceStatus;
use Illuminate\Support\Collection;
use Livewire\Component;

class KanbanColumn extends Component
{
    public MaintenanceStatus $status;
    public Collection $maintenances;
    public string $title;
    public string $colorClass;

    public function mount(MaintenanceStatus $status, Collection $maintenances)
    {
        $this->status = $status;
        $this->maintenances = $maintenances;
        $this->title = $status->label();
        $this->colorClass = $this->getColumnColorClass();
    }

    private function getColumnColorClass(): string
    {
        return match($this->status) {
            MaintenanceStatus::OPEN => 'border-info/30 bg-info/5',
            MaintenanceStatus::SCHEDULED => 'border-warning/30 bg-warning/5',
            MaintenanceStatus::IN_PROGRESS => 'border-primary/30 bg-primary/5',
            MaintenanceStatus::COMPLETED => 'border-success/30 bg-success/5',
            MaintenanceStatus::CANCELLED => 'border-error/30 bg-error/5',
        };
    }

    private function getBadgeColorClass(): string
    {
        return match($this->status) {
            MaintenanceStatus::OPEN => 'bg-info text-info-content',
            MaintenanceStatus::SCHEDULED => 'bg-warning text-warning-content',
            MaintenanceStatus::IN_PROGRESS => 'bg-primary text-primary-content',
            MaintenanceStatus::COMPLETED => 'bg-success text-success-content',
            MaintenanceStatus::CANCELLED => 'bg-error text-error-content',
        };
    }

    public function render()
    {
        return view('livewire.kanban-column');
    }
}
