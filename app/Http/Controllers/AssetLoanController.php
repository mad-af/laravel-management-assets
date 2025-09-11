<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\AssetLog;
use App\Enums\AssetStatus;
use App\Enums\LoanCondition;
use App\Enums\AssetLogAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetLoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = AssetLoan::with(['asset']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('borrower_name', 'like', "%{$search}%")
                  ->orWhereHas('asset', function ($assetQuery) use ($search) {
                      $assetQuery->where('name', 'like', "%{$search}%")
                                 ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'active') {
                $query->active();
            } elseif ($status === 'returned') {
                $query->whereNotNull('checkin_at');
            } elseif ($status === 'overdue') {
                $query->overdue();
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('checkout_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('checkout_at', '<=', $request->get('date_to'));
        }

        $assetLoans = $query->orderBy('checkout_at', 'desc')->paginate(15);

        return view('dashboard.asset-loans.index', compact('assetLoans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $assets = Asset::where('status', '!=', AssetStatus::CHECKED_OUT)
                      ->whereNotIn('status', [AssetStatus::DAMAGED, AssetStatus::LOST, AssetStatus::MAINTENANCE])
                      ->orderBy('name')
                      ->get();

        return view('dashboard.asset-loans.create', compact('assets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
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
        if ($asset->status === AssetStatus::CHECKED_OUT) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['asset_id' => 'Asset sudah dalam status checked out']);
        }

        if (in_array($asset->status, [AssetStatus::DAMAGED, AssetStatus::LOST, AssetStatus::MAINTENANCE])) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['asset_id' => 'Asset tidak dapat di-checkout karena status: ' . $asset->status]);
        }

        // Create asset loan record
        $assetLoan = AssetLoan::create($validated);

        // Update asset status
        $asset->update(['status' => AssetStatus::CHECKED_OUT]);

        return redirect()->route('asset-loans.show', $assetLoan)
            ->with('success', 'Asset loan berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AssetLoan $assetLoan): View
    {
        $assetLoan->load(['asset']);

        return view('dashboard.asset-loans.show', compact('assetLoan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AssetLoan $assetLoan): View
    {
        $assetLoan->load(['asset']);
        $assets = Asset::orderBy('name')->get();

        return view('dashboard.asset-loans.edit', compact('assetLoan', 'assets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AssetLoan $assetLoan): RedirectResponse
    {
        $validated = $request->validate([
            'borrower_name' => 'required|string|max:255',
            'checkout_at' => 'required|date',
            'due_at' => 'required|date|after:checkout_at',
            'checkin_at' => 'nullable|date|after_or_equal:checkout_at',
            'condition_out' => 'required|in:excellent,good,fair,poor',
            'condition_in' => 'nullable|in:excellent,good,fair,poor',
            'notes' => 'nullable|string|max:1000',
        ]);

        $assetLoan->update($validated);

        // Update asset status if checkin_at is provided
        if ($validated['checkin_at'] && !$assetLoan->getOriginal('checkin_at')) {
            // Asset is being returned
            $newStatus = AssetStatus::ACTIVE;
            if (isset($validated['condition_in'])) {
                if (in_array($validated['condition_in'], [LoanCondition::POOR->value])) {
                    $newStatus = AssetStatus::MAINTENANCE;
                } elseif (in_array($validated['condition_in'], [LoanCondition::FAIR->value])) {
                    $newStatus = AssetStatus::DAMAGED;
                }
            }
            $assetLoan->asset->update(['status' => $newStatus]);
        } elseif (!$validated['checkin_at'] && $assetLoan->getOriginal('checkin_at')) {
            // Asset is being checked out again
            $assetLoan->asset->update(['status' => AssetStatus::CHECKED_OUT]);
        }

        return redirect()->route('asset-loans.show', $assetLoan)
            ->with('success', 'Asset loan berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssetLoan $assetLoan): RedirectResponse
    {
        // If loan is active, return asset to active status
        if ($assetLoan->isActive()) {
            $assetLoan->asset->update(['status' => AssetStatus::ACTIVE]);
        }

        $assetLoan->delete();

        return redirect()->route('asset-loans.index')
            ->with('success', 'Asset loan berhasil dihapus.');
    }

    /**
     * Return an asset (checkin).
     */
    public function checkin(Request $request, AssetLoan $assetLoan): RedirectResponse
    {
        $validated = $request->validate([
            'checkin_at' => 'required|date',
            'condition_in' => 'required|in:excellent,good,fair,poor',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Update loan record
        $assetLoan->update([
            'checkin_at' => $validated['checkin_at'],
            'condition_in' => $validated['condition_in'],
            'notes' => $assetLoan->notes . ($validated['notes'] ? '\n\nCheckin Notes: ' . $validated['notes'] : ''),
        ]);

        // Determine new asset status based on condition
        $newStatus = AssetStatus::ACTIVE;
        if (in_array($validated['condition_in'], [LoanCondition::POOR->value])) {
            $newStatus = AssetStatus::MAINTENANCE;
        } elseif (in_array($validated['condition_in'], [LoanCondition::FAIR->value])) {
            $newStatus = AssetStatus::DAMAGED;
        }

        // Update asset status and condition
        $assetLoan->asset->update([
            'status' => $newStatus,
            'condition' => $validated['condition_in']
        ]);

        return redirect()->route('asset-loans.show', $assetLoan)
            ->with('success', 'Asset berhasil di-checkin.');
    }
}