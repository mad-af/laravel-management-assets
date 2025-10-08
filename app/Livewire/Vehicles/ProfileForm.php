<?php

namespace App\Livewire\Vehicles;

use App\Models\Asset;
use App\Models\Category;
use App\Models\VehicleProfile;
use App\Support\SessionKey;
use App\Traits\WithAlert;
use Livewire\Component;
use Mary\Traits\Toast;

class ProfileForm extends Component
{
    use Toast, WithAlert;

    public $assetId;

    public $asset_id = '';

    public $year_purchase = '';

    public $year_manufacture = '';

    public $current_odometer_km = '';

    public $last_service_date = '';

    public $service_target_odometer_km = '';

    public $next_service_date = '';

    public $plate_no = '';

    public $vin = '';

    public $isEdit = false;

    protected function rules()
    {
        $currentYear = date('Y');

        return [
            'asset_id' => 'required|exists:assets,id',
            'year_purchase' => 'nullable|integer|min:1900|max:'.$currentYear,
            'year_manufacture' => 'nullable|integer|min:1900|max:'.($currentYear + 1),
            'current_odometer_km' => 'nullable|integer|min:0',
            'last_service_date' => 'nullable|date',
            'service_target_odometer_km' => 'nullable|integer|min:0',
            'next_service_date' => 'nullable|date',
            'plate_no' => 'nullable|string|max:20',
            'vin' => 'nullable|string|max:50',
        ];
    }

    protected $listeners = [
        'editVehicle' => 'edit',
        'resetForm' => 'resetForm',
    ];

    public function updatedAssetId($value)
    {
        // keep event dispatch as-is
        $this->dispatch('asset-id-changed', $value);

        // sync aliases
        $this->asset_id = $value;
        $this->assetId = $value;

        // treat empty/blank as reset
        if (\Illuminate\Support\Str::of((string) $value)->trim()->isEmpty()) {
            $this->resetForm();

            return;
        }

        $this->loadVehicle($value);
    }

    public function mount($assetId = null)
    {
        $this->asset_id = $assetId;

        if ($assetId) {
            $this->loadVehicle();
        }
    }

    public function loadVehicle($param = null)
    {
        if ($param !== null) {
            $this->assetId = $param;
            $this->asset_id = $param; // keep in sync
        }

        if (! $this->assetId) {
            return;
        }

        $vehicle = Asset::with('vehicleProfile')->find($this->assetId)?->vehicleProfile;

        if ($vehicle) {
            $this->isEdit = true;

            $this->year_purchase = $vehicle->year_purchase;
            $this->year_manufacture = $vehicle->year_manufacture;
            $this->current_odometer_km = $vehicle->current_odometer_km;
            $this->last_service_date = optional($vehicle->last_service_date)->format('Y-m-d');
            $this->service_target_odometer_km = $vehicle->service_target_odometer_km;
            $this->next_service_date = optional($vehicle->next_service_date)->format('Y-m-d');
            $this->plate_no = $vehicle->plate_no;
            $this->vin = $vehicle->vin;
        } else {
            $this->resetForm();
        }
    }

    public function resetForm()
    {
        $this->year_purchase = '';
        $this->year_manufacture = '';
        $this->current_odometer_km = '';
        $this->last_service_date = '';
        $this->service_target_odometer_km = '';
        $this->next_service_date = '';
        $this->plate_no = '';
        $this->vin = '';
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
                'current_odometer_km' => $this->current_odometer_km ?: 0,
                'last_service_date' => $this->last_service_date ?: null,
                'service_target_odometer_km' => $this->service_target_odometer_km ?: null,
                'next_service_date' => $this->next_service_date ?: null,
                'plate_no' => $this->plate_no,
                'vin' => $this->vin,
            ];

            if ($this->isEdit) {
                $vehicleProfile = VehicleProfile::where('asset_id', $this->assetId)->first();
                $vehicleProfile->update($data);
                $this->showSuccessAlert('Profil kendaraan berhasil diperbarui.', 'Berhasil');
                $this->dispatch('vehicle-updated');
            } else {
                VehicleProfile::create(array_merge($data, ['asset_id' => $this->assetId]));
                $this->showSuccessAlert('Profil kendaraan berhasil dibuat.', 'Berhasil');
                $this->dispatch('vehicle-saved');
            }

            $this->resetForm();
            $this->dispatch('close-drawer');
        } catch (\Exception $e) {
            dd($e);
            $this->showErrorAlert('Gagal menyimpan profil kendaraan: '.$e->getMessage(), 'Error');
        }
    }

    public function render()
    {
        // asumsi $vehicleCategory sudah diambil seperti di bawah
        $currentBranchId = session_get(SessionKey::BranchId);
        $vehicleCategory = Category::where('name', 'Kendaraan')->first();

        $assets = Asset::query()
            ->with(['branch'])
            ->when($currentBranchId, function ($query) use ($currentBranchId) {
                $query->where('branch_id', $currentBranchId);
            })
            ->where('category_id', $vehicleCategory?->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($asset) {
                $asset->display_name = $asset->name.' ('.$asset->code.')';

                return $asset;
            });

        return view('livewire.vehicles.profile-form', compact('assets'))
            ->with('assetId', $this->assetId);
    }
}
