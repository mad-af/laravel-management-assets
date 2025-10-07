<?php

namespace App\Livewire\VehicleTaxes;

use App\Models\Asset;
use App\Models\VehicleTaxHistory;
use App\Models\VehicleTaxType;
use Livewire\Component;
use Mary\Traits\Toast;

class TaxPaymentForm extends Component
{
    use Toast;

    // State
    public ?string $vehicleTaxId = null;

    public ?string $asset_id = null;
    public ?string $vehicle_tax_type_id = null;
    public ?string $paid_date = null;
    public ?int $year = null;
    public ?string $amount = null;
    public ?string $receipt_no = null;
    public string $notes = '';

    public bool $isEdit = false;

    // Dropdown sources
    public array $assets = [];
    public array $vehicleTaxTypes = [];

    protected $rules = [
        'asset_id' => 'required|uuid|exists:assets,id',
        'vehicle_tax_type_id' => 'required|uuid|exists:vehicle_tax_types,id',
        'paid_date' => 'required|date',
        'year' => 'required|integer|min:2000|max:2099',
        'amount' => 'required|numeric|min:0',
        'receipt_no' => 'nullable|string|max:255',
        'notes' => 'nullable|string|max:1000',
    ];

    protected $listeners = [
        'editVehicleTax' => 'edit',
        'resetForm' => 'resetForm',
    ];

    public function mount(?string $vehicleTaxId = null): void
    {
        $this->vehicleTaxId = $vehicleTaxId;
        $this->year = date('Y'); // Set default year to current year

        // Load dropdown data
        $this->loadAssets();
        $this->loadVehicleTaxTypes();

        if ($vehicleTaxId) {
            $this->isEdit = true;
            $this->loadVehicleTaxHistory();
        }
    }

    /**
     * Load assets for dropdown
     */
    protected function loadAssets(): void
    {
        $this->assets = Asset::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code'])
            ->map(function ($asset) {
                return [
                    'id' => $asset->id,
                    'name' => $asset->name . ' (' . $asset->code . ')',
                ];
            })
            ->toArray();
    }

    /**
     * Load vehicle tax types for dropdown
     */
    protected function loadVehicleTaxTypes(): void
    {
        $this->vehicleTaxTypes = VehicleTaxType::query()
            ->orderBy('tax_type')
            ->get(['id', 'tax_type'])
            ->map(function ($taxType) {
                return [
                    'id' => $taxType->id,
                    'tax_type' => $taxType->tax_type,
                ];
            })
            ->toArray();
    }

    /**
     * Load vehicle tax history data when editing
     */
    protected function loadVehicleTaxHistory(): void
    {
        $vehicleTaxHistory = VehicleTaxHistory::find($this->vehicleTaxId);

        if (! $vehicleTaxHistory) {
            return;
        }

        $this->asset_id = $vehicleTaxHistory->asset_id;
        $this->vehicle_tax_type_id = $vehicleTaxHistory->vehicle_tax_type_id;
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
            $data = [
                'asset_id' => $this->asset_id,
                'vehicle_tax_type_id' => $this->vehicle_tax_type_id,
                'paid_date' => $this->paid_date,
                'year' => $this->year,
                'amount' => $this->amount,
                'receipt_no' => $this->receipt_no,
                'notes' => $this->notes,
            ];

            if ($this->isEdit && $this->vehicleTaxId) {
                $vehicleTaxHistory = VehicleTaxHistory::findOrFail($this->vehicleTaxId);
                $vehicleTaxHistory->update($data);

                $this->success('Riwayat pajak berhasil diperbarui!');
                $this->dispatch('vehicle-tax-updated');
            } else {
                VehicleTaxHistory::create($data);

                $this->success('Riwayat pajak berhasil ditambahkan!');
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

    public function resetForm(): void
    {
        $this->reset([
            'vehicleTaxId', 'asset_id', 'vehicle_tax_type_id', 'paid_date',
            'year', 'amount', 'receipt_no', 'notes', 'isEdit',
        ]);

        $this->year = date('Y'); // Reset year to current year
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.vehicle-taxes.tax-payment-form');
    }
}