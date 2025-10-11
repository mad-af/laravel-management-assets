<?php

namespace App\Http\Controllers;

use App\Enums\AssetLogAction;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\AssetLog;
use App\Models\Branch;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Asset::with(['category', 'branch']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('branch', function ($branchQuery) use ($search) {
                        $branchQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('category')) {
            $query->byCategory($request->get('category'));
        }

        if ($request->filled('branch')) {
            $query->where('branch_id', $request->get('branch'));
        }

        if ($request->filled('status')) {
            $query->byStatus($request->get('status'));
        }

        if ($request->filled('condition')) {
            $query->byCondition($request->get('condition'));
        }

        $assets = $query->orderBy('created_at', 'desc')->paginate(15);
        $categories = Category::active()->orderBy('name')->get();
        $branches = Branch::where('is_active', true)->orderBy('name')->get();

        return view('dashboard.assets.index', compact('assets', 'categories', 'branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = Category::active()->orderBy('name')->get();
        $branches = Branch::where('is_active', true)->orderBy('name')->get();

        return view('dashboard.assets.create', compact('categories', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:assets,code',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => 'required|exists:branches,id',
            'status' => 'required|in:active,damaged,lost,maintenance,checked_out',
            'condition' => 'required|in:excellent,good,fair,poor',
            'value' => 'required|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'description' => 'nullable|string|max:1000',
        ]);

        $asset = Asset::create($validated);

        // Log asset creation
        if (Auth::check()) {
            AssetLog::create([
                'asset_id' => $asset->id,
                'user_id' => Auth::id(),
                'action' => AssetLogAction::CREATED,
                'notes' => 'Asset created successfully',
            ]);
        }

        return redirect()->route('assets.index')
            ->with('success', 'Asset created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset): View
    {
        $asset->load(['category', 'logs.user']);

        return view('dashboard.assets.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset): View
    {
        $categories = Category::active()->orderBy('name')->get();
        $branches = Branch::where('is_active', true)->orderBy('name')->get();

        return view('dashboard.assets.edit', compact('asset', 'categories', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:assets,code,'.$asset->id,
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => 'required|exists:branches,id',
            'status' => 'required|in:active,damaged,lost,maintenance,checked_out',
            'condition' => 'required|in:excellent,good,fair,poor',
            'value' => 'required|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'description' => 'nullable|string|max:1000',
        ]);

        // Update asset - logging handled by model observer
        $asset->update($validated);

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Asset updated successfully.');
    }

    /**
     * Update the asset status.
     */
    public function updateStatus(Request $request, Asset $asset): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:active,damaged,lost,maintenance,checked_out',
        ]);

        $oldStatus = $asset->status;
        $asset->update(['status' => $validated['status']]);

        // Update status - logging handled by model observer

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Asset status updated successfully.');
    }

    /**
     * Update the asset status via API.
     */
    public function updateStatusApi(Request $request, Asset $asset): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:active,damaged,lost,maintenance,checked_out',
        ]);

        $oldStatus = $asset->status;
        $asset->update(['status' => $validated['status']]);

        // Update status - logging handled by model observer

        return response()->json([
            'success' => true,
            'message' => 'Asset status updated successfully.',
            'data' => [
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
                'asset' => [
                    'id' => $asset->id,
                    'code' => $asset->code,
                    'name' => $asset->name,
                    'status' => $asset->status,
                    'status_badge_color' => $asset->status_badge_color,
                ],
            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset): RedirectResponse
    {

        // Asset deletion - logging handled by model observer

        $assetName = $asset->name;
        $asset->delete();

        return redirect()->route('assets.index')
            ->with('success', "Asset '{$assetName}' deleted successfully.");
    }

    /**
     * Export assets to CSV.
     */
    public function export(Request $request)
    {
        $query = Asset::with(['category']);

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('category')) {
            $query->byCategory($request->get('category'));
        }

        if ($request->filled('status')) {
            $query->byStatus($request->get('status'));
        }

        if ($request->filled('condition')) {
            $query->byCondition($request->get('condition'));
        }

        $assets = $query->orderBy('created_at', 'desc')->get();

        // Log export action
        if (Auth::check()) {
            AssetLog::create([
                'asset_id' => null, // This is a bulk action
                'user_id' => Auth::id(),
                'action' => 'exported',
                'notes' => 'Assets exported to CSV ('.$assets->count().' records)',
            ]);
        }

        $filename = 'assets_'.date('Y-m-d_H-i-s').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($assets) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Code',
                'Name',
                'Category',
                'Branch',
                'Status',
                'Condition',
                'Value',
                'Purchase Date',
                'Description',
                'Created At',
                'Updated At',
            ]);

            // CSV data
            foreach ($assets as $asset) {
                fputcsv($file, [
                    $asset->code,
                    $asset->name,
                    $asset->category->name,
                    $asset->branch->name,
                    ucfirst($asset->status),
                    ucfirst($asset->condition),
                    $asset->value,
                    $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : '',
                    $asset->description,
                    $asset->created_at->format('Y-m-d H:i:s'),
                    $asset->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get asset statistics for dashboard.
     */
    public function statistics()
    {
        $stats = [
            'total' => Asset::count(),
            'active' => Asset::byStatus(AssetStatus::ACTIVE)->count(),
            'inactive' => Asset::byStatus('inactive')->count(),
            'maintenance' => Asset::byStatus(AssetStatus::MAINTENANCE)->count(),
            'disposed' => Asset::byStatus('disposed')->count(),
            'total_value' => Asset::sum('value'),
            'by_category' => Asset::selectRaw('categories.name as category, COUNT(*) as count')
                ->join('categories', 'assets.category_id', '=', 'categories.id')
                ->groupBy('categories.name')
                ->orderBy('count', 'desc')
                ->get(),
            'by_condition' => Asset::selectRaw('condition, COUNT(*) as count')
                ->groupBy('condition')
                ->orderBy('count', 'desc')
                ->get(),
            'recent_assets' => Asset::with(['category', 'branch'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Search for asset by code (for scanner API)
     */
    public function searchByCode(Request $request): JsonResponse
    {
        $code = $request->get('code');

        if (! $code) {
            return response()->json([
                'found' => false,
                'message' => 'Kode tidak boleh kosong',
            ], 400);
        }

        // Search by tag_code first, then by code
        $asset = Asset::with(['category', 'branch', 'company'])
            ->where('tag_code', $code)
            ->orWhere('code', $code)
            ->first();

        if ($asset) {
            // Update last_seen_at
            $asset->update(['last_seen_at' => now()]);

            // Log the scan activity
            AssetLog::create([
                'asset_id' => $asset->id,
                'user_id' => Auth::id(),
                'action' => 'scanned',
                'description' => 'Asset scanned via QR/Barcode scanner',
                'metadata' => json_encode([
                    'scanned_code' => $code,
                    'scan_timestamp' => now()->toISOString(),
                    'user_agent' => $request->userAgent(),
                    'ip_address' => $request->ip(),
                ]),
            ]);

            return response()->json([
                'found' => true,
                'asset' => [
                    'id' => $asset->id,
                    'name' => $asset->name,
                    'code' => $asset->code,
                    'tag_code' => $asset->tag_code,
                    'status' => $asset->status,
                    'status_badge_color' => $asset->status_badge_color,
                    'condition' => $asset->condition,
                    'condition_badge_color' => $asset->condition_badge_color,
                    'description' => $asset->description,
                    'purchase_date' => $asset->purchase_date?->format('Y-m-d'),
                    'purchase_price' => $asset->purchase_price,
                    'category' => $asset->category ? [
                        'id' => $asset->category->id,
                        'name' => $asset->category->name,
                    ] : null,
                    'branch' => $asset->branch ? [
                        'id' => $asset->branch->id,
                        'name' => $asset->branch->name,
                    ] : null,
                    'company' => $asset->company ? [
                        'id' => $asset->company->id,
                        'name' => $asset->company->name,
                        'code' => $asset->company->code,
                    ] : null,
                    'last_seen_at' => $asset->last_seen_at?->format('Y-m-d H:i:s'),
                ],
            ]);
        }

        return response()->json([
            'found' => false,
            'message' => 'Asset dengan kode tersebut tidak ditemukan',
        ]);
    }

    /**
     * Checkout an asset via API.
     */
    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'borrower_name' => 'required|string|max:255',
            'checkout_at' => 'required|date',
            'due_at' => 'required|date|after:checkout_at',
            'condition_out' => 'required|in:excellent,good,fair,poor',
            'notes' => 'nullable|string|max:1000',
        ]);

        $asset = Asset::findOrFail($validated['asset_id']);

        // Check if asset is available for checkout
        if ($asset->status === AssetStatus::ON_LOAN) {
            return response()->json([
                'success' => false,
                'message' => 'Asset sudah dalam status checked out',
            ], 400);
        }

        if (in_array($asset->status, [AssetStatus::LOST, AssetStatus::MAINTENANCE])) {
            return response()->json([
                'success' => false,
                'message' => 'Asset tidak dapat di-checkout karena status: '.$asset->status,
            ], 400);
        }

        // Create asset loan record
        $loan = \App\Models\AssetLoan::create([
            'asset_id' => $validated['asset_id'],
            'borrower_name' => $validated['borrower_name'],
            'checkout_at' => $validated['checkout_at'],
            'due_at' => $validated['due_at'],
            'condition_out' => $validated['condition_out'],
            'notes' => $validated['notes'],
        ]);

        // Update asset status
        $asset->update(['status' => AssetStatus::ON_LOAN]);

        // Log the checkout
        AssetLog::create([
            'asset_id' => $asset->id,
            'user_id' => Auth::id(),
            'action' => AssetLogAction::CHECKED_OUT,
            'notes' => 'Asset checked out to: '.$validated['borrower_name'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Asset berhasil di-checkout',
            'data' => [
                'asset' => [
                    'id' => $asset->id,
                    'code' => $asset->code,
                    'name' => $asset->name,
                    'status' => $asset->status,
                    'status_badge_color' => $asset->status_badge_color,
                ],
                'loan' => $loan,
            ],
        ]);
    }

    /**
     * Checkin an asset via API.
     */
    public function checkin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'checkin_at' => 'required|date',
            'condition_in' => 'required|in:excellent,good,fair,poor',
            'notes' => 'nullable|string|max:1000',
        ]);

        $asset = Asset::findOrFail($validated['asset_id']);

        // Check if asset is checked out
        if ($asset->status !== AssetStatus::ON_LOAN) {
            return response()->json([
                'success' => false,
                'message' => 'Asset tidak dalam status checked out',
            ], 400);
        }

        // Find active loan
        $loan = \App\Models\AssetLoan::where('asset_id', $validated['asset_id'])
            ->whereNull('checkin_at')
            ->first();

        if (! $loan) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ditemukan record peminjaman aktif untuk asset ini',
            ], 400);
        }

        // Update loan record
        $loan->update([
            'checkin_at' => $validated['checkin_at'],
            'condition_in' => $validated['condition_in'],
            'notes' => $loan->notes.($validated['notes'] ? '\n\nCheckin Notes: '.$validated['notes'] : ''),
        ]);

        // Determine new asset status based on condition
        $newStatus = AssetStatus::ACTIVE;
        if (in_array($validated['condition_in'], ['poor'])) {
            $newStatus = AssetStatus::MAINTENANCE;
        }

        // Update asset status and condition
        $asset->update([
            'status' => $newStatus,
            'condition' => $validated['condition_in'],
        ]);

        // Log the checkin
        AssetLog::create([
            'asset_id' => $asset->id,
            'user_id' => Auth::id(),
            'action' => AssetLogAction::CHECKED_IN,
            'notes' => 'Asset checked in with condition: '.$validated['condition_in'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Asset berhasil di-checkin',
            'data' => [
                'asset' => [
                    'id' => $asset->id,
                    'code' => $asset->code,
                    'name' => $asset->name,
                    'status' => $asset->status,
                    'status_badge_color' => $asset->status_badge_color,
                    'condition' => $asset->condition,
                    'condition_badge_color' => $asset->condition_badge_color,
                ],
                'loan' => $loan,
            ],
        ]);
    }
}
