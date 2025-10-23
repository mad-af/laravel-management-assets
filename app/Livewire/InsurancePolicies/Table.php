<?php

namespace App\Livewire\InsurancePolicies;

use App\Enums\InsuranceStatus;
use App\Models\InsurancePolicy;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Table extends Component
{
    use Toast, WithPagination;

    public $search = '';

    public $statusFilter = '';

    public int $perPage = 10;

    protected $queryString = ['search', 'statusFilter'];

    protected $listeners = [
        'policy-saved' => '$refresh',
        'policy-updated' => '$refresh',
        'policy-deleted' => '$refresh',
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

    public function openEditDrawer($policyId)
    {
        $this->dispatch('open-edit-drawer', policyId: $policyId);
    }

    public function delete($policyId)
    {
        try {
            InsurancePolicy::findOrFail($policyId)->delete();
            $this->success('Polis asuransi berhasil dihapus!');
            $this->dispatch('policy-deleted');
        } catch (\Throwable $e) {
            $this->error('Gagal menghapus polis: '.$e->getMessage());
        }
    }

    public function render()
    {
        $policies = InsurancePolicy::query()
            ->with(['insurance', 'asset'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('policy_no', 'like', '%'.$this->search.'%')
                        ->orWhereHas('insurance', function ($iq) {
                            $iq->where('name', 'like', '%'.$this->search.'%');
                        })
                        ->orWhereHas('asset', function ($aq) {
                            $aq->where('name', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->when($this->statusFilter === InsuranceStatus::ACTIVE->value, function ($query) {
                $query->where('status', InsuranceStatus::ACTIVE->value);
            })
            ->when($this->statusFilter === InsuranceStatus::INACTIVE->value, function ($query) {
                $query->where('status', InsuranceStatus::INACTIVE->value);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.insurance-policies.table', compact('policies'));
    }
}
