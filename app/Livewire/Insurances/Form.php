<?php

namespace App\Livewire\Insurances;

use App\Models\Insurance;
use Livewire\Component;
use Mary\Traits\Toast;

class Form extends Component
{
    use Toast;

    public $insuranceId;

    public $name = '';

    public $phone = '';

    public $email = '';

    public $address = '';

    public $isEdit = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:30',
        'email' => 'nullable|email|max:255',
        'address' => 'nullable|string|max:500',
    ];

    protected $listeners = [
        'resetForm' => 'resetForm',
    ];

    public function mount($insuranceId = null)
    {
        $this->insuranceId = $insuranceId;

        if ($insuranceId) {
            $this->isEdit = true;
            $this->loadInsurance();
        }
    }

    public function loadInsurance()
    {
        if ($this->insuranceId) {
            $insurance = Insurance::find($this->insuranceId);
            if ($insurance) {
                $this->name = $insurance->name;
                $this->phone = $insurance->phone ?? '';
                $this->email = $insurance->email ?? '';
                $this->address = $insurance->address ?? '';
            }
        }
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->isEdit && $this->insuranceId) {
                $insurance = Insurance::find($this->insuranceId);
                $insurance->update([
                    'name' => $this->name,
                    'phone' => $this->phone ?: null,
                    'email' => $this->email ?: null,
                    'address' => $this->address ?: null,
                ]);
                $this->success('Provider asuransi berhasil diperbarui!');
                $this->dispatch('insurance-updated');
            } else {
                Insurance::create([
                    'name' => $this->name,
                    'phone' => $this->phone ?: null,
                    'email' => $this->email ?: null,
                    'address' => $this->address ?: null,
                ]);
                $this->success('Provider asuransi berhasil ditambahkan!');
                $this->dispatch('insurance-saved');
                $this->resetForm();
            }
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->name = '';
        $this->phone = '';
        $this->email = '';
        $this->address = '';
        $this->isEdit = false;
        $this->insuranceId = null;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.insurances.form');
    }
}
