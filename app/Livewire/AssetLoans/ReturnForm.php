<?php

namespace App\Livewire\AssetLoans;

use App\Enums\AssetCondition;
use App\Models\AssetLoan;
use Livewire\Component;
use Mary\Traits\Toast;

class ReturnForm extends Component
{
    use Toast;

    public ?string $assetLoanId = null;

    public ?string $checkin_at = null;

    public ?string $condition_in = null;

    public ?string $notes = null;

    public array $conditions = [];

    public ?AssetLoan $loan = null;

    protected $rules = [
        'checkin_at' => 'required|date',
        'condition_in' => 'required',
        'notes' => 'nullable|string',
    ];

    public function mount($assetLoanId = null)
    {
        $this->assetLoanId = $assetLoanId;

        // Build condition options
        $this->conditions = collect(AssetCondition::cases())
            ->map(fn($c) => ['value' => $c->value, 'label' => $c->label()])
            ->toArray();

        if ($assetLoanId) {
            $this->loan = AssetLoan::with(['asset', 'employee'])->find($assetLoanId);
            if ($this->loan) {
                $this->checkin_at = $this->loan->checkin_at?->format('Y-m-d') ?? now()->format('Y-m-d');
                $this->condition_in = $this->loan->condition_in?->value ?? $this->loan->condition_out?->value;
                $this->notes = $this->loan->notes;
            }
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $loan = AssetLoan::find($this->assetLoanId);
            if (! $loan) {
                $this->error('Data pinjaman tidak ditemukan');
                return;
            }

            $loan->update([
                'checkin_at' => $this->checkin_at,
                'condition_in' => $this->condition_in,
                'notes' => $this->notes ?: null,
            ]);

            $this->success('Pengembalian asset berhasil disimpan!');
            $this->dispatch('table-refresh');
            $this->dispatch('close-drawer');

        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.asset-loans.return-form');
    }
}