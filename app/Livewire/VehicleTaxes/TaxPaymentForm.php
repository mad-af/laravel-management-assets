<?php

namespace App\Livewire\VehicleTaxes;

use App\Models\Asset;
use App\Models\VehicleTaxHistory;
use Livewire\Attributes\Url;
use Livewire\Component;
use Mary\Traits\Toast;

class TaxPaymentForm extends Component
{
    use Toast;

    // State
    #[Url('asset_id')]
    public ?string $asset_id = null;

    public ?string $vehicle_tax_history_id = null;

    public ?string $paid_date = null;

    public ?int $year = null;

    public ?string $amount = null;

    public ?string $receipt_no = null;

    public string $notes = '';

    public ?string $vehicleTaxId = null;

    public bool $isEdit = false;

    // Dropdown sources
    public array $assets = [];

    public array $vehicleTaxHistories = [];

    protected $rules = [
        'asset_id' => 'required|uuid|exists:assets,id',
        'vehicle_tax_history_id' => 'required|uuid|exists:vehicle_tax_histories,id',
        'paid_date' => 'required|date',
        'year' => 'required|integer|min:2000|max:2099',
        'amount' => 'required|numeric|min:0',
        'receipt_no' => 'nullable|string|max:255',
        'notes' => 'nullable|string|max:1000',
    ];

    protected $listeners = [
        'editVehicleTax' => 'edit',
        'openTaxPaymentForm' => 'openForm',
        'resetForm' => 'resetForm',
    ];

    public function mount(?string $assetId = null): void
    {
        $this->asset_id = $assetId;
        $this->year = date('Y'); // Set default year to current year

        // Load dropdown data
        $this->loadAssets();
        $this->loadVehicleTaxHistories();
    }

    /**
     * Handle asset selection change
     */
    public function updatedAssetId(): void
    {
        $this->vehicle_tax_history_id = null;
        $this->resetFormFields();
        $this->loadVehicleTaxHistories();
    }

    /**
     * Handle vehicle tax type selection change
     */
    public function updatedVehicleTaxHistoryId(): void
    {
        if ($this->vehicle_tax_history_id) {
            $this->populateFormFromTaxHistory();
        } else {
            $this->resetFormFields();
        }
    }

    /**
     * Load assets for dropdown
     */
    protected function loadAssets(): void
    {
        $branchId = session('selected_branch_id');

        $this->assets = Asset::vehicles()
            ->forBranch($branchId)
            ->orderBy('name')
            ->get(['id', 'name', 'code'])
            ->map(function ($asset) {
                return [
                    'id' => $asset->id,
                    'name' => $asset->name.' ('.$asset->code.')',
                ];
            })
            ->toArray();
    }

    /**
     * Load vehicle tax types for dropdown - only unpaid taxes for selected asset
     */
    protected function loadVehicleTaxHistories(): void
    {
        if (! $this->asset_id) {
            $this->vehicleTaxHistories = [];

            return;
        }

        // Get unpaid vehicle tax histories for the selected asset
        $unpaidTaxHistories = VehicleTaxHistory::with('vehicleTaxType')
            ->where('asset_id', $this->asset_id)
            ->whereNull('paid_date')
            ->whereNotNull('due_date')
            ->orderBy('due_date', 'asc')
            ->get();

        $this->vehicleTaxHistories = $unpaidTaxHistories->map(function ($taxHistory) {
            $status = $this->getTaxStatus($taxHistory);

            return [
                'id' => $taxHistory->id,
                'tax_type' => $taxHistory->vehicleTaxType->tax_type->label().' - '.$taxHistory->due_date->format('d/m/Y').' ('.$status.')',
            ];
        })->toArray();
    }

    /**
     * Populate form fields based on selected tax history
     */
    protected function populateFormFromTaxHistory(): void
    {
        $taxHistory = VehicleTaxHistory::find($this->vehicle_tax_history_id);

        if (! $taxHistory) {
            return;
        }

        $this->year = $taxHistory->year;
        // Set default paid date to today
        $this->paid_date = now()->format('Y-m-d');
        // Keep amount and receipt_no empty for user input
        $this->amount = null;
        $this->receipt_no = null;
        $this->notes = $taxHistory->notes ?? '';
    }

