<?php

namespace App\Http\Controllers;

use App\Models\VehicleProfile;
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
     * Display the specified vehicle profile.
     */
    public function show(VehicleProfile $vehicleProfile)
    {
        return view('dashboard.vehicles.show', compact('vehicleProfile'));
    }
}
