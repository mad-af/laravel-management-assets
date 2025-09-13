<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetLog;
use App\Models\AssetMaintenance;
use App\Enums\AssetStatus;
use App\Enums\AssetLogAction;
use App\Enums\MaintenanceStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenances = AssetMaintenance::with(['asset', 'assignedUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard.maintenances.index', compact('maintenances'));
    }

    /**
     * Store a newly created maintenance record.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:preventive,corrective',
            'priority' => 'required|string|in:low,medium,high,critical',
            'status' => 'nullable|string|in:open,scheduled,in_progress,completed,cancelled',
            'description' => 'required|string|max:1000',
            'scheduled_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
            'cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Set default status if not provided
        if (!isset($validated['status'])) {
            $validated['status'] = MaintenanceStatus::OPEN->value;
        }

        // Get the asset
        $asset = Asset::findOrFail($validated['asset_id']);

        // Create maintenance record
        $maintenance = AssetMaintenance::create($validated);

        // Update asset status to MAINTENANCE
        $oldStatus = $asset->status;
        $asset->update(['status' => AssetStatus::MAINTENANCE]);

        // Log the maintenance creation and status change
        if (Auth::check()) {
            AssetLog::create([
                'asset_id' => $asset->id,
                'user_id' => Auth::id(),
                'action' => AssetLogAction::STATUS_CHANGED,
                'changed_fields' => [
                    'status' => ['old' => $oldStatus->value, 'new' => AssetStatus::MAINTENANCE->value]
                ],
                'notes' => 'Asset moved to maintenance: ' . $validated['title'],
            ]);
        }

        return redirect()->route('maintenances.index');
    }

    /**
     * Show the form for editing the specified maintenance.
     */
    public function edit(AssetMaintenance $maintenance)
    {
        // Return JSON response for AJAX requests
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'maintenance' => $maintenance->load(['asset', 'assignedUser'])
            ]);
        }

        // Return view for regular requests
        return view('dashboard.maintenances.edit', compact('maintenance'));
    }

    /**
     * Update the specified maintenance in storage.
     */
    public function update(Request $request, AssetMaintenance $maintenance): RedirectResponse
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:preventive,corrective',
            'priority' => 'required|string|in:low,medium,high,critical',
            'status' => 'nullable|string|in:open,scheduled,in_progress,completed,cancelled',
            'description' => 'required|string|max:1000',
            'scheduled_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
            'cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Update maintenance record
        $maintenance->update($validated);

        // Log the maintenance update
        if (Auth::check()) {
            AssetLog::create([
                'asset_id' => $maintenance->asset_id,
                'user_id' => Auth::id(),
                'action' => AssetLogAction::UPDATED,
                'notes' => 'Maintenance updated: ' . $validated['title'],
            ]);
        }

        return redirect()->route('maintenances.index');
    }
}
