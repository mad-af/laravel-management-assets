<?php

namespace App\Livewire\Companies;

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Support\Str;
use Livewire\Component;
use Mary\Traits\Toast;

class Form extends Component
{
    use Toast;

    public $companyId;

    public $name = '';

    public $code = '';

    public $tax_id = '';

    public $phone = '';

    public $email = '';

    public $website = '';

    public $image;

    public $is_active = true;

    public $isEdit = false;

    public $hq_branch_id = '';

    public $branches = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:10|unique:companies,code',
        'tax_id' => 'nullable|string|max:50',
        'phone' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'website' => 'nullable|url|max:255',
        'image' => 'nullable|image|max:2048',
        'is_active' => 'boolean',
        'location_id' => 'nullable|exists:locations,id',
    ];

    protected $listeners = [
        'editCompany' => 'edit',
        'resetForm' => 'resetForm',
    ];

    public function mount($companyId = null)
    {
        $this->loadBranches();
        if ($companyId) {
            $this->edit($companyId);
        }
    }

    public function loadBranches()
    {
        $this->branches = Branch::where('is_active', true)
            ->where('company_id', $this->companyId)
            ->orderBy('name')
            ->get()
            ->map(function ($branch) {
                return [
                    'id' => $branch->id,
                    'name' => $branch->name,
                ];
            })
            ->toArray();
    }

    public function edit($companyId)
    {
        $company = Company::with('hqBranch')->findOrFail($companyId);
        $this->companyId = $company->id;
        $this->name = $company->name;
        $this->code = $company->code;
        $this->tax_id = $company->tax_id;
        $this->phone = $company->phone;
        $this->email = $company->email;
        $this->website = $company->website;
        $this->is_active = $company->is_active;
        $this->hq_branch_id = $company->hq_branch_id;
        $this->isEdit = true;

        // Update validation rules for edit
        $this->rules['code'] = 'required|string|max:10|unique:companies,code,'.$this->companyId;
    }

    public function resetForm()
    {
        $this->reset([
            'companyId', 'name', 'code', 'hq_branch_id', 'tax_id', 
            'phone', 'email', 'website', 'image', 'is_active', 'isEdit',
        ]);
        $this->is_active = true;
        $this->hq_branch_id = '';
        $this->rules['code'] = 'required|string|max:10|unique:companies,code';
        $this->loadBranches();
    }

    public function render()
    {
        return view('livewire.companies.form');
    }

    public function generateCode()
    {
        // Ambil huruf pertama dari setiap kata
        $initial = collect(explode(' ', $this->name))
            ->map(fn ($word) => strtoupper(Str::substr($word, 0, 1)))
            ->join('');

        // Jika kurang dari 4 huruf, ambil huruf tambahan dari nama pertama
        if (strlen($initial) < 4) {
            $firstWord = strtoupper(Str::replace(' ', '', $this->name));
            $initial = strtoupper(Str::substr($firstWord, 0, 4));
        }

        // Jika lebih dari 4 huruf, potong jadi 4
        $code = Str::upper(Str::substr($initial, 0, 4));

        // Pastikan unik di database
        $original = $code;
        $counter = 1;
        while (Company::where('code', $code)->exists()) {
            // Tambahkan angka jika bentrok, potong supaya tetap 4
            $suffix = (string) $counter++;
            $code = Str::substr($original, 0, 4 - strlen($suffix)).$suffix;
        }

        $this->code = $code;
    }
}
