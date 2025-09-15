<?php

namespace App\Livewire\Companies;

use Livewire\Component;

class Drawer extends Component
{
    public $showDrawer = false;
    public $editingCompanyId = null;

    protected $listeners = [
        'openDrawer' => 'openDrawer',
        'closeDrawer' => 'closeDrawer',
        'editCompany' => 'editCompany',
        'company-saved' => 'handleCompanySaved',
        'close-drawer' => 'closeDrawer'
    ];

    public function openDrawer()
    {
        $this->showDrawer = true;
        $this->editingCompanyId = null;
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->editingCompanyId = null;
        $this->dispatch('resetForm');
    }

    public function editCompany($companyId)
    {
        $this->editingCompanyId = $companyId;
        $this->showDrawer = true;
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
