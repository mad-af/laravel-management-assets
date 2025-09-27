<?php

namespace App\Livewire\Branches;

use Livewire\Component;
use Livewire\Attributes\Url;

class Drawer extends Component
{
    #[Url(as: 'action')]       // ?action=create|edit
    public ?string $action = null;

    #[Url(as: 'branch_id')]  // ?branch_id=123
    public ?string $branch_id = null;

    public bool $showDrawer = false;
    public ?string $editingBranchId = null;

    protected $listeners = [
        'close-drawer' => 'closeDrawer',
        'open-drawer' => 'openDrawer',
        'open-edit-drawer' => 'openEditDrawer',
        'branch-saved' => 'closeDrawer',
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

    public function updatedBranchId()
    {
        $this->applyActionFromUrl();
    }

    protected function applyActionFromUrl(): void
    {
        if ($this->action === 'create') {
            $this->showDrawer = true;
            $this->editingBranchId = null;
        } elseif ($this->action === 'edit' && $this->branch_id) {
            $this->showDrawer   = true;
            $this->editingBranchId = $this->branch_id;
        } // else: biarkan state tetap (jangan auto-tutup tiap update)
    }

    public function openEditDrawer($branchId)
    {
        $this->action = 'edit';
        $this->branch_id = $branchId;
        $this->applyActionFromUrl();
    }

    public function openDrawer($branchId = null)
    {
        if ($branchId) {
            $this->action = 'edit';
            $this->branch_id = $branchId;
        } else {
            $this->action = 'create';
        }
        $this->applyActionFromUrl();
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->editingBranchId = null;
        $this->dispatch('resetForm');

        // hapus query di URL (Url-bound akan pushState)
        $this->action = null;
        $this->branch_id = null;
    }

    public function render()
    {
        return view('livewire.branches.drawer');
    }
}