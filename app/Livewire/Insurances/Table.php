<?php

namespace App\Livewire\Insurances;

use App\Models\Insurance;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Table extends Component
{
    use Toast, WithPagination;

    public $search = '';

    public int $perPage = 10;

    protected $queryString = ['search'];

    protected $listeners = [
        'insurance-saved' => '$refresh',
        'insurance-updated' => '$refresh',
        'insurance-deleted' => '$refresh',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openDrawer()
    {
        $this->dispatch('open-drawer');
    }

    public function openEditDrawer($insuranceId)
    {
        $this->dispatch('open-edit-drawer', insuranceId: $insuranceId);
    }

    public function delete($insuranceId)
    {
        try {
            Insurance::findOrFail($insuranceId)->delete();
            $this->success('Provider asuransi berhasil dihapus!');
            $this->dispatch('insurance-deleted');
        } catch (\Throwable $e) {
            $this->error('Gagal menghapus provider: '.$e->getMessage());
        }
    }

    public function render()
    {
        $insurances = Insurance::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhere('phone', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.insurances.table', compact('insurances'));
    }
}
