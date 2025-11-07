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
        $this->redirect(route('companies.index', [
            'action' => 'edit',
            'company_id' => $companyId,
        ]), navigate: true);
    }

    public function openDrawer()
    {
        $this->redirect(route('companies.index', [
            'action' => 'create',
        ]), navigate: true);
    }

    public function closeDrawer()
    {
        $this->dispatch('resetForm');
        $this->redirect(route('companies.index'), navigate: true);
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
