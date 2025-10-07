<?php

namespace App\Livewire\VehicleTaxes;

use App\Enums\VehicleTaxTypeEnum;
use App\Models\Asset;
use App\Models\VehicleTaxType;
use Livewire\Attributes\Url;
use Livewire\Component;
use Mary\Traits\Toast;

class TaxTypeForm extends Component
{
    use Toast;

    #[Url(as: 'asset_id')] // ?asset_id=123
    public ?string $asset_id = null;

    public ?string $tax_type = null;

    public ?string $due_date = null;

    public bool $isEdit = false;

    // Dropdown sources
    public array $assets = [];

    public array $taxTypeOptions = [];

    public bool $is_kir = false;

    protected $rules = [
        'asset_id' => 'required|uuid|exists:assets,id',
        'tax_type' => 'required|string|max:255',
        'due_date' => 'required|date',
    ];

    protected $listeners = [
        'editVehicleTaxType' => 'edit',
        'resetForm' => 'resetForm',
    ];

    public function mount(?string $assetId = null): void
    {
        $this->asset_id = $assetId;

        // Load dropdown data
        $this->loadAssets();
        $this->loadTaxTypeOptions();

        // if ($asset_id) {
        //     $this->isEdit = true;
        //     $this->loadVehicleTaxType();
        // }
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
     * Load tax type options from enum
     */
    protected function loadTaxTypeOptions(): void
    {
        $this->taxTypeOptions = VehicleTaxTypeEnum::options();
    }

    /**
     * Load vehicle tax type data when editing
     */
    protected function loadVehicleTaxType(): void
    {
        $vehicleTaxType = VehicleTaxType::find($this->vehicleTaxTypeId);

        if (! $vehicleTaxType) {
            return;
        }

        $this->asset_id = $vehicleTaxType->asset_id;
        $this->tax_type = $vehicleTaxType->tax_type?->value;
        $this->due_date = $vehicleTaxType->due_date?->format('Y-m-d');
    }

    public function save(): void
    {
        $this->validate();

        try {
            $data = [
                'asset_id' => $this->asset_id,
                'tax_type' => $this->tax_type,
                'due_date' => $this->due_date,
            ];

            if ($this->isEdit && $this->vehicleTaxTypeId) {
                $vehicleTaxType = VehicleTaxType::findOrFail($this->vehicleTaxTypeId);
                $vehicleTaxType->update($data);

                $this->success('Jenis pajak berhasil diperbarui!');
                $this->dispatch('vehicle-tax-type-updated');
            } else {
                VehicleTaxType::create($data);

                $this->success('Jenis pajak berhasil ditambahkan!');
                $this->dispatch('vehicle-tax-type-saved');
                $this->resetForm();
            }
        } catch (\Throwable $e) {
            $this->error('Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function edit(string $vehicleTaxTypeId): void
    {
        // $this->vehicleTaxTypeId = $vehicleTaxTypeId;
        $this->isEdit = true;
        $this->loadVehicleTaxType();
    }

    public function resetForm(): void
    {
        $this->reset([
            'vehicleTaxTypeId', 'asset_id', 'tax_type', 'due_date', 'isEdit',
        ]);

        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.vehicle-taxes.tax-type-form');
    }
}
