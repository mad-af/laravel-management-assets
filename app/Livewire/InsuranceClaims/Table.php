<?php

namespace App\Livewire\InsuranceClaims;

use App\Models\InsuranceClaim;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Table extends Component
{
    use Toast, WithPagination;

    public $search = '';

    public $statusFilter = '';

    protected $queryString = ['search', 'statusFilter'];

    protected $listeners = [
        'claim-saved' => '$refresh',
        'claim-deleted' => '$refresh',
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

    public function openEditDrawer($claimId)
    {
        $this->dispatch('open-edit-drawer', claimId: $claimId);
    }

    public function delete($claimId)
    {
        $claim = InsuranceClaim::find($claimId);
        if ($claim) {
            $claim->delete();
            $this->success('Klaim berhasil dihapus');
            $this->dispatch('claim-deleted');
        }
    }

    public function render()
    {
        $claims = InsuranceClaim::query()
            ->with(['policy.insurance', 'asset'])
            ->when($this->search, function ($query) {
                $search = '%'.$this->search.'%';
                $query->where('claim_no', 'like', $search)
                    ->orWhereHas('policy', function ($q) use ($search) {
                        $q->where('policy_no', 'like', $search)
                            ->orWhereHas('insurance', function ($iq) use ($search) {
                                $iq->where('name', 'like', $search);
                            });
                    })
                    ->orWhereHas('asset', function ($aq) use ($search) {
                        $aq->where('name', 'like', $search);
                    });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.insurance-claims.table', compact('claims'));
    }
}
