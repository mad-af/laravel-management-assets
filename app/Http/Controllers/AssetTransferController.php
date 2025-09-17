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
use Illuminate\Validation\Rule;

class AssetTransferController extends Controller
{
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
            'transfer_no' => 'required|string|unique:asset_transfers',
            'reason' => 'nullable|string',
            'type' => ['required', Rule::enum(AssetTransferType::class)],
            'priority' => ['required', Rule::enum(AssetTransferPriority::class)],
            'scheduled_at' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.asset_id' => 'required|exists:assets,id',
            'items.*.to_location_id' => 'required|exists:locations,id',
        ]);

        DB::transaction(function () use ($request) {
            $transfer = AssetTransfer::create([
                'company_id' => Auth::user()->company_id,
                'transfer_no' => $request->transfer_no,
                'reason' => $request->reason,
                'status' => AssetTransferStatus::DRAFT,
                'requested_by' => Auth::id(),
                'scheduled_at' => $request->scheduled_at,
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                $asset = Asset::find($item['asset_id']);
                $transfer->items()->create([
                    'asset_id' => $item['asset_id'],
                    'from_location_id' => $asset->location_id,
                    'to_location_id' => $item['to_location_id'],
                    'status' => AssetTransferItemStatus::PENDING,
                ]);
            }
        });

        return redirect()->route('asset-transfers.index')
            ->with('success', 'Transfer aset berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AssetTransfer $assetTransfer)
    {
        $assetTransfer->load(['company', 'requestedBy', 'approvedBy', 'items.asset', 'items.fromLocation', 'items.toLocation']);
        
        return view('dashboard.asset-transfers.show', compact('assetTransfer'));
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
        $assetTransfer->load('items');

        return view('dashboard.asset-transfers.edit', compact('assetTransfer', 'assets', 'locations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AssetTransfer $assetTransfer)
    {
        if ($assetTransfer->status !== AssetTransferStatus::DRAFT) {
            return redirect()->route('asset-transfers.show', $assetTransfer)
                ->with('error', 'Hanya transfer dengan status draft yang dapat diupdate.');
        }

        $request->validate([
            'transfer_no' => 'required|string|unique:asset_transfers,transfer_no,' . $assetTransfer->id,
            'reason' => 'nullable|string',
            'type' => ['sometimes', Rule::enum(AssetTransferType::class)],
            'priority' => ['sometimes', Rule::enum(AssetTransferPriority::class)],
            'scheduled_at' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.asset_id' => 'required|exists:assets,id',
            'items.*.to_location_id' => 'required|exists:locations,id',
        ]);

        DB::transaction(function () use ($request, $assetTransfer) {
            $assetTransfer->update([
                'transfer_no' => $request->transfer_no,
                'reason' => $request->reason,
                'scheduled_at' => $request->scheduled_at,
                'notes' => $request->notes,
            ]);

            // Delete existing items
            $assetTransfer->items()->delete();

            // Create new items
            foreach ($request->items as $item) {
                $asset = Asset::find($item['asset_id']);
                $assetTransfer->items()->create([
                    'asset_id' => $item['asset_id'],
                    'from_location_id' => $asset->location_id,
                    'to_location_id' => $item['to_location_id'],
                    'status' => AssetTransferItemStatus::PENDING,
                ]);
            }
        });

        return redirect()->route('asset-transfers.show', $assetTransfer)
            ->with('success', 'Transfer aset berhasil diupdate.');
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
                    'status' => AssetTransferItemStatus::DELIVERED,
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
