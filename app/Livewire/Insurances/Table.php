<?php

namespace App\Livewire\Insurances;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Insurance;
use App\Enums\UserRole;

class Table extends Component
{
    use WithPagination;

    public $search = '';

    protected $listeners = [
        'insurance-saved' => '$refresh',
        'insurance-updated' => '$refresh',
        'insurance-deleted' => '$refresh',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openEditDrawer($insuranceId)
    {
        $this->dispatch('open-edit-drawer', insuranceId: $insuranceId);
    }

    public function delete($insuranceId)
    {
        try {
            Insurance::findOrFail($insuranceId)->delete();
            $this->dispatch('insurance-deleted');
        } catch (\Throwable $e) {
            // handle error
        }
    }

    public function render()
    {
        $insurances = Insurance::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.insurances.table', compact('insurances'));
    }

    public function getIsAdminProperty(): bool
    {
        $user = Auth::user();
        return $user && $user->role === UserRole::ADMIN;
    }
}
