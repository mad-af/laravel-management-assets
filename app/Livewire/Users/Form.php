<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\Company;
use App\Enums\UserRole;
use Livewire\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Hash;

class Form extends Component
{
    use Toast;

    public $userId;
    public $name = '';
    public $email = '';
    public $phone = '';
    public $password = '';
    public $password_confirmation = '';
    public $company_id = '';
    public $role = '';
    public $is_active = true;
    public $isEdit = false;
    public $allCompanies = [];
    public $allRoles = [];

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email' . ($this->isEdit ? ',' . $this->userId : ''),
            'phone' => 'nullable|string|max:20',
            'company_id' => 'nullable|exists:companies,id',
            'role' => 'required|in:' . implode(',', UserRole::values()),
            'is_active' => 'boolean',
        ];

        if (!$this->isEdit) {
            $rules['password'] = 'required|string|min:8|confirmed';
        } else {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        return $rules;
    }

    protected $listeners = [
        'editUser' => 'edit',
        'resetForm' => 'resetForm'
    ];

    public function mount($userId = null)
    {
        $this->userId = $userId;
        $this->allCompanies = Company::where('is_active', true)->get();
        $this->allRoles = collect(UserRole::cases())->map(function ($role) {
            return [
                'value' => $role->value,
                'label' => $role->label()
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
            $user = User::find($this->userId);
            if ($user) {
                $this->name = $user->name;
                $this->email = $user->email;
                $this->phone = $user->phone;
                $this->company_id = $user->company_id;
                $this->role = $user->role->value;
                $this->is_active = $user->is_active;
            }
        }
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->isEdit && $this->userId) {
                $user = User::find($this->userId);
                $updateData = [
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'company_id' => $this->company_id,
                    'role' => UserRole::from($this->role),
                    'is_active' => $this->is_active,
                ];

                if ($this->password) {
                    $updateData['password'] = Hash::make($this->password);
                }

                $user->update($updateData);
                // Role is updated directly in the update array
                
                $this->success('User updated successfully!');
                $this->dispatch('user-updated');
            } else {
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'password' => Hash::make($this->password),
                    'company_id' => $this->company_id,
                    'role' => UserRole::from($this->role),
                    'is_active' => $this->is_active,
                ]);
                
                $this->success('User created successfully!');
                $this->dispatch('user-saved');
                $this->resetForm();
            }
        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->company_id = '';
        $this->role = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.users.form');
    }
}