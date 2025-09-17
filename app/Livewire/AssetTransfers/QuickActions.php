<?php

namespace App\Livewire\AssetTransfers;

use App\Models\AssetTransfer;
use App\Traits\WithAlert;
use Livewire\Component;

class QuickActions extends Component
{
    use WithAlert;

    public $quickActionsData;
    public $showEditModal = false;
    public $showStatusModal = false;

    public function mount($quickActionsData)
    {
        $this->quickActionsData = $quickActionsData;
    }

    public function openEditModal()
    {
        $this->showEditModal = true;
    }

    public function openStatusModal()
    {
        $this->showStatusModal = true;
    }

    public function executeTransfer()
    {
        if ($this->transfer->status->value === 'approved') {
            $this->transfer->update([
                'status' => 'in_progress',
                'executed_at' => now()
            ]);
            
            $this->success('Transfer berhasil dieksekusi!');
        }
    }

    public function cancelTransfer()
    {
        if (in_array($this->transfer->status->value, ['draft', 'pending'])) {
            $this->transfer->update(['status' => 'cancelled']);
            
            $this->success('Transfer berhasil dibatalkan!');
        }
    }

    public function render()
    {
        return view('livewire.asset-transfers.quick-actions');
    }
}