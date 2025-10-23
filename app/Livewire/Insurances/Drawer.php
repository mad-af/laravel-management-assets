<?php

namespace App\Livewire\Insurances;

use Livewire\Attributes\Url;
use Livewire\Component;

class Drawer extends Component
{
    #[Url(as: 'action')] // ?action=create|edit
    public ?string $action = null;

    #[Url(as: 'insurance_id')] // ?insurance_id=123
    public ?string $insurance_id = null;

    public bool $showDrawer = false;

    public ?string $editingInsuranceId = null;

    protected $listeners = [
        'close-drawer' => 'closeDrawer',
        'open-drawer' => 'openDrawer',
        'open-edit-drawer' => 'openEditDrawer',
        'insurance-saved' => 'closeDrawer',
        'insurance-updated' => 'closeDrawer',
    ];

    public function mount()
    {
        $this->applyActionFromUrl();
    }

    public function updatedAction($value)
    {
        $this->applyActionFromUrl();
    }

    public function updatedInsuranceId()
    {
        $this->applyActionFromUrl();
    }

    protected function applyActionFromUrl(): void
    {
        if ($this->action === 'create') {
            $this->showDrawer = true;
            $this->editingInsuranceId = null;
        } elseif ($this->action === 'edit' && $this->insurance_id) {
            $this->showDrawer = true;
            $this->editingInsuranceId = $this->insurance_id;
        }
    }

    public function openEditDrawer($insuranceId)
    {
        $this->action = 'edit';
        $this->insurance_id = $insuranceId;
        $this->applyActionFromUrl();
    }

    public function openDrawer($insuranceId = null)
    {
        if ($insuranceId) {
            $this->action = 'edit';
            $this->insurance_id = $insuranceId;
        } else {
            $this->action = 'create';
        }
        $this->applyActionFromUrl();
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->editingInsuranceId = null;
        $this->dispatch('resetForm');

        $this->action = null;
        $this->insurance_id = null;
    }

    public function render()
    {
        return view('livewire.insurances.drawer');
    }
}
