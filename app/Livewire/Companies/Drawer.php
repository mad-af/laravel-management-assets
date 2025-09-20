<?php

namespace App\Livewire\Companies;

use Livewire\Component;
use Livewire\Attributes\Url;

class Drawer extends Component
{
    #[Url(as: 'action')]       // ?action=create|edit
    public ?string $action = null;

    #[Url(as: 'company_id')]  // ?company_id=123
    public ?string $company_id = null;

    public bool $showDrawer = false;
    public ?string $editingCompanyId = null;

    protected $listeners = [
        'close-drawer' => 'closeDrawer',
        'open-drawer' => 'openDrawer',
        'open-edit-drawer' => 'openEditDrawer',
        'company-saved' => 'handleCompanySaved',
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

    public function updatedCompanyId()
    {
        $this->applyActionFromUrl();
    }

    protected function applyActionFromUrl(): void
    {
        if ($this->action === 'create') {
            $this->showDrawer = true;
            $this->editingCompanyId = null;
        } elseif ($this->action === 'edit' && $this->company_id) {
            $this->showDrawer   = true;
            $this->editingCompanyId = $this->company_id;
        } // else: biarkan state tetap (jangan auto-tutup tiap update)
    }

    public function openEditDrawer($companyId)
    {
        $this->action = 'edit';
        $this->company_id = $companyId;
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
        $this->editingCompanyId = null;
        $this->dispatch('resetForm');

        // hapus query di URL (Url-bound akan pushState)
        $this->action = null;
        $this->company_id = null;
    }

    public function editCompany($companyId)
    {
        $this->openEditDrawer($companyId);
    }

    public function handleCompanySaved()
    {
        $this->closeDrawer();
    }

    public function render()
    {
        return view('livewire.companies.drawer');
    }
}
