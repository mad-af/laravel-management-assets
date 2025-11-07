<?php

namespace App\Livewire\Users;

use App\Enums\UserRole;
use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use App\Models\UserBranch;
use App\Models\UserCompany;
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

    public array $branch_ids = [];

    public $role = '';

    public $is_active = true;

    public $isEdit = false;

    public $allCompanies = [];

    public $allRoles = [];

    public array $companies = [];

    public $companySearchTerm = '';

    public $companyBranches = [];

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email'.($this->isEdit ? ','.$this->userId : ''),
            'phone' => 'nullable|string|max:20',
            'company_ids' => 'nullable|array',
            'company_ids.*' => 'nullable|exists:companies,id',
            'role' => 'required|in:'.implode(',', UserRole::values()),
            'is_active' => 'nullable|boolean',
        ];

        if ($this->isEdit) {
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

        // Initialize branches list grouped by selected companies
        $this->refreshCompanyBranches();
    }

    public function loadUser()
    {
        if ($this->userId) {
            $user = User::with(['userCompanies.company', 'userBranches'])->find($this->userId);
            if ($user) {
                $this->name = $user->name;
                $this->email = $user->email;
                $this->phone = $user->phone;
                $this->company_ids = $user->userCompanies->pluck('company_id')->toArray();
                $this->branch_ids = $user->userBranches->pluck('branch_id')->toArray();
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
        $this->branch_ids = [];
        $this->role = '';
        $this->is_active = true;
        $this->resetValidation();
        $this->refreshCompanyBranches();
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

    public function save()
    {
        try {

            $this->validate($this->rules());

            if ($this->isEdit && $this->userId) {
                // Update user

                $user = User::find($this->userId);
                if (! $user) {
                    $this->error('Data Tidak Ditemukan', 'User tidak ditemukan.');

                    return;
                }

                $user->name = $this->name;
                $user->email = $this->email;
                $user->role = $this->role; // cast ke enum oleh model

                // Password opsional saat edit
                if (! empty($this->password)) {
                    $user->password = $this->password; // otomatis di-hash oleh cast
                }

                $user->save();

                // Sinkronisasi perusahaan via model UserCompany (mengisi kolom pivot 'id')
                UserCompany::syncForUser($user, $this->company_ids ?? []);

                // Sinkronisasi cabang via model UserBranch
                UserBranch::syncForUser($user, $this->branch_ids ?? []);

                // Bersihkan field password agar tidak tersisa di form
                $this->password = '';
                $this->password_confirmation = '';

                $this->success('Berhasil Diperbarui', 'Data user telah diperbarui.');
                $this->dispatch('user-updated', id: $user->id);
            } else {
                // Create user baru
                $user = new User;
                $user->name = $this->name;
                $user->email = $this->email;
                $user->role = $this->role; // cast ke enum oleh model
                $user->save();

                // Sinkronisasi perusahaan setelah user dibuat via model UserCompany
                UserCompany::syncForUser($user, $this->company_ids ?? []);

                // Sinkronisasi cabang via model UserBranch
                UserBranch::syncForUser($user, $this->branch_ids ?? []);

                $this->userId = $user->id;
                $this->isEdit = true;

                $this->success('Berhasil Ditambahkan', 'User baru telah dibuat.');
                $this->dispatch('user-saved', id: $user->id);
            }
        } catch (\Exception $e) {
            $this->error('Gagal Menyimpan', $e->getMessage());
            dd($e);
        } finally {
            $this->dispatch('close-drawer');
        }
    }

    // Metode sinkronisasi dipindahkan ke App\Models\UserCompany::syncForUser()

    public function updatedCompanyIds()
    {
        // Filter branch selections to only those within selected companies
        if (! empty($this->branch_ids)) {
            $validBranchIds = Branch::whereIn('company_id', $this->company_ids ?? [])
                ->pluck('id')
                ->toArray();
            $this->branch_ids = array_values(array_intersect($this->branch_ids, $validBranchIds));
        }

        $this->refreshCompanyBranches();
    }

    protected function refreshCompanyBranches(): void
    {
        $ids = $this->company_ids ?? [];

        if (empty($ids)) {
            $this->companyBranches = collect([]);

            return;
        }

        // Ambil companies beserta branches aktifnya, lalu urutkan branches: HQ terlebih dahulu, kemudian nama
        $companies = Company::with(['branches' => function ($q) {
            $q->where('is_active', true);
        }])
            ->whereIn('id', $ids)
            ->orderBy('name')
            ->get();

        // Sort branches per company: hq_branch_id di depan, lalu nama ascending
        $companies->transform(function ($company) {
            $sorted = $company->branches
                ->sortBy(function ($branch) use ($company) {
                    $isHqFirst = ((string) $branch->id === (string) $company->hq_branch_id) ? 0 : 1;

                    return [$isHqFirst, $branch->name];
                })
                ->values();

            $company->setRelation('branches', $sorted);

            return $company;
        });

        $this->companyBranches = $companies;
    }

    public function render()
    {
        return view('livewire.users.form');
    }
}
