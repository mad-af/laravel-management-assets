<?php

namespace App\Livewire\Companies;

use App\Models\Company;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class Form extends Component
{
    use WithFileUploads, Toast;

    public $companyId;
    public $name = '';
    public $code = '';
    public $tax_id = '';
    public $address = '';
    public $phone = '';
    public $email = '';
    public $website = '';
    public $image;
    public $is_active = true;
    public $isEdit = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:10|unique:companies,code',
        'tax_id' => 'nullable|string|max:50',
        'address' => 'nullable|string',
        'phone' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'website' => 'nullable|url|max:255',
        'image' => 'nullable|image|max:2048',
        'is_active' => 'boolean',
    ];

    protected $listeners = [
        'editCompany' => 'edit',
        'resetForm' => 'resetForm'
    ];

    public function mount($companyId = null)
    {
        if ($companyId) {
            $this->edit($companyId);
        }
    }

    public function edit($companyId)
    {
        $company = Company::findOrFail($companyId);
        $this->companyId = $company->id;
        $this->name = $company->name;
        $this->code = $company->code;
        $this->tax_id = $company->tax_id;
        $this->address = $company->address;
        $this->phone = $company->phone;
        $this->email = $company->email;
        $this->website = $company->website;
        $this->is_active = $company->is_active;
        $this->isEdit = true;

        // Update validation rules for edit
        $this->rules['code'] = 'required|string|max:10|unique:companies,code,' . $this->companyId;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'tax_id' => $this->tax_id,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'is_active' => $this->is_active,
        ];

        if ($this->image) {
            $data['image'] = $this->image->store('companies', 'public');
        }

        if ($this->isEdit) {
            $company = Company::findOrFail($this->companyId);
            $company->update($data);
            $this->success('Company updated successfully!');
        } else {
            Company::create($data);
            $this->success('Company created successfully!');
        }

        $this->dispatch('company-saved');
        $this->dispatch('close-drawer');
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'companyId', 'name', 'code', 'tax_id', 'address', 
            'phone', 'email', 'website', 'image', 'is_active', 'isEdit'
        ]);
        $this->is_active = true;
        $this->rules['code'] = 'required|string|max:10|unique:companies,code';
    }

    public function render()
    {
        return view('livewire.companies.form');
    }
}
