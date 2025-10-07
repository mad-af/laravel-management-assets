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

    public ?string $due_date_kir = null;

    public ?string $pkb_tax_type_id = null;

    public ?string $kir_tax_type_id = null;

    public bool $isEdit = false;

    // Dropdown sources
    public array $assets = [];

    public array $taxTypeOptions = [];

    public bool $is_kir = false;

    protected $rules = [
        'asset_id' => 'required|uuid|exists:assets,id',
        'due_date' => 'required|date',
        'due_date_kir' => 'nullable|date|required_if:is_kir,true',
    ];

    protected $listeners = [
        'editVehicleTaxType' => 'edit',
        'resetForm' => 'resetForm',
    ];

    public function updatedAssetId()
    {
        if ($this->asset_id) {
            $this->loadVehicleTaxType();
        } else {
            $this->resetForm();
        }
    }

    public function mount(?string $assetId = null): void
    {
        $this->asset_id = $assetId;

        // Load dropdown data
        $this->loadAssets();
        $this->loadTaxTypeOptions();
        $this->loadVehicleTaxType();
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
        if (! $this->asset_id) {
            return;
        }

        // Load existing PKB tax type
        $pkbTaxType = VehicleTaxType::where('asset_id', $this->asset_id)
            ->where('tax_type', VehicleTaxTypeEnum::PKB_TAHUNAN)
            ->first();

        if ($pkbTaxType) {
            $this->pkb_tax_type_id = $pkbTaxType->id;
            $this->due_date = $pkbTaxType->due_date?->format('Y-m-d');
        }

        // Load existing KIR tax type
        $kirTaxType = VehicleTaxType::where('asset_id', $this->asset_id)
            ->where('tax_type', VehicleTaxTypeEnum::KIR)
            ->first();

        if ($kirTaxType) {
            $this->kir_tax_type_id = $kirTaxType->id;
            $this->due_date_kir = $kirTaxType->due_date?->format('Y-m-d');
            $this->is_kir = true;
        }
    }

    public function save(): void
    {
        $this->validate();

        try {
            // Handle PKB tax type (always created/updated)
            if ($this->pkb_tax_type_id) {
                // Update existing PKB tax type
                VehicleTaxType::where('id', $this->pkb_tax_type_id)->update([
                    'asset_id' => $this->asset_id,
                    'tax_type' => VehicleTaxTypeEnum::PKB_TAHUNAN,
                    'due_date' => $this->due_date,
                ]);
            } else {
                // Create new PKB tax type
                VehicleTaxType::create([
                    'asset_id' => $this->asset_id,
                    'tax_type' => VehicleTaxTypeEnum::PKB_TAHUNAN,
                    'due_date' => $this->due_date,
                ]);
            }

            // Handle KIR tax type (only if is_kir is enabled)
            if ($this->is_kir) {
                if ($this->kir_tax_type_id) {
                    // Update existing KIR tax type
                    VehicleTaxType::where('id', $this->kir_tax_type_id)->update([
                        'asset_id' => $this->asset_id,
                        'tax_type' => VehicleTaxTypeEnum::KIR,
                        'due_date' => $this->due_date_kir,
                    ]);
                } else {
                    // Create new KIR tax type
                    VehicleTaxType::create([
                        'asset_id' => $this->asset_id,
                        'tax_type' => VehicleTaxTypeEnum::KIR,
                        'due_date' => $this->due_date_kir,
                    ]);
                }
            } else {
                // If KIR is disabled but exists, delete it
                if ($this->kir_tax_type_id) {
                    VehicleTaxType::where('id', $this->kir_tax_type_id)->delete();
                    $this->kir_tax_type_id = null;
                }
            }

            $this->success('Data pajak kendaraan berhasil disimpan!');
            $this->dispatch('close-drawer');
            $this->resetForm();
            $this->dispatch('reload-page');
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
        $this->asset_id = null;
        $this->reset([
            'asset_id', 'due_date', 'due_date_kir', 'is_kir', 'isEdit',
        ]);

        $this->pkb_tax_type_id = null;
        $this->kir_tax_type_id = null;

        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.vehicle-taxes.tax-type-form');
    }
}
