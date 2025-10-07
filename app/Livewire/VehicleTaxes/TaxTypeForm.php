<?php

namespace App\Livewire\VehicleTaxes;

use App\Models\Asset;
use App\Models\VehicleTaxType;
use Livewire\Component;
use Mary\Traits\Toast;

class TaxTypeForm extends Component
{
    use Toast;

    public ?string $asset_id = null;
    public ?string $tax_type = null;
    public ?string $due_date = null;

    public bool $isEdit = false;

    // Dropdown sources
    public array $assets = [];

    protected $rules = [
        'asset_id' => 'required|uuid|exists:assets,id',
        'tax_type' => 'required|string|max:255',
        'due_date' => 'required|date',
    ];

    protected $listeners = [
        'editVehicleTaxType' => 'edit',
        'resetForm' => 'resetForm',
    ];

    public function mount(?string $asset_id = null): void
    {
        $this->asset_id = $asset_id;

        // Load dropdown data
        $this->loadAssets();

        if ($asset_id) {
            $this->isEdit = true;
            $this->loadVehicleTaxType();
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
     * Load vehicle tax type data when editing
     */
    protected function loadVehicleTaxType(): void
    {
        $vehicleTaxType = VehicleTaxType::find($this->vehicleTaxTypeId);

        if (! $vehicleTaxType) {
            return;
        }

        $this->asset_id = $vehicleTaxType->asset_id;
        $this->tax_type = $vehicleTaxType->tax_type;
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