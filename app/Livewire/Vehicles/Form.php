<?php

namespace App\Livewire\Vehicles;

use App\Models\VehicleProfile;
use App\Models\Asset;
use App\Traits\WithAlert;
use Livewire\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Auth;

class Form extends Component
{
    use Toast, WithAlert;

    public $vehicleId;
    public $asset_id = '';
    public $year_purchase = '';
    public $year_manufacture = '';
    public $current_odometer_km = '';
    public $last_service_date = '';
    public $service_interval_km = '';
    public $service_interval_days = '';
    public $service_target_odometer_km = '';
    public $next_service_date = '';
    public $annual_tax_due_date = '';
    public $plate_no = '';
    public $vin = '';
    public $brand = '';
    public $model = '';
    public $isEdit = false;

    protected function rules()
    {
        $currentYear = date('Y');
        return [
            'asset_id' => 'required|exists:assets,id',
            'year_purchase' => 'nullable|integer|min:1900|max:' . ($currentYear + 1),
            'year_manufacture' => 'nullable|integer|min:1900|max:' . ($currentYear + 1),
            'current_odometer_km' => 'nullable|integer|min:0',
            'last_service_date' => 'nullable|date',
            'service_interval_km' => 'nullable|integer|min:1',
            'service_interval_days' => 'nullable|integer|min:1',
            'service_target_odometer_km' => 'nullable|integer|min:0',
            'next_service_date' => 'nullable|date',
            'annual_tax_due_date' => 'nullable|date',
            'plate_no' => 'required|string|max:20',
            'vin' => 'nullable|string|max:50',
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
        ];
    }

    protected $listeners = [
        'editVehicle' => 'edit',
        'resetForm' => 'resetForm'
    ];

    public function mount($vehicleId = null)
    {
        $this->vehicleId = $vehicleId;
        
        if ($vehicleId) {
            $this->isEdit = true;
            $this->loadVehicle();
        }
    }



    public function loadVehicle()
    {
        if ($this->vehicleId) {
            $vehicle = VehicleProfile::find($this->vehicleId);
            if ($vehicle) {
                $this->asset_id = $vehicle->asset_id;
                $this->year_purchase = $vehicle->year_purchase;
                $this->year_manufacture = $vehicle->year_manufacture;
                $this->current_odometer_km = $vehicle->current_odometer_km;
                $this->last_service_date = $vehicle->last_service_date?->format('Y-m-d');
                $this->service_interval_km = $vehicle->service_interval_km;
                $this->service_interval_days = $vehicle->service_interval_days;
                $this->service_target_odometer_km = $vehicle->service_target_odometer_km;
                $this->next_service_date = $vehicle->next_service_date?->format('Y-m-d');
                $this->annual_tax_due_date = $vehicle->annual_tax_due_date?->format('Y-m-d');
                $this->plate_no = $vehicle->plate_no;
                $this->vin = $vehicle->vin;
                $this->brand = $vehicle->brand;
                $this->model = $vehicle->model;
            }
        }
    }

    public function resetForm()
    {
        $this->asset_id = '';
        $this->year_purchase = '';
        $this->year_manufacture = '';
        $this->current_odometer_km = '';
        $this->last_service_date = '';
        $this->service_interval_km = '';
        $this->service_interval_days = '';
        $this->service_target_odometer_km = '';
        $this->next_service_date = '';
        $this->annual_tax_due_date = '';
        $this->plate_no = '';
        $this->vin = '';
        $this->brand = '';
        $this->model = '';
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'asset_id' => $this->asset_id,
                'year_purchase' => $this->year_purchase ?: null,
                'year_manufacture' => $this->year_manufacture ?: null,
                'current_odometer_km' => $this->current_odometer_km ?: null,
                'last_service_date' => $this->last_service_date ?: null,
                'service_interval_km' => $this->service_interval_km ?: null,
                'service_interval_days' => $this->service_interval_days ?: null,
                'service_target_odometer_km' => $this->service_target_odometer_km ?: null,
                'next_service_date' => $this->next_service_date ?: null,
                'annual_tax_due_date' => $this->annual_tax_due_date ?: null,
                'plate_no' => $this->plate_no,
                'vin' => $this->vin,
                'brand' => $this->brand,
                'model' => $this->model,
            ];

            if ($this->isEdit) {
                $vehicle = VehicleProfile::find($this->vehicleId);
                $vehicle->update($data);
                $this->showSuccessAlert('Profil kendaraan berhasil diperbarui.', 'Berhasil');
                $this->dispatch('vehicle-updated');
            } else {
                VehicleProfile::create($data);
                $this->showSuccessAlert('Profil kendaraan berhasil dibuat.', 'Berhasil');
                $this->dispatch('vehicle-saved');
            }

            $this->resetForm();
        } catch (\Exception $e) {
            $this->showErrorAlert('Gagal menyimpan profil kendaraan: ' . $e->getMessage(), 'Error');
        }
    }

    public function render()
    {
        $assets = Asset::where('company_id', Auth::user()?->company_id)
            ->whereDoesntHave('vehicleProfile')
            ->orWhere('id', $this->asset_id)
            ->get()
            ->map(function ($asset) {
                $asset->display_name = $asset->name . ' (' . $asset->asset_code . ')';
                return $asset;
            });
        
        return view('livewire.vehicles.form', compact('assets'))
            ->with('vehicleId', $this->vehicleId)
            ->with('isEdit', $this->isEdit);
    }
}