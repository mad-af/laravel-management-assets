<?php

namespace App\Livewire\AssetTransfers;

use App\Models\AssetTransfer;
use Livewire\Component;

class EditDrawer extends Component
{
    public $showDrawer = false;
    public $transfer;
    public $transferId;

    protected $listeners = [
        'openEditDrawer' => 'openDrawer',
        'closeEditDrawer' => 'closeDrawer',
        'transfer-updated' => 'handleTransferUpdated'
    ];

    public function openDrawer($transferId)
    {
        $this->transferId = $transferId;
        $this->transfer = AssetTransfer::with(['items.asset', 'fromLocation', 'toLocation'])->find($transferId);
        $this->showDrawer = true;
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->transfer = null;
        $this->transferId = null;
        $this->dispatch('resetEditForm');
    }

    public function handleTransferUpdated()
    {
        $this->closeDrawer();
        $this->dispatch('transfer-saved'); // Refresh table
    }

    public function render()
    {
        return view('livewire.asset-transfers.edit-drawer');
    }
}