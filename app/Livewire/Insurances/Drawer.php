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
        $this->redirect(route('insurances.index', [
            'action' => 'edit',
            'insurance_id' => $insuranceId,
        ]), navigate: true);
    }

    public function openDrawer($insuranceId = null)
    {
        if ($insuranceId) {
            $this->redirect(route('insurances.index', [
                'action' => 'edit',
                'insurance_id' => $insuranceId,
            ]), navigate: true);
        } else {
            $this->redirect(route('insurances.index', [
                'action' => 'create',
            ]), navigate: true);
        }
    }

    public function closeDrawer()
    {
        $this->dispatch('resetForm');
        $this->redirect(route('insurances.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.insurances.drawer');
    }
}
