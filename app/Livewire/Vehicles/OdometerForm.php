<?php

namespace App\Livewire\Vehicles;

use App\Models\VehicleOdometerLog;
use App\Models\Asset;
use App\Enums\VehicleOdometerSource;
use App\Models\Category;
use App\Support\SessionKey;
use App\Traits\WithAlert;
use Livewire\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Auth;

class OdometerForm extends Component
{
    use Toast, WithAlert;

    public $odometerLogId;
    public $asset_id = '';
    public $reading_km = '';
    public $read_at = '';
    public $source = VehicleOdometerSource::MANUAL;
    public $notes = '';
    public $isEdit = false;

    protected function rules()
    {
        return [
            'asset_id' => 'required|exists:assets,id',
            'reading_km' => 'required|integer|min:0',
            'read_at' => 'required|date',
            'source' => 'required|in:manual,telematics,service',
            'notes' => 'nullable|string|max:500',
        ];
    }

    protected $listeners = [
        'editOdometerLog' => 'edit',
        'resetOdometerForm' => 'resetForm'
    ];

    public function updatedAssetId($value)
    {
        // keep event dispatch as-is
        $this->dispatch('asset-id-changed', $value);

        // treat empty/blank as reset
        if (\Illuminate\Support\Str::of((string) $value)->trim()->isEmpty()) {
            $this->resetForm();
            return;
        }

        // For OdometerForm, we don't need to load existing data like ProfileForm
        // Just keep the asset_id updated
    }

    public function mount($assetId = null)
    {
        $this->asset_id = $assetId;
        $this->read_at = now()->format('Y-m-d H:i');
    }

    public function resetForm()
    {
        $this->reading_km = '';
        $this->read_at = now()->format('Y-m-d H:i');
        $this->source = '';
        $this->notes = '';
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'asset_id' => $this->asset_id,
                'reading_km' => $this->reading_km,
                'read_at' => $this->read_at,
                'source' => VehicleOdometerSource::from($this->source),
                'notes' => $this->notes ?: null,
            ];

            if ($this->isEdit) {
                $log = VehicleOdometerLog::find($this->odometerLogId);
                $log->update($data);
                $this->showSuccessAlert('Log odometer berhasil diperbarui.', 'Berhasil');
                $this->dispatch('odometer-log-updated');
            } else {
                VehicleOdometerLog::create($data);
                $this->showSuccessAlert('Log odometer berhasil dibuat.', 'Berhasil');
                $this->dispatch('odometer-log-saved');
            }

            $this->resetForm();
        } catch (\Exception $e) {
            $this->showErrorAlert('Gagal menyimpan log odometer: ' . $e->getMessage(), 'Error');
        }
    }

    public function render()
    {
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
                $asset->display_name = $asset->name . ' (' . $asset->code . ')';
                return $asset;
            });
        
        $sources = collect(VehicleOdometerSource::options())->map(function ($label, $value) {
            return ['value' => $value, 'label' => $label];
        })->values()->toArray();
        
        return view('livewire.vehicles.odometer-form', compact('assets', 'sources'))
            ->with('odometerLogId', $this->odometerLogId)
            ->with('isEdit', $this->isEdit);
    }
}