<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\VehicleProfile;
use App\Models\VehicleOdometerLog;
use App\Enums\VehicleOdometerSource;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Display a listing of the vehicles with Livewire components.
     */
    public function index()
    {
        return view('dashboard.vehicles.index');
    }

    /**
     * Display the specified vehicle.
     */
    public function show(Asset $vehicle)
    {
        // Load necessary relationships
        $vehicle->load(['category', 'location', 'vehicleProfile', 'odometerLogs', 'maintenances']);
        
        // Ensure this is actually a vehicle asset
        $vehicleCategory = Category::where('name', 'Kendaraan')->first();
        if (!$vehicleCategory || $vehicle->category_id !== $vehicleCategory->id) {
            abort(404, 'Vehicle not found');
        }
        
        return view('dashboard.vehicles.show', compact('vehicle'));
    }

    /**
     * Store or update vehicle profile.
     */
    public function storeProfile(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'plate_no' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'vin' => 'nullable|string|max:255',
            'year_purchase' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'year_manufacture' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'current_odometer_km' => 'nullable|integer|min:0',
            'last_service_date' => 'nullable|date',
            'service_interval_km' => 'nullable|integer|min:1',
            'service_interval_days' => 'nullable|integer|min:1',
            'service_target_odometer_km' => 'nullable|integer|min:0',
            'next_service_date' => 'nullable|date',
            'annual_tax_due_date' => 'nullable|date',
        ]);

        try {
            $data = [
                'year_purchase' => $request->year_purchase ?: null,
                'year_manufacture' => $request->year_manufacture ?: null,
                'current_odometer_km' => $request->current_odometer_km ?: null,
                'last_service_date' => $request->last_service_date ?: null,
                'service_interval_km' => $request->service_interval_km ?: null,
                'service_interval_days' => $request->service_interval_days ?: null,
                'service_target_odometer_km' => $request->service_target_odometer_km ?: null,
                'next_service_date' => $request->next_service_date ?: null,
                'annual_tax_due_date' => $request->annual_tax_due_date ?: null,
                'plate_no' => $request->plate_no,
                'vin' => $request->vin,
                'brand' => $request->brand,
                'model' => $request->model,
            ];

            // Use updateOrCreate for upsert functionality based on asset_id
            VehicleProfile::updateOrCreate(
                ['asset_id' => $request->asset_id],
                $data
            );

            return redirect()->back()->with('success', 'Profil kendaraan berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan profil kendaraan: ' . $e->getMessage());
        }
    }

    /**
     * Store or update odometer log.
     */
    public function storeOdometer(Request $request)
    {
        // Get current odometer for validation
        $vehicleProfile = VehicleProfile::where('asset_id', $request->asset_id)->first();
        $latestOdometerLog = VehicleOdometerLog::where('asset_id', $request->asset_id)
            ->orderBy('read_at', 'desc')
            ->first();

        $currentOdometer = 0;
        if ($vehicleProfile && $vehicleProfile->current_odometer_km) {
            $currentOdometer = max($currentOdometer, $vehicleProfile->current_odometer_km);
        }
        if ($latestOdometerLog && $latestOdometerLog->reading_km) {
            $currentOdometer = max($currentOdometer, $latestOdometerLog->reading_km);
        }

        // Set minimum reading_km based on current odometer (skip for updates)
        $minReading = $request->odometer_log_id ? 0 : $currentOdometer;

        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'reading_km' => 'required|integer|min:' . $minReading,
            'read_at' => 'required|date',
            'source' => 'required|string|in:' . implode(',', array_column(VehicleOdometerSource::cases(), 'value')),
            'notes' => 'nullable|string',
            'odometer_log_id' => 'nullable|exists:vehicle_odometer_logs,id',
        ], [
            'reading_km.min' => 'Reading odometer tidak boleh lebih rendah dari data terakhir (' . number_format($currentOdometer) . ' km).'
        ]);

        try {
            $data = [
                'asset_id' => $request->asset_id,
                'reading_km' => $request->reading_km,
                'read_at' => $request->read_at,
                'source' => VehicleOdometerSource::from($request->source),
                'notes' => $request->notes ?: null,
            ];

            if ($request->odometer_log_id) {
                $log = VehicleOdometerLog::find($request->odometer_log_id);
                $log->update($data);
                $message = 'Log odometer berhasil diperbarui.';
            } else {
                VehicleOdometerLog::create($data);
                $message = 'Log odometer berhasil dibuat.';
            }

            // Update current odometer in vehicle profile
            VehicleProfile::updateOrCreate(
                ['asset_id' => $request->asset_id],
                ['current_odometer_km' => $request->reading_km]
            );

            return redirect()->route('vehicles.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan log odometer: ' . $e->getMessage());
        }
    }

    public function getFormData()
    {
        $vehicleCategory = Category::where('name', 'Kendaraan')->first();

        $assets = Asset::query()
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

        return compact('assets', 'sources');
    }
}
