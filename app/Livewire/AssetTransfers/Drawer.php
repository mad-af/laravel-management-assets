<?php

namespace App\Livewire\AssetTransfers;

use Livewire\Component;
use Livewire\Attributes\Url;

class Drawer extends Component
{
    #[Url(as: 'action')]       // ?action=create|edit
    public ?string $action = null;

    #[Url(as: 'transfer_id')]  // ?transfer_id=123
    public ?int $transfer_id = null;

    public bool $showDrawer = false;
    public ?int $editingTransferId = null;

    protected $listeners = [
        'openDrawer' => 'openDrawer',
        'closeDrawer' => 'closeDrawer',
        'editTransfer' => 'editTransfer',
        'transfer-saved' => 'handleTransferSaved',
        'transfer-updated' => 'handleTransferSaved',
        'close-drawer' => 'closeDrawer',
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

    public function openDrawer()
    {
        $this->action = 'create'; // URL akan jadi ?action=create
        $this->showDrawer = true;
        $this->editingTransferId = null;
    }

    public function editTransfer(int $id)
    {
        $this->transfer_id = $id; // URL jadi ?action=edit&transfer_id=ID
        $this->action = 'edit';
        $this->showDrawer = true;
        $this->editingTransferId = $id;
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
