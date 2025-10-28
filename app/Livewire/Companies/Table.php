<?php

namespace App\Livewire\Companies;

use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;
use App\Models\Company;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public array $expanded = [2];

    protected $queryString = ['search', 'statusFilter'];

    protected $listeners = [
        'company-saved' => '$refresh',
        'company-deleted' => '$refresh',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function openDrawer()
    {
        $this->dispatch('open-drawer');
    }

    public function openEditDrawer($companyId)
    {
        $this->dispatch('open-edit-drawer', companyId: $companyId);
    }

    public function render()
    {
        $companies = Company::query()
            // Grouped search (biar orWhere tidak bocor ke filter lain)
            ->when($this->search, function ($query) {
                $s = '%'.$this->search.'%';
                $query->where(function ($q) use ($s) {
                    $q->where('name', 'like', $s)
                        ->orWhere('code', 'like', $s)
                        ->orWhere('email', 'like', $s);
                });
            })
            ->when($this->statusFilter === 'active', fn ($q) => $q->where('is_active', true))
            ->when($this->statusFilter === 'inactive', fn ($q) => $q->where('is_active', false))
            // Eager load branches
            ->with([
                'branches' => function ($q) {
                    $q->select('id', 'company_id', 'name', 'address', 'is_active') // tambah kolom lain bila perlu
                        ->where('is_active', true); // aktifkan jika hanya ingin cabang aktif
                },
            ])
            ->withCount(['userCompanies'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Tandai branch mana yang HQ (inject properti dinamis is_hq)
        $companies->getCollection()->transform(function ($company) {
            $hqId = $company->hq_branch_id ?? null;
            if ($company->relationLoaded('branches')) {
                $company->branches->each(function ($branch) use ($hqId) {
                    $branch->is_hq = ($branch->id === $hqId);
                });
            }

            return $company;
            
        });

        return view('livewire.companies.table', compact('companies'));
    }

    public function getIsAdminProperty(): bool
    {
        $user = Auth::user();
        return $user && $user->role === UserRole::ADMIN;
    }
}
