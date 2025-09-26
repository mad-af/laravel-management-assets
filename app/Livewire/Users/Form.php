<?php

namespace App\Livewire\Users;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;
use Livewire\Component;
use Mary\Traits\Toast;

class Form extends Component
{
    use Toast;

    public $userId;

    public $name = '';

    public $email = '';

    public $phone = '';

    public $password = '';

    public $password_confirmation = '';

    public array $company_ids = [];

    public $role = '';

    public $is_active = true;

    public $isEdit = false;

    public $allCompanies = [];

    public $allRoles = [];

    public array $companies = [];

    public $companySearchTerm = '';

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email'.($this->isEdit ? ','.$this->userId : ''),
            'phone' => 'nullable|string|max:20',
            'company_id' => 'nullable|exists:companies,id',
            'role' => 'required|in:'.implode(',', UserRole::values()),
            'is_active' => 'boolean',
        ];

        if (! $this->isEdit) {
            $rules['password'] = 'required|string|min:8|confirmed';
        } else {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        return $rules;
    }

    protected $listeners = [
        'editUser' => 'edit',
        'resetForm' => 'resetForm',
    ];

    public function mount($userId = null)
    {
        $this->userId = $userId;
        $this->allCompanies = Company::where('is_active', true)->get();

        // Initialize companies search with first 10 companies
        $this->companies = Company::where('is_active', true)
            ->limit(10)
            ->get()
            ->map(function ($company) {
                return [
                    'id' => $company->id,
                    'name' => $company->name,
                    'code' => $company->code,
                ];
            })->toArray();

        $this->allRoles = collect(UserRole::cases())
            ->map(function ($role) {
                return [
                    'value' => $role->value,
                    'label' => $role->label(),
                    'disabled' => $role == UserRole::ADMIN,
                ];
            })->toArray();

        if ($userId) {
            $this->isEdit = true;
            $this->loadUser();
        }
    }

    public function loadUser()
    {
        if ($this->userId) {
            $user = User::with(['userCompanies.company'])->find($this->userId);
            if ($user) {
                $this->name = $user->name;
                $this->email = $user->email;
                $this->phone = $user->phone;
                $this->company_ids = $user->userCompanies->pluck('company_id')->toArray();
                $this->role = $user->role->value;
                $this->is_active = $user->is_active;

                // Ensure selected company is in search results for editing
                if ($user->company_id && $user->company) {
                    $selectedCompany = [
                        'id' => $user->company->id,
                        'name' => $user->company->name,
                        'code' => $user->company->code,
                    ];

                    // Check if selected company is already in search results
                    $exists = collect($this->companies)->contains('id', $user->company->id);

                    if (! $exists) {
                        // Add selected company to the beginning of search results
                        array_unshift($this->companies, $selectedCompany);
                    }
                }
            }
        }
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->company_ids = [];
        $this->role = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function searchCompanies($value = '')
    {
        $this->companySearchTerm = $value;

        if (empty($value)) {
            // Return first 10 companies when no search term
            $this->companies = Company::where('is_active', true)
                ->limit(10)
                ->get()
                ->map(function ($company) {
                    return [
                        'id' => $company->id,
                        'name' => $company->name,
                        'code' => $company->code,
                    ];
                })->toArray();
        } else {
            // Search companies by name or code
            $this->companies = Company::where('is_active', true)
                ->where(function ($query) use ($value) {
                    $query->where('name', 'like', '%'.$value.'%')
                        ->orWhere('code', 'like', '%'.$value.'%');
                })
                ->limit(20)
                ->get()
                ->map(function ($company) {
                    return [
                        'id' => $company->id,
                        'name' => $company->name,
                        'code' => $company->code,
                    ];
                })->toArray();
        }
    }

    public function render()
    {
        return view('livewire.users.form');
    }
}
