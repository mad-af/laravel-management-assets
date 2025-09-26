<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Table extends Component
{
    use WithPagination, Toast;

    public $search = '';
    public $statusFilter = '';
    public $roleFilter = '';
    public $perPage = 10;

    protected $listeners = [
        'user-saved' => '$refresh',
        'user-updated' => '$refresh',
        'user-deleted' => '$refresh',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function openEditDrawer($userId)
    {
        $this->dispatch('open-edit-drawer', userId: $userId);
    }

    public function delete($userId)
    {
        try {
            $user = User::findOrFail($userId);
            $user->delete();
            $this->success('User deleted successfully!');
            $this->dispatch('user-deleted');
        } catch (\Exception $e) {
            $this->error('Failed to delete user: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = User::query()
            ->with(['userCompanies.company']);

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        // Apply status filter
        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        // Apply role filter
        if ($this->roleFilter) {
            $query->where('role', $this->roleFilter);
        }

        $users = $query->orderBy('created_at', 'desc')
                      ->paginate($this->perPage);

        return view('livewire.users.table', [
            'users' => $users,
        ]);
    }
}