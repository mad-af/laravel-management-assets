<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

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
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('dashboard.assets.index', compact('assets', 'categories', 'locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

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
            'status' => 'required|in:active,inactive,maintenance,disposed',
            'condition' => 'required|in:excellent,good,fair,poor',
            'value' => 'required|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'description' => 'nullable|string|max:1000',
        ]);

        $asset = Asset::create($validated);

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
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

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
            'status' => 'required|in:active,inactive,maintenance,disposed',
            'condition' => 'required|in:excellent,good,fair,poor',
            'value' => 'required|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'description' => 'nullable|string|max:1000',
        ]);

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
            'status' => 'required|in:active,inactive,maintenance,disposed',
        ]);

        $oldStatus = $asset->status;
        $asset->update(['status' => $validated['status']]);

        // Log the status change
        if (Auth::check()) {
            $asset->logs()->create([
                'user_id' => Auth::id(),
                'action' => 'status_changed',
                'changed_fields' => json_encode([
                    'status' => [
                        'old' => $oldStatus,
                        'new' => $validated['status']
                    ]
                ]),
                'notes' => "Status changed from {$oldStatus} to {$validated['status']}",
            ]);
        }

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Asset status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset): RedirectResponse
    {
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
}
