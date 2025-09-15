<?php

namespace App\Livewire\Companies;

use App\Models\Company;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    protected $queryString = ['search', 'statusFilter'];

    protected $listeners = [
        'company-saved' => '$refresh',
        'company-deleted' => '$refresh',
        'edit-company' => 'editCompany'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function editCompany($companyId)
    {
        $this->dispatch('editCompany', $companyId);
    }

    public function openDrawer()
    {
        $this->dispatch('openDrawer');
    }

    public function render()
    {
        $companies = Company::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter === 'active', function ($query) {
                $query->where('is_active', true);
            })
            ->when($this->statusFilter === 'inactive', function ($query) {
                $query->where('is_active', false);
            })
            ->withCount(['users', 'assets'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.companies.table', compact('companies'));
    }
}
