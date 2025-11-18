<?php

namespace App\Livewire\Vehicles;

use App\Enums\VehicleOdometerSource;
use App\Models\Asset;
use App\Models\Category;
use App\Models\VehicleOdometerLog;
use App\Models\VehicleProfile;
use App\Support\SessionKey;
use App\Traits\WithAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Mary\Traits\Toast;

class OdometerForm extends Component
{
    use Toast, WithAlert;

    public $odometerLogId;

    public $assets = [];

    public $asset_id = '';

    public $odometer_km = '';

    public $read_at = '';

    public $source = 'manual';

    public $notes = '';

    public $isEdit = false;

    protected function rules()
    {
        return [
            'asset_id' => 'required|exists:assets,id',
            'odometer_km' => 'required|integer|min:0',
            'read_at' => 'required|date',
            'source' => 'required|in:manual,telematics,service',
            'notes' => 'nullable|string|max:500',
        ];
    }

    protected $listeners = [
        'editOdometerLog' => 'edit',
        'resetOdometerForm' => 'resetForm',
    ];

    public function updatedAssetId($value)
    {
        $this->dispatch('asset-id-changed', $value);

        if (\Illuminate\Support\Str::of((string) $value)->trim()->isEmpty()) {
            $this->resetForm();

            return;
        }
    }

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

    public function mount($assetId = null)
    {
        $this->asset_id = $assetId;
        $this->read_at = now()->format('Y-m-d H:i');
        $this->loadAssets();
    }

    public function resetForm()
    {
        $this->odometer_km = '';
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
                'odometer_km' => $this->odometer_km,
                'read_at' => $this->read_at,
                'source' => VehicleOdometerSource::from($this->source),
                'notes' => $this->notes ?: null,
            ];

            if ($this->isEdit) {
                $log = VehicleOdometerLog::find($this->odometerLogId);
                $log->update($data);
                $this->success('Log odometer berhasil diperbarui!');
            } else {
                VehicleOdometerLog::create($data);
                $this->success('Log odometer berhasil dibuat!');
            }
            
            $this->resetForm();
            $this->dispatch('odometer-saved');
            $this->dispatch('close-drawer');
        } catch (\Exception $e) {
            $this->error('Gagal menyimpan log odometer: '.$e->getMessage());
        }
    }

    public function render()
    {
        $sources = collect(VehicleOdometerSource::options())->map(function ($label, $value) {
            return ['value' => $value, 'label' => $label];
        })->values()->toArray();

        $lastOdometerKm = null;
        if (! empty($this->asset_id)) {
            $profileKm = VehicleProfile::where('asset_id', $this->asset_id)->value('current_odometer_km');
            $maxLogKm = VehicleOdometerLog::where('asset_id', $this->asset_id)->max('odometer_km');
            if ($profileKm !== null || $maxLogKm !== null) {
                $lastOdometerKm = max($profileKm ?? 0, $maxLogKm ?? 0);
            }
        }

        return view('livewire.vehicles.odometer-form', compact('sources'))
            ->with('odometerLogId', $this->odometerLogId)
            ->with('isEdit', $this->isEdit)
            ->with('lastOdometerKm', $lastOdometerKm)
            ->with('unit', 'km');
    }
}
