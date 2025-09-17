<?php

namespace App\Livewire\AssetTransfers;

use Livewire\Component;

class Drawer extends Component
{
    public $showDrawer = false;
    public $editingTransferId = null;

    protected $listeners = [
        'openDrawer' => 'openDrawer',
        'closeDrawer' => 'closeDrawer',
        'editTransfer' => 'editTransfer',
        'transfer-saved' => 'handleTransferSaved',
        'transfer-updated' => 'handleTransferSaved',
        'close-drawer' => 'closeDrawer'
    ];

    public function openDrawer()
    {
        $this->showDrawer = true;
        $this->editingTransferId = null;
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->editingTransferId = null;
        $this->dispatch('resetForm');
    }

    public function editTransfer($transferId)
    {
        $this->editingTransferId = $transferId;
        $this->showDrawer = true;
    }

    public function handleTransferSaved()
    {
        $this->closeDrawer();
    }

    public function render()
    {
        return view('livewire.asset-transfers.drawer');
    }
}