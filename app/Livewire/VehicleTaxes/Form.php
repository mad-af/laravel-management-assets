<?php

namespace App\Livewire\VehicleTaxes;

use App\Models\Asset;
use App\Models\VehicleTax;
use Livewire\Component;
use Mary\Traits\Toast;

class Form extends Component
{
    use Toast;

    // State

    public $vehicleTaxes = [];
    public ?string $vehicleTaxId = null;

    public ?string $asset_id = null;

    public ?string $tax_period_start = null;

    public ?string $tax_period_end = null;

    public ?string $due_date = null;

    public ?string $payment_date = null;

    public ?string $amount = null;

    public ?string $receipt_no = null;

    public string $notes = '';

    public bool $isEdit = false;

    // Dropdown sources
    public array $assets = [];

    protected $rules = [
        'asset_id' => 'required|uuid|exists:assets,id',
        'tax_period_start' => 'required|date',
        'tax_period_end' => 'required|date|after_or_equal:tax_period_start',
        'due_date' => 'required|date',
        'payment_date' => 'nullable|date',
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

        // Load assets
        $this->loadAssets();

        if ($vehicleTaxId) {
            $this->isEdit = true;
            $this->loadVehicleTax();
        }
    }

    /**
     * Ambil daftar asset untuk dropdown
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
     * Load data vehicle tax saat edit
     */
    protected function loadVehicleTax(): void
    {
        $vehicleTax = VehicleTax::find($this->vehicleTaxId);

        if (! $vehicleTax) {
            return;
        }

        $this->asset_id = $vehicleTax->asset_id;
        $this->tax_period_start = $vehicleTax->tax_period_start?->format('Y-m-d');
        $this->tax_period_end = $vehicleTax->tax_period_end?->format('Y-m-d');
        $this->due_date = $vehicleTax->due_date?->format('Y-m-d');
        $this->payment_date = $vehicleTax->payment_date?->format('Y-m-d');
        $this->amount = $vehicleTax->amount;
        $this->receipt_no = $vehicleTax->receipt_no;
        $this->notes = $vehicleTax->notes ?? '';
    }

    public function save(): void
    {
        $this->validate();

        try {
            $data = [
                'asset_id' => $this->asset_id,
                'tax_period_start' => $this->tax_period_start,
                'tax_period_end' => $this->tax_period_end,
                'due_date' => $this->due_date,
                'payment_date' => $this->payment_date,
                'amount' => $this->amount,
                'receipt_no' => $this->receipt_no,
                'notes' => $this->notes,
            ];

            if ($this->isEdit && $this->vehicleTaxId) {
                $vehicleTax = VehicleTax::findOrFail($this->vehicleTaxId);
                $vehicleTax->update($data);

                $this->success('Vehicle tax updated successfully!');
                $this->dispatch('vehicle-tax-updated');
            } else {
                VehicleTax::create($data);

                $this->success('Vehicle tax created successfully!');
                $this->dispatch('vehicle-tax-saved');
                $this->resetForm();
            }
        } catch (\Throwable $e) {
            $this->error('An error occurred: '.$e->getMessage());
        }
    }

    public function edit(string $vehicleTaxId): void
    {
        $this->vehicleTaxId = $vehicleTaxId;
        $this->isEdit = true;
        $this->loadVehicleTax();
    }

    public function resetForm(): void
    {
        $this->reset([
            'vehicleTaxId', 'asset_id', 'tax_period_start', 'tax_period_end',
            'due_date', 'payment_date', 'amount', 'receipt_no', 'notes', 'isEdit',
        ]);

        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.vehicle-taxes.form');
    }
}