<?php

namespace App\Livewire\Companies;

use App\Models\Company;
use Livewire\Component;

class EditDrawer extends Component
{
    public $showDrawer = false;
    public $company;
    public $companyId;

    protected $listeners = [
        'openEditDrawer' => 'openDrawer',
        'closeEditDrawer' => 'closeDrawer',
        'company-updated' => 'handleCompanyUpdated'
    ];

    public function openDrawer($companyId)
    {
        $this->companyId = $companyId;
        $this->company = Company::find($companyId);
        $this->showDrawer = true;
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->company = null;
        $this->companyId = null;
        $this->dispatch('resetEditForm');
    }

    public function handleCompanyUpdated()
    {
        $this->closeDrawer();
        $this->dispatch('company-saved'); // Refresh table
    }

    public function render()
    {
        return view('livewire.companies.edit-drawer');
    }
}