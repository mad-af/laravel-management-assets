<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
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
}
