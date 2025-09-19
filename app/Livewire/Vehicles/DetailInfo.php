<?php

namespace App\Livewire\AssetTransfers;

use App\Models\AssetTransfer;
use Livewire\Component;

class DetailInfo extends Component
{
    public array $transferData;

    public function mount(array $transferData)
    {
        $this->transferData = $transferData;
    }

    public function getPriorityBadgeClass($priority)
    {
        return match($priority->value) {
            'low' => 'badge-success',
            'medium' => 'badge-warning', 
            'high' => 'badge-error',
            default => 'badge-ghost'
        };
    }

    public function getStatusBadgeClass($status)
    {
        return match($status->value) {
            'draft' => 'badge-ghost',
            'pending' => 'badge-warning',
            'approved' => 'badge-info',
            'in_progress' => 'badge-primary',
            'completed' => 'badge-success',
            'cancelled' => 'badge-error',
            default => 'badge-ghost'
        };
    }

    public function render()
    {
        return view('livewire.asset-transfers.detail-info');
    }
}