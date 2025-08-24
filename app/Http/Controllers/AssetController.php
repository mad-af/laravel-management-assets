<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetLog;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Asset::with(['category', 'location']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('location', function ($locationQuery) use ($search) {
                      $locationQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('category')) {
            $query->byCategory($request->get('category'));
        }

        if ($request->filled('location')) {
            $query->byLocation($request->get('location'));
        }

        if ($request->filled('status')) {
            $query->byStatus($request->get('status'));
        }

        if ($request->filled('condition')) {
            $query->byCondition($request->get('condition'));
        }

        $assets = $query->orderBy('created_at', 'desc')->paginate(15);
        $categories = Category::active()->orderBy('name')->get();
        $locations = Location::active()->orderBy('name')->get();

        return view('dashboard.assets.index', compact('assets', 'categories', 'locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = Category::active()->orderBy('name')->get();
        $locations = Location::active()->orderBy('name')->get();

        return view('dashboard.assets.create', compact('categories', 'locations'));
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
            'location_id' => 'required|exists:locations,id',
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
                'action' => 'created',
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
        $asset->load(['category', 'location', 'logs.user']);

        return view('dashboard.assets.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset): View
    {
        $categories = Category::active()->orderBy('name')->get();
        $locations = Location::active()->orderBy('name')->get();

        return view('dashboard.assets.edit', compact('asset', 'categories', 'locations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:assets,code,' . $asset->id,
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
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
                    'status_badge_color' => $asset->status_badge_color
                ]
            ]
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
        $query = Asset::with(['category', 'location']);

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('location', function ($locationQuery) use ($search) {
                      $locationQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('category')) {
            $query->byCategory($request->get('category'));
        }

        if ($request->filled('location')) {
            $query->byLocation($request->get('location'));
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
                'notes' => 'Assets exported to CSV (' . $assets->count() . ' records)',
            ]);
        }

        $filename = 'assets_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($assets) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Code',
                'Name',
                'Category',
                'Location',
                'Status',
                'Condition',
                'Value',
                'Purchase Date',
                'Description',
                'Created At',
                'Updated At'
            ]);

            // CSV data
            foreach ($assets as $asset) {
                fputcsv($file, [
                    $asset->code,
                    $asset->name,
                    $asset->category->name,
                    $asset->location->name,
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
            'active' => Asset::byStatus('active')->count(),
            'inactive' => Asset::byStatus('inactive')->count(),
            'maintenance' => Asset::byStatus('maintenance')->count(),
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
            'recent_assets' => Asset::with(['category', 'location'])
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
        
        if (!$code) {
            return response()->json([
                'found' => false,
                'message' => 'Kode tidak boleh kosong'
            ], 400);
        }

        // Search by tag_code first, then by code
        $asset = Asset::with(['category', 'location'])
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
                    'ip_address' => $request->ip()
                ])
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
                        'name' => $asset->category->name
                    ] : null,
                    'location' => $asset->location ? [
                        'id' => $asset->location->id,
                        'name' => $asset->location->name
                    ] : null,
                    'last_seen_at' => $asset->last_seen_at?->format('Y-m-d H:i:s')
                ]
            ]);
        }

        return response()->json([
            'found' => false,
            'message' => 'Asset dengan kode tersebut tidak ditemukan'
        ]);
    }
}
