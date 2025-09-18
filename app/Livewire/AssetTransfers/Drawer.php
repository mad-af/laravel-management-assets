<?php

namespace App\Livewire\AssetTransfers;

use Livewire\Component;
use Livewire\Attributes\Url;

class Drawer extends Component
{
    #[Url(as: 'action')]       // ?action=create|edit
    public ?string $action = null;

    #[Url(as: 'transfer_id')]  // ?transfer_id=123
    public ?string $transfer_id = null;

    public bool $showDrawer = false;
    public ?string $editingTransferId = null;

    protected $listeners = [
        'closeDrawer' => 'closeDrawer',
        'transfer-saved' => 'handleTransferSaved',
        'transfer-updated' => 'handleTransferSaved',
        'close-drawer' => 'closeDrawer',
        'open-drawer' => 'openDrawer',
        'open-edit-drawer' => 'openEditDrawer',
    ];

    public function mount()
    {
        $this->applyActionFromUrl(); // hanya sekali di initial load
    }

    // Dipanggil kalau kamu ubah action via property (akan auto update URL)
    public function updatedAction($value)
    {
        $this->applyActionFromUrl();
    }

    public function updatedTransferId()
    {
        $this->applyActionFromUrl();
    }

    protected function applyActionFromUrl(): void
    {
        if ($this->action === 'create') {
            $this->showDrawer = true;
            $this->editingTransferId = null;
        } elseif ($this->action === 'edit' && $this->transfer_id) {
            $this->showDrawer   = true;
            $this->editingTransferId = $this->transfer_id;
        } // else: biarkan state tetap (jangan auto-tutup tiap update)
    }

    public function openEditDrawer($transferId)
    {
        $this->action = 'edit';
        $this->transfer_id = $transferId;
        $this->applyActionFromUrl();
    }

    public function openDrawer()
    {
        $this->action = 'create';
        $this->applyActionFromUrl();
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->editingTransferId = null;
        $this->dispatch('resetForm');

        // hapus query di URL (Url-bound akan pushState)
        $this->action = null;
        $this->transfer_id = null;
    }

    public function render()
    {
        return view('livewire.asset-transfers.drawer');
    }
}
