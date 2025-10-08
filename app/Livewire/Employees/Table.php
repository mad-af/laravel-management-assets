<?php

namespace App\Livewire\Employees;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    /** @var \Illuminate\Support\Collection<Company> */
    public $companies;

    /** Company yang sedang dipilih via tabs */
    public ?string $selectedCompanyId = null;

    // (opsional) filter branch spesifik saat tab company dipilih
    public ?string $branchId = null;

    // Persist ke URL
    protected $queryString = [
        'search',
        'statusFilter',
    ];

    protected $listeners = [
        'employee-saved' => '$refresh',
        'employee-deleted' => '$refresh',
    ];

    public function mount(?string $selectedCompanyId = null): void
    {
        // Ambil daftar perusahaan (sesuaikan scoping/authorization kamu)
        $user = Auth::user();

        // Ambil perusahaan yang di-assign ke user (sesuaikan relasinya kalau beda)
        $this->companies = $user->companies()
            ->get();

        // Set default tab: dari URL/param jika ada, kalau tidak pakai first()
        $this->selectedCompanyId = $selectedCompanyId
            ?? ($this->companies->first()->id ?? null);
    }

    /** Reset pagination saat ganti tab */
    public function updatedSelectedCompanyId(): void
    {
        $this->resetPage();
    }

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

    public function openEditDrawer($employeeId)
    {
        $this->dispatch('open-edit-drawer', employeeId: $employeeId);
    }

    public function render()
    {
        $search = trim($this->search);

        $employees = Employee::query()
            ->with(['branch:id,name'])

            // filter by selected tab company
            ->when($this->selectedCompanyId, fn ($q) => $q->where('company_id', $this->selectedCompanyId)
            )

            // (opsional) filter branch spesifik
            ->when($this->branchId, fn ($q) => $q->where('branch_id', $this->branchId)
            )

            // search kolom + company.name
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('full_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhereHas('company', fn ($c) => $c->where('name', 'like', "%{$search}%"));

                    if (is_numeric($search)) {
                        $qq->orWhere('employee_number', (int) $search);
                    }
                });
            })

            ->when($this->statusFilter === 'active', fn ($q) => $q->where('is_active', true))
            ->when($this->statusFilter === 'inactive', fn ($q) => $q->where('is_active', false))

            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.employees.table', compact('employees'));
    }
}
