<?php

namespace App\Livewire\Scanners;

use Livewire\Component;
use Mary\Traits\Toast;

class Form extends Component
{
    use Toast;

    public $scannerId;
    public $name = '';
    public $type = '';
    public $location = '';
    public $status = 'active';
    public $description = '';
    public $is_active = true;
    public $isEdit = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'type' => 'required|string|max:100',
        'location' => 'required|string|max:255',
        'status' => 'required|string|in:active,inactive,maintenance',
        'description' => 'nullable|string|max:1000',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'name.required' => 'Nama scanner wajib diisi.',
        'type.required' => 'Tipe scanner wajib diisi.',
        'location.required' => 'Lokasi scanner wajib diisi.',
        'status.required' => 'Status scanner wajib dipilih.',
        'status.in' => 'Status scanner tidak valid.',
    ];

    protected $listeners = [
        'editScanner' => 'edit',
        'resetForm' => 'resetForm'
    ];

    public function mount($scannerId = null)
    {
        $this->scannerId = $scannerId;
        
        if ($scannerId) {
            $this->isEdit = true;
            $this->loadScanner();
        }
    }

    public function loadScanner()
    {
        // For now, we'll simulate loading scanner data
        // In a real application, you would load from a Scanner model
        if ($this->scannerId) {
            // Simulate loading scanner data
            $this->name = 'Scanner ' . $this->scannerId;
            $this->type = 'QR/Barcode Scanner';
            $this->location = 'Office Main';
            $this->status = 'active';
            $this->description = 'Scanner untuk asset management';
            $this->is_active = true;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->isEdit && $this->scannerId) {
                // Update scanner logic would go here
                // For now, we'll just show a success message
                $this->success('Scanner updated successfully!');
                $this->dispatch('scanner-updated');
            } else {
                // Create scanner logic would go here
                // For now, we'll just show a success message
                $this->success('Scanner created successfully!');
                $this->dispatch('scanner-saved');
                $this->resetForm();
            }
        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->name = '';
        $this->type = '';
        $this->location = '';
        $this->status = 'active';
        $this->description = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.scanners.form');
    }
}