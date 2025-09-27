<?php

namespace App\Livewire\Employees;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mary\Traits\Toast;

class Form extends Component
{
    use Toast;

    // State
    public ?string $employeeId = null;

    public ?string $company_id = null;

    public ?string $branch_id = null;

    public ?string $employee_number = null;

    public string $full_name = '';

    public ?string $email = null;

    public ?string $phone = null;

    public bool $is_active = true;

    public bool $isEdit = false;

    // Dropdown sources
    public array $companies = [];

    public array $branches = [];

    protected $rules = [
        'company_id' => 'required|uuid|exists:companies,id',
        'branch_id' => 'nullable|uuid|exists:branches,id',
        'employee_number' => 'nullable|string|min:1|unique:employees,employee_number,NULL,id',
        'full_name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:50',
        'is_active' => 'boolean',
    ];

    protected $listeners = [
        'editEmployee' => 'edit',
        'resetForm' => 'resetForm',
    ];

    public function mount(?string $employeeId = null): void
    {
        $this->employeeId = $employeeId;

        // Load companies berdasarkan userCompany
        $this->loadCompanies();

        if ($employeeId) {
            $this->isEdit = true;
            $this->loadEmployee();
        }
    }

    /**
     * Ambil daftar company yang dimiliki user (userCompany) dan format untuk <x-select>
     */
    protected function loadCompanies(): void
    {
        $user = Auth::user();

        // Asumsi: relasi $user->companies() mengembalikan pivot user_companies (punya kolom company_id)
        // Jika user->companies() langsung ke Company model, bagian pluck bisa disesuaikan jadi pluck('id')
        $companyIds = method_exists($user, 'companies')
            ? $user->companies()->pluck('company_id')
            : collect();

        $this->companies = Company::query()
            ->whereIn('id', $companyIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->toArray();
    }

    /**
     * Ambil daftar branch untuk company terpilih
     */
    protected function loadBranches(?string $companyId): void
    {
        if (! $companyId) {
            $this->branches = [];

            return;
        }

        $this->branches = Branch::query()
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->toArray();
    }

    public function updatedCompanyId($value): void
    {
        $this->branch_id = null;
        $this->loadBranches($value);
    }

    /**
     * Load data employee saat edit
     */
    protected function loadEmployee(): void
    {
        $employee = Employee::find($this->employeeId);

        if (! $employee) {
            return;
        }

        $this->company_id = $employee->company_id;
        $this->branch_id = $employee->branch_id;
        $this->employee_number = $employee->employee_number;
        $this->full_name = $employee->full_name;
        $this->email = $employee->email;
        $this->phone = $employee->phone;
        $this->is_active = (bool) $employee->is_active;

        // Pastikan opsi branch terisi sesuai company saat edit
        $this->loadBranches($this->company_id);

        // Aturan unik untuk employee_number saat update
        $this->rules['employee_number'] = 'nullable|integer|min:1|unique:employees,employee_number,'.$employee->id;
    }

    public function save(): void
    {
        $this->validate();

        try {
            if ($this->isEdit && $this->employeeId) {
                $employee = Employee::findOrFail($this->employeeId);

                $employee->update([
                    'company_id' => $this->company_id,
                    'branch_id' => $this->branch_id,
                    'employee_number' => $this->employee_number,
                    'full_name' => $this->full_name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'is_active' => $this->is_active,
                ]);

                $this->success('Employee updated successfully!');
                $this->dispatch('employee-updated');
            } else {
                Employee::create([
                    'company_id' => $this->company_id,
                    'branch_id' => $this->branch_id,
                    'employee_number' => $this->employee_number,
                    'full_name' => $this->full_name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'is_active' => $this->is_active,
                ]);

                $this->success('Employee created successfully!');
                $this->dispatch('employee-saved');
                $this->resetForm();
            }
        } catch (\Throwable $e) {
            $this->error('An error occurred: '.$e->getMessage());
        }
    }

    public function edit(string $employeeId): void
    {
        $this->employeeId = $employeeId;
        $this->isEdit = true;
        $this->loadEmployee();
    }

    public function resetForm(): void
    {
        $this->reset([
            'employeeId', 'company_id', 'branch_id', 'employee_number',
            'full_name', 'email', 'phone', 'is_active', 'isEdit',
            'branches',
        ]);

        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.employees.form');
    }
}
