<?php

namespace App\Http\Controllers;

use App\Models\AssetMaintenance;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenances = AssetMaintenance::with(['asset', 'assignedUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard.maintenances.index', compact('maintenances'));
    }
}