    /**
     * Reset form fields except asset_id and vehicle_tax_history_id
     */
    protected function resetFormFields(): void
    {
        $this->paid_date = null;
        $this->year = date('Y');
        $this->amount = null;
        $this->receipt_no = null;
        $this->notes = '';
    }

    /**
     * Load vehicle tax history data when editing
     */
    protected function loadVehicleTaxHistory(): void
    {
        if (! $this->vehicleTaxId) {
            return;
        }

        $vehicleTaxHistory = VehicleTaxHistory::find($this->vehicleTaxId);

        if (! $vehicleTaxHistory) {
            return;
        }

        $this->asset_id = $vehicleTaxHistory->asset_id;
        $this->vehicle_tax_history_id = $vehicleTaxHistory->vehicle_tax_type_id;
        $this->paid_date = $vehicleTaxHistory->paid_date?->format('Y-m-d');
        $this->year = $vehicleTaxHistory->year;
        $this->amount = $vehicleTaxHistory->amount;
        $this->receipt_no = $vehicleTaxHistory->receipt_no;
        $this->notes = $vehicleTaxHistory->notes ?? '';
    }

    public function save(): void
    {
        $this->validate();

        try {
            if ($this->isEdit && $this->vehicleTaxId) {
                // Update existing tax history
                $vehicleTaxHistory = VehicleTaxHistory::findOrFail($this->vehicleTaxId);
                $vehicleTaxHistory->update([
                    'paid_date' => $this->paid_date,
                    'year' => $this->year,
                    'amount' => $this->amount,
                    'receipt_no' => $this->receipt_no,
                    'notes' => $this->notes,
                ]);

                $this->success('Riwayat pajak berhasil diperbarui!');
                $this->dispatch('vehicle-tax-updated');
            } else {
                // Update the selected unpaid tax history with payment details
                $vehicleTaxHistory = VehicleTaxHistory::findOrFail($this->vehicle_tax_history_id);
                $vehicleTaxHistory->update([
                    'paid_date' => $this->paid_date,
                    'amount' => $this->amount,
                    'receipt_no' => $this->receipt_no,
                    'notes' => $this->notes,
                ]);

                $this->success('Pembayaran pajak berhasil dicatat!');
                $this->dispatch('vehicle-tax-saved');
                $this->resetForm();
            }
        } catch (\Throwable $e) {
            $this->error('Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function edit(string $vehicleTaxId): void
    {
        $this->vehicleTaxId = $vehicleTaxId;
        $this->isEdit = true;
        $this->loadVehicleTaxHistory();
    }

    public function openForm(?string $assetId = null, ?string $vehicleTaxId = null): void
    {
        $this->resetForm();

        if ($assetId) {
            $this->asset_id = $assetId;
        }

        if ($vehicleTaxId) {
            $this->vehicleTaxId = $vehicleTaxId;
            $this->isEdit = true;
            $this->loadVehicleTaxHistory();
        }
    }

    public function resetForm(): void
    {
        $this->reset([
            'vehicleTaxId', 'asset_id', 'vehicle_tax_history_id', 'paid_date',
            'year', 'amount', 'receipt_no', 'notes', 'isEdit',
        ]);

        $this->year = date('Y'); // Reset year to current year
        $this->resetValidation();
    }

    /**
     * Get simple tax status text for display
     */
    private function getTaxStatus($taxHistory): string
    {
        $dueDate = \Carbon\Carbon::parse($taxHistory->due_date);

        if ($taxHistory->paid_date) {
            return 'Dibayar';
        } elseif ($dueDate->isPast()) {
            return 'Terlambat';
        } elseif ($dueDate->diffInDays(now()) <= 30) {
            return 'Jatuh Tempo';
        } else {
            return 'Akan Datang';
        }
    }

    public function render()
    {
        return view('livewire.vehicle-taxes.tax-payment-form');
    }
}
