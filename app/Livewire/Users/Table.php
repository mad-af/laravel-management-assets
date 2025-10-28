<?php

namespace App\Livewire\Users;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Enums\UserRole;

class Table extends Component
{
    use WithPagination;

    public $search = '';

    protected $listeners = [
        'user-saved' => '$refresh',
        'user-updated' => '$refresh',
        'user-deleted' => '$refresh',
    ];

    public function updatingSearch()
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
            User::findOrFail($userId)->delete();
            $this->dispatch('user-deleted');
        } catch (\Throwable $e) {
            // handle error
        }
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                      ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.users.table', compact('users'));
    }

    public function getIsAdminProperty(): bool
    {
        $user = Auth::user();
        return $user && $user->role === UserRole::ADMIN;
    }
}