<?php

namespace App\Livewire\VehicleTaxes;

use App\Enums\VehicleTaxTypeEnum;
use App\Models\Asset;
use App\Models\VehicleTaxType;
use Illuminate\Support\Facades\DB;
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

    // Konfirmasi frasa agar tombol submit aktif
    public string $confirmation_text = '';

    protected $rules = [
        'asset_id' => 'required|uuid|exists:assets,id',
        'due_date' => 'nullable|date|required_if:is_pajak_tahunan,true',
        'due_date_kir' => 'nullable|date|required_if:is_kir,true',
    ];

    protected $listeners = [
        'editVehicleTaxType' => 'edit',
        'resetForm' => 'resetForm',
    ];

    // Computed: apakah frasa konfirmasi cocok
    public function getIsConfirmedProperty(): bool
    {
        return $this->confirmation_text === 'Data telah saya verifikasi';
    }

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

            DB::transaction(function () {
                // Jika TIDAK ada pajak aktif → pastikan hanya TIDAK_BERPAJAK yang tersisa
                if (! $this->is_pajak_tahunan && ! $this->is_kir) {
                    // Hapus PKB & KIR jika ada
                    VehicleTaxType::query()
                        ->where('asset_id', $this->asset_id)
                        ->whereIn('tax_type', [
                            VehicleTaxTypeEnum::PKB_TAHUNAN->value,
                            VehicleTaxTypeEnum::KIR->value,
                        ])->delete();

                    // Reset id di state
                    $this->pkb_tax_type_id = null;
                    $this->kir_tax_type_id = null;

                    // Upsert TIDAK_BERPAJAK (due_date null)
                    $noTax = VehicleTaxType::updateOrCreate(
                        [
                            'asset_id' => $this->asset_id,
                            'tax_type' => VehicleTaxTypeEnum::TIDAK_BERPAJAK->value,
                        ],
                        ['due_date' => null]
                    );

                    return; // selesai skenario no-tax
                }

                // Ada pajak aktif → hapus baris TIDAK_BERPAJAK bila ada
                VehicleTaxType::query()
                    ->where('asset_id', $this->asset_id)
                    ->where('tax_type', VehicleTaxTypeEnum::TIDAK_BERPAJAK->value)
                    ->delete();

                // ===== PKB Tahunan =====
                if ($this->is_pajak_tahunan) {
                    $pkb = VehicleTaxType::updateOrCreate(
                        [
                            'asset_id' => $this->asset_id,
                            'tax_type' => VehicleTaxTypeEnum::PKB_TAHUNAN->value,
                        ],
                        [
                            'due_date' => $this->due_date,
                        ]
                    );

                    // simpan id ke state (berguna untuk UI/validasi berikutnya)
                    $this->pkb_tax_type_id = $pkb->id;
                } else {
                    // nonaktif → hapus bila ada & reset id
                    VehicleTaxType::query()
                        ->where('asset_id', $this->asset_id)
                        ->where('tax_type', VehicleTaxTypeEnum::PKB_TAHUNAN->value)
                        ->delete();
                    $this->pkb_tax_type_id = null;
                }

                // ===== KIR =====
                if ($this->is_kir) {
                    $kir = VehicleTaxType::updateOrCreate(
                        [
                            'asset_id' => $this->asset_id,
                            'tax_type' => VehicleTaxTypeEnum::KIR->value,
                        ],
                        [
                            'due_date' => $this->due_date_kir,
                        ]
                    );

                    $this->kir_tax_type_id = $kir->id;
                } else {
                    VehicleTaxType::query()
                        ->where('asset_id', $this->asset_id)
                        ->where('tax_type', VehicleTaxTypeEnum::KIR->value)
                        ->delete();
                    $this->kir_tax_type_id = null;
                }
            });

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

        $this->confirmation_text = '';

        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.vehicle-taxes.tax-type-form');
    }
}
