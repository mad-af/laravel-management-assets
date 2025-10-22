<?php

namespace App\Livewire\VehicleTaxes;

use App\Enums\VehicleTaxTypeEnum;
use App\Models\Asset;
use App\Models\VehicleTaxType;
use Livewire\Attributes\On;
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

    public bool $is_pajak_tahunan = false;

    public bool $is_kir = false;

    protected $rules = [
        'asset_id' => 'required|uuid|exists:assets,id',
        'due_date' => 'nullable|date|required_if:is_pajak_tahunan,true',
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
    #[On('combobox-load-assets')]
    public function loadAssets($search = '')
    {
        $query = Asset::forBranch()->vehicles();

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('code', 'like', "%$search%")
                    ->orWhere('tag_code', 'like', "%$search%");
            });
        }

        $this->assets = $query->orderBy('name')
            ->get(['id', 'name', 'code', 'tag_code', 'image'])
            ->toArray();

        $this->dispatch('combobox-set-assets', $this->assets);
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
        $pkbTaxType = VehicleTaxType::query()
            ->where('asset_id', $this->asset_id)
            ->where('tax_type', VehicleTaxTypeEnum::PKB_TAHUNAN->value)
            ->orderBy('due_date', 'desc')
            ->first();

        if ($pkbTaxType) {
            $this->pkb_tax_type_id = $pkbTaxType->id;
            $this->due_date = $pkbTaxType->due_date?->format('Y-m-d');
            $this->is_pajak_tahunan = true;
        } else {
            $this->is_pajak_tahunan = false;
        }

        // Load existing KIR tax type
        $kirTaxType = VehicleTaxType::query()
            ->where('asset_id', $this->asset_id)
            ->where('tax_type', VehicleTaxTypeEnum::KIR->value)
            ->orderBy('due_date', 'desc')
            ->first();

        if ($kirTaxType) {
            $this->kir_tax_type_id = $kirTaxType->id;
            $this->due_date_kir = $kirTaxType->due_date?->format('Y-m-d');
            $this->is_kir = true;
        } else {
            $this->is_kir = false;
        }
    }

    public function save(): void
    {
        $this->validate();

        try {
            // Jika kedua pajak tidak diaktifkan, pastikan ada 1 baris TIDAK_BERPAJAK dengan due_date null
            if (! $this->is_pajak_tahunan && ! $this->is_kir) {
                // Hapus PKB jika ada
                if ($this->pkb_tax_type_id) {
                    VehicleTaxType::where('id', $this->pkb_tax_type_id)->delete();
                    $this->pkb_tax_type_id = null;
                }
                // Hapus KIR jika ada
                if ($this->kir_tax_type_id) {
                    VehicleTaxType::where('id', $this->kir_tax_type_id)->delete();
                    $this->kir_tax_type_id = null;
                }

                // Upsert TIDAK_BERPAJAK
                $noTax = VehicleTaxType::query()
                    ->where('asset_id', $this->asset_id)
                    ->where('tax_type', VehicleTaxTypeEnum::TIDAK_BERPAJAK->value)
                    ->first();

                if ($noTax) {
                    $noTax->update([
                        'due_date' => null,
                    ]);
                } else {
                    VehicleTaxType::create([
                        'asset_id' => $this->asset_id,
                        'tax_type' => VehicleTaxTypeEnum::TIDAK_BERPAJAK,
                        'due_date' => null,
                    ]);
                }
            } else {
                // Ada pajak aktif: hapus baris TIDAK_BERPAJAK jika ada
                VehicleTaxType::query()
                    ->where('asset_id', $this->asset_id)
                    ->where('tax_type', VehicleTaxTypeEnum::TIDAK_BERPAJAK->value)
                    ->delete();

                // Handle PKB tax type jika diaktifkan
                if ($this->is_pajak_tahunan) {
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
                } else {
                    // Jika tidak diaktifkan, hapus PKB jika ada
                    if ($this->pkb_tax_type_id) {
                        VehicleTaxType::where('id', $this->pkb_tax_type_id)->delete();
                        $this->pkb_tax_type_id = null;
                    }
                }

                // Handle KIR tax type jika diaktifkan
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
                    // Jika tidak diaktifkan, hapus KIR jika ada
                    if ($this->kir_tax_type_id) {
                        VehicleTaxType::where('id', $this->kir_tax_type_id)->delete();
                        $this->kir_tax_type_id = null;
                    }
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
            'asset_id', 'due_date', 'due_date_kir', 'is_kir', 'is_pajak_tahunan', 'isEdit',
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
