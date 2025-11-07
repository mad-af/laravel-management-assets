<?php

namespace App\Livewire\Branches;

use Livewire\Attributes\Url;
use Livewire\Component;

class Drawer extends Component
{
    #[Url(as: 'action')] // ?action=create|edit
    public ?string $action = null;

    #[Url(as: 'branch_id')] // ?branch_id=123
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
            $this->showDrawer = true;
            $this->editingBranchId = $this->branch_id;
        } // else: biarkan state tetap (jangan auto-tutup tiap update)
    }

    public function openEditDrawer($branchId)
    {
        $this->redirect(route('branches.index', [
            'action' => 'edit',
            'branch_id' => $branchId,
        ]), navigate: true);
    }

    public function openDrawer($branchId = null)
    {
        if ($branchId) {
            $this->redirect(route('branches.index', [
                'action' => 'edit',
                'branch_id' => $branchId,
            ]), navigate: true);
        } else {
            $this->redirect(route('branches.index', [
                'action' => 'create',
            ]), navigate: true);
        }
    }

    public function closeDrawer()
    {
        $this->dispatch('resetForm');
        $this->redirect(route('branches.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.branches.drawer');
    }
}
