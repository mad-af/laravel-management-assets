<?php

namespace App\Http\Controllers;

use App\Enums\AssetLogAction;
use App\Enums\AssetStatus;
use App\Enums\MaintenanceStatus;
use App\Models\Asset;
use App\Models\AssetLog;
use App\Models\AssetMaintenance;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        if (! isset($validated['status'])) {
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
                    'status' => ['old' => $oldStatus->value, 'new' => AssetStatus::MAINTENANCE->value],
                ],
                'notes' => 'Asset moved to maintenance: '.$validated['title'],
            ]);
        }

        return redirect()->route('maintenances.index');
    }

    /**
     * Generate PDF report for maintenance.
     */
    public function generatePDF(AssetMaintenance $maintenance)
    {
        // Load relationships
        $maintenance->load(['asset', 'assignedUser']);

        // Prepare data for PDF template
        $data = (object) [
            'work_order_no' => 'WO-'.str_pad($maintenance->id, 6, '0', STR_PAD_LEFT),
            'workshop' => (object) [
                'name' => 'Workshop Maintenance',
                'address' => 'Jl. Maintenance No. 123',
            ],
            'vehicle' => (object) [
                'vehicle_no' => $maintenance->asset->asset_code ?? 'N/A',
                'brand' => (object) [
                    'name' => $maintenance->asset->brand ?? 'Unknown',
                ],
                'type' => $maintenance->asset->model ?? 'Unknown',
            ],
            'employee' => (object) [
                'name' => $maintenance->assignedUser->name ?? 'N/A',
                'phone' => $maintenance->assignedUser->phone ?? 'N/A',
            ],
            'start_date' => $maintenance->scheduled_date ?? $maintenance->created_at,
            'estimation_end_date' => $maintenance->scheduled_date ?
                \Carbon\Carbon::parse($maintenance->scheduled_date)->addDays(7) :
                \Carbon\Carbon::parse($maintenance->created_at)->addDays(7),
            'note' => $maintenance->notes ?? $maintenance->description ?? 'No notes available',
            'maintenance' => $maintenance,
        ];

        // Prepare service instruction items
        $item = collect([
            (object) ['instruction' => $maintenance->title],
            (object) ['instruction' => $maintenance->description],
        ]);

        // Generate PDF
        $pdf = Pdf::loadView('pdf-template.maintenance-report', compact('data', 'item'));

        // Return PDF as response
        return $pdf->stream('maintenance-report-'.$maintenance->id.'.pdf');
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
                'maintenance' => $maintenance->load(['asset', 'assignedUser']),
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
                'notes' => 'Maintenance updated: '.$validated['title'],
            ]);
        }

        return redirect()->route('maintenances.index');
    }
}
