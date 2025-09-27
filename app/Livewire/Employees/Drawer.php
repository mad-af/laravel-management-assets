<?php

namespace App\Livewire\Employees;

use Livewire\Attributes\Url;
use Livewire\Component;

class Drawer extends Component
{
    #[Url(as: 'action')] // ?action=create|edit
    public ?string $action = null;

    #[Url(as: 'employee_id')] // ?employee_id=123
    public ?string $employee_id = null;

    public bool $showDrawer = false;

    public ?string $editingEmployeeId = null;

    protected $listeners = [
        'close-drawer' => 'closeDrawer',
        'open-drawer' => 'openDrawer',
        'open-edit-drawer' => 'openEditDrawer',
        'employee-saved' => 'closeDrawer',
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

    public function updatedEmployeeId()
    {
        $this->applyActionFromUrl();
    }

    protected function applyActionFromUrl(): void
    {
        if ($this->action === 'create') {
            $this->showDrawer = true;
            $this->editingEmployeeId = null;
        } elseif ($this->action === 'edit' && $this->employee_id) {
            $this->showDrawer = true;
            $this->editingEmployeeId = $this->employee_id;
        } // else: biarkan state tetap (jangan auto-tutup tiap update)
    }

    public function openEditDrawer($employeeId)
    {
        $this->action = 'edit';
        $this->employee_id = $employeeId;
        $this->applyActionFromUrl();
    }

    public function openDrawer($employeeId = null)
    {
        if ($employeeId) {
            $this->action = 'edit';
            $this->employee_id = $employeeId;
        } else {
            $this->action = 'create';
        }
        $this->applyActionFromUrl();
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->editingEmployeeId = null;
        $this->dispatch('resetForm');

        // hapus query di URL (Url-bound akan pushState)
        $this->action = null;
        $this->employee_id = null;
    }

    public function editEmployee($employeeId)
    {
        $this->openEditDrawer($employeeId);
        $this->showDrawer = true;
    }

    public function handleEmployeeSaved()
    {
        $this->closeDrawer();
    }

    public function render()
    {
        return view('livewire.employees.drawer');
    }
}
