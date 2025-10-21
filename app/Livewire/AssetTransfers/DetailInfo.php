<?php

namespace App\Livewire\AssetTransfers;

use Livewire\Component;

class DetailInfo extends Component
{
    public array $transferData;

    public function mount(array $transferData)
    {
        $this->transferData = $transferData;
    }

    public function getStatusBadgeClass($status)
    {
        return match($status->value ?? (is_string($status) ? $status : '')) {
            'draft' => 'badge-ghost',
            'pending' => 'badge-warning',
            'approved' => 'badge-info',
            'shipped' => 'badge-primary',
            'delivered' => 'badge-success',
            'cancelled' => 'badge-error',
            default => 'badge-ghost'
        };
    }

    public function render()
    {
        return view('livewire.asset-transfers.detail-info');
    }
}