<?php

namespace App\Livewire\Branches;

use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Table extends Component
{
    use Toast, WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public int $perPage = 10;

    /** Tabs */
    public ?string $selectedCompanyId = null;

    public $companies; // Collection sederhana untuk tabs

    protected $listeners = [
        'branch-saved' => '$refresh',
        'branch-updated' => '$refresh',
        'branch-deleted' => '$refresh',
    ];

    public function mount(): void
    {
        $user = Auth::user();

        // Ambil perusahaan yang di-assign ke user (sesuaikan relasinya kalau beda)
        $this->companies = $user->companies()
            ->get();

        // Default pilih perusahaan pertama
        $this->selectedCompanyId ??= optional($this->companies->first())->id;
    }

    /** Reset paging saat filter berubah */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingSelectedCompanyId()
    {
        $this->resetPage();
    }

    public function openEditDrawer(string $branchId): void
    {
        $this->dispatch('open-edit-drawer', branchId: $branchId);
    }

    public function delete(string $branchId): void
    {
        try {
            Branch::findOrFail($branchId)->delete();
            $this->success('Cabang berhasil dihapus!');
            $this->dispatch('branch-deleted');
        } catch (\Throwable $e) {
            $this->error('Gagal menghapus cabang: '.$e->getMessage());
        }
    }

    public function render()
    {
        $branches = Branch::query()
            ->when($this->selectedCompanyId, fn ($q) => $q->where('company_id', $this->selectedCompanyId))
            ->when(! $this->selectedCompanyId, fn ($q) => $q->whereRaw('1=0')) // tidak ada perusahaan terpilih
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->paginate($this->perPage);

        return view('livewire.branches.table', compact('branches'));
    }
}
