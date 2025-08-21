<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetLogController extends Controller
{
    /**
     * Display a listing of asset logs.
     */
    public function index(Request $request): View
    {
        $query = AssetLog::with(['asset', 'user'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('asset_id')) {
            $query->forAsset($request->get('asset_id'));
        }

        if ($request->filled('action')) {
            $query->byAction($request->get('action'));
        }

        if ($request->filled('user_id')) {
            $query->byUser($request->get('user_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $logs = $query->paginate(20);
        
        // Get filter options
        $assets = Asset::orderBy('name')->get(['id', 'name', 'code']);
        $users = User::orderBy('name')->get(['id', 'name']);
        $actions = AssetLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('dashboard.asset-logs.index', compact('logs', 'assets', 'users', 'actions'));
    }

    /**
     * Display asset logs for a specific asset.
     */
    public function forAsset(Request $request, Asset $asset): View
    {
        $query = AssetLog::with(['user'])
            ->forAsset($asset->id)
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('action')) {
            $query->byAction($request->get('action'));
        }

        if ($request->filled('user_id')) {
            $query->byUser($request->get('user_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $logs = $query->paginate(15);

        // Get available actions for filter dropdown
        $actions = AssetLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        // Get users for filter dropdown
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('dashboard.asset-logs.for-asset', compact('asset', 'logs', 'actions', 'users'));
    }

    /**
     * Display the specified asset log.
     */
    public function show(AssetLog $assetLog): View
    {
        $assetLog->load(['asset', 'user']);
        $log = $assetLog;

        return view('dashboard.asset-logs.show', compact('log'));
    }

    /**
     * Export asset logs to CSV.
     */
    public function export(Request $request)
    {
        $query = AssetLog::with(['asset', 'user'])
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('asset_id')) {
            $query->forAsset($request->get('asset_id'));
        }

        if ($request->filled('action')) {
            $query->byAction($request->get('action'));
        }

        if ($request->filled('user_id')) {
            $query->byUser($request->get('user_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $logs = $query->get();

        $filename = 'asset_logs_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Asset Code',
                'Asset Name',
                'Action',
                'User',
                'Changed Fields',
                'Notes',
                'Date',
            ]);

            // CSV data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->asset->code ?? 'N/A',
                    $log->asset->name ?? 'N/A',
                    ucfirst(str_replace('_', ' ', $log->action)),
                    $log->user->name ?? 'System',
                    $log->formattedChanges,
                    $log->notes ?? '',
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get activity statistics for dashboard.
     */
    public function statistics()
    {
        $stats = [
            'total_logs' => AssetLog::count(),
            'today_logs' => AssetLog::whereDate('created_at', today())->count(),
            'this_week_logs' => AssetLog::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'this_month_logs' => AssetLog::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'by_action' => AssetLog::selectRaw('action, COUNT(*) as count')
                ->groupBy('action')
                ->orderBy('count', 'desc')
                ->get(),
            'by_user' => AssetLog::selectRaw('users.name as user_name, COUNT(*) as count')
                ->join('users', 'asset_logs.user_id', '=', 'users.id')
                ->groupBy('users.name')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'recent_activity' => AssetLog::with(['asset', 'user'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
        ];

        return response()->json($stats);
    }
}
