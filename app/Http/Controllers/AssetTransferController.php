<?php

namespace App\Http\Controllers;

use App\Enums\AssetTransferStatus;
use App\Enums\AssetTransferPriority;
use App\Enums\AssetTransferType;
use App\Enums\AssetTransferItemStatus;
use App\Enums\AssetLocationChangeType;
use App\Models\AssetTransfer;
use App\Models\Asset;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AssetTransferController extends Controller
{
    /**
     * Generate unique transfer number.
     */
    private function generateTransferNo()
    {
        return 'TRF-' . date('Ymd') . '-' . strtoupper(Str::random(4));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transfers = AssetTransfer::with(['company', 'requestedBy', 'approvedBy'])
            ->where('company_id', Auth::user()->company_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard.asset-transfers.index', compact('transfers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        dd("Hello");
        $assets = Asset::where('company_id', Auth::user()->company_id)
            ->where('status', 'active')
            ->with('location')
            ->get();
        
        $locations = Location::where('is_active', true)->get();

        return view('dashboard.asset-transfers.create', compact('assets', 'locations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $request->validate([
            'reason' => 'required|string|max:500',
            'from_location_id' => 'required|exists:locations,id',
            'to_location_id' => 'required|exists:locations,id|different:from_location_id',
            'status' => 'required|string',
            'priority' => 'required|string',
            'scheduled_at' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.asset_id' => 'required|exists:assets,id',
            'items.*.from_location_id' => 'nullable|exists:locations,id',
            'items.*.to_location_id' => 'nullable|exists:locations,id',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $user = Auth::user();
                
                // Ensure user has company_id
                if (!$user->company_id) {
                    Log::error('User attempting to create asset transfer without company_id', [
                        'user_id' => $user->id,
                        'user_email' => $user->email
                    ]);
                    throw new \Exception('User account is not properly configured. Please contact administrator.');
                }
                
                $transfer = AssetTransfer::create([
                    'company_id' => $user->company_id,
                    'transfer_no' => $this->generateTransferNo(),
                    'reason' => $request->reason,
                    'status' => AssetTransferStatus::from($request->status),
                    'priority' => AssetTransferPriority::from($request->priority),
                    'requested_by' => Auth::id(),
                    'from_location_id' => $request->from_location_id,
                    'to_location_id' => $request->to_location_id,
                    'scheduled_at' => $request->scheduled_at,
                    'notes' => $request->notes,
                ]);

                foreach ($request->items as $item) {
                    $transfer->items()->create([
                        'asset_id' => $item['asset_id'],
                        'from_location_id' => $request->from_location_id,
                        'to_location_id' => $request->to_location_id,
                        'notes' => $item['notes'] ?? '',
                    ]);
                }
            });

            // Success: redirect back without query params and show success message
            return redirect('/admin/asset-transfers')
                ->with('success', 'Transfer aset berhasil dibuat.');
                
        } catch (\Exception $e) {
            Log::error('Asset transfer creation failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
            
            // Error: redirect back with query params and show error message
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat transfer aset: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AssetTransfer $assetTransfer)
    {
        $assetTransfer->load(['company', 'requestedBy', 'approvedBy', 'fromLocation', 'toLocation', 'items.asset.location', 'items.fromLocation', 'items.toLocation']);
        
        // Prepare data for detail-info component
        $transferData = [
            'transfer_no' => $assetTransfer->transfer_no,
            'status' => $assetTransfer->status,
            'priority' => $assetTransfer->priority,
            'type' => $assetTransfer->type,
            'from_location' => $assetTransfer->fromLocation->name ?? null,
            'to_location' => $assetTransfer->toLocation->name ?? null,
            'requested_by' => $assetTransfer->requestedBy->name ?? null,
            'company' => $assetTransfer->company->name ?? null,
            'scheduled_at' => $assetTransfer->scheduled_at,
            'requested_at' => $assetTransfer->requested_at,
            'description' => $assetTransfer->description,
            'reason' => $assetTransfer->reason,
            'notes' => $assetTransfer->notes,
        ];
        
        // Prepare data for items-table component
        $itemsData = $assetTransfer->items->map(function ($item) {
            return [
                'asset_name' => $item->asset->name ?? 'N/A',
                'asset_brand' => $item->asset->brand ?? '',
                'asset_model' => $item->asset->model ?? '',
                'asset_code' => $item->asset->asset_code ?? 'N/A',
                'from_location' => $item->fromLocation->name ?? 'N/A',
                'to_location' => $item->toLocation->name ?? 'N/A',
                'current_location' => $item->asset->location->name ?? 'Unknown',
                'status' => $item->status?->value ?? 'pending',
                'quantity' => $item->quantity,
                'notes' => $item->notes,
            ];
        })->toArray();
        
        // Prepare data for quick-actions component
        $quickActionsData = [
            'status' => $assetTransfer->status->value,
            'id' => $assetTransfer->id,
        ];
        
        // Prepare data for timeline component
        $timelineData = [
            'created_at' => $assetTransfer->created_at,
            'requested_by' => $assetTransfer->requestedBy->name ?? null,
            'scheduled_at' => $assetTransfer->scheduled_at,
            'approved_at' => $assetTransfer->approved_at,
            'approved_by' => $assetTransfer->approvedBy->name ?? null,
            'executed_at' => $assetTransfer->executed_at,
        ];
        
        return view('dashboard.asset-transfers.show', compact('assetTransfer', 'transferData', 'itemsData', 'quickActionsData', 'timelineData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AssetTransfer $assetTransfer)
    {
        if ($assetTransfer->status !== AssetTransferStatus::DRAFT) {
            return redirect()->route('asset-transfers.show', $assetTransfer)
                ->with('error', 'Hanya transfer dengan status draft yang dapat diedit.');
        }

        $assets = Asset::where('company_id', Auth::user()->company_id)
            ->where('status', 'active')
            ->with('location')
            ->get();
        
        $locations = Location::where('is_active', true)->get();
        $assetTransfer->load(['items', 'fromLocation', 'toLocation']);

        return view('dashboard.asset-transfers.edit', compact('assetTransfer', 'assets', 'locations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AssetTransfer $assetTransfer)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'from_location_id' => 'required|exists:locations,id',
            'to_location_id' => 'required|exists:locations,id|different:from_location_id',
            'status' => 'required|string',
            'priority' => 'required|string',
            'scheduled_at' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.asset_id' => 'required|exists:assets,id',
            'items.*.from_location_id' => 'nullable|exists:locations,id',
            'items.*.to_location_id' => 'nullable|exists:locations,id',
        ]);

        try {
            DB::transaction(function () use ($request, $assetTransfer) {
                $assetTransfer->update([
                    'reason' => $request->reason,
                    'from_location_id' => $request->from_location_id,
                    'to_location_id' => $request->to_location_id,
                    'status' => AssetTransferStatus::from($request->status),
                    'priority' => AssetTransferPriority::from($request->priority),
                    'scheduled_at' => $request->scheduled_at,
                    'notes' => $request->notes,
                ]);

                // Delete existing items
                $assetTransfer->items()->delete();

                // Create new items
                foreach ($request->items as $item) {
                    $assetTransfer->items()->create([
                        'asset_id' => $item['asset_id'],
                        'from_location_id' => $request->from_location_id,
                        'to_location_id' => $request->to_location_id,
                        'notes' => $item['notes'] ?? '',
                    ]);
                }
            });

            // Success: redirect back without query params and show success message
            return redirect('/admin/asset-transfers')
                ->with('success', 'Transfer aset berhasil diupdate.');
                
        } catch (\Exception $e) {
            Log::error('Asset transfer update failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'transfer_id' => $assetTransfer->id,
                'request_data' => $request->all()
            ]);
            
            // Error: redirect back with query params and show error message
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate transfer aset: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssetTransfer $assetTransfer)
    {
        if ($assetTransfer->status !== AssetTransferStatus::DRAFT) {
            return redirect()->route('asset-transfers.index')
                ->with('error', 'Hanya transfer dengan status draft yang dapat dihapus.');
        }

        $assetTransfer->delete();

        return redirect()->route('asset-transfers.index')
            ->with('success', 'Transfer aset berhasil dihapus.');
    }

    /**
     * Execute the asset transfer.
     */
    public function execute(AssetTransfer $assetTransfer)
    {
        if ($assetTransfer->status !== AssetTransferStatus::DRAFT) {
            return redirect()->route('asset-transfers.show', $assetTransfer)
                ->with('error', 'Hanya transfer dengan status draft yang dapat dieksekusi.');
        }

        DB::transaction(function () use ($assetTransfer) {
            foreach ($assetTransfer->items as $item) {
                // Update asset location
                $item->asset->update([
                    'location_id' => $item->to_location_id
                ]);
                
                // Update item status
                $item->update([
                    'transferred_at' => now(),
                ]);

                // Create location history
                $item->asset->locationHistories()->create([
                    'from_location_id' => $item->from_location_id,
                    'to_location_id' => $item->to_location_id,
                    'changed_at' => now(),
                    'changed_by' => Auth::id(),
                    'transfer_id' => $assetTransfer->id,
                    'change_type' => AssetLocationChangeType::TRANSFER,
                    'remark' => 'Transfer: ' . $assetTransfer->transfer_no,
                ]);
            }

            // Update transfer status
            $assetTransfer->update([
                'status' => AssetTransferStatus::EXECUTED,
                'executed_at' => now(),
            ]);
        });

        return redirect()->route('asset-transfers.show', $assetTransfer)
            ->with('success', 'Transfer aset berhasil dieksekusi.');
    }
}
