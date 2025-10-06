<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\VehicleTax;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleTaxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = VehicleTax::with(['asset']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('receipt_no', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('asset', function ($assetQuery) use ($search) {
                        $assetQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->get('asset_id'));
        }

        $vehicleTaxes = $query->orderBy('due_date', 'desc')->paginate(10);
        $assets = Asset::all();

        return view('dashboard.vehicle-taxes.index', compact('vehicleTaxes', 'assets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $assets = Asset::where('is_active', true)->orderBy('name')->get();

        return view('dashboard.vehicle-taxes.create', compact('assets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'tax_period_start' => 'required|date',
            'tax_period_end' => 'required|date|after:tax_period_start',
            'due_date' => 'required|date',
            'payment_date' => 'nullable|date',
            'amount' => 'required|numeric|min:0',
            'receipt_no' => 'nullable|string|max:64',
            'notes' => 'nullable|string',
        ]);

        VehicleTax::create($validated);

        return redirect()->route('vehicle-taxes.index')
            ->with('success', 'Vehicle tax record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(VehicleTax $vehicleTax): View
    {
        $vehicleTax->load('asset');

        return view('dashboard.vehicle-taxes.show', compact('vehicleTax'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VehicleTax $vehicleTax): View
    {
        $assets = Asset::where('is_active', true)->orderBy('name')->get();

        return view('dashboard.vehicle-taxes.edit', compact('vehicleTax', 'assets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VehicleTax $vehicleTax): RedirectResponse
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'tax_period_start' => 'required|date',
            'tax_period_end' => 'required|date|after:tax_period_start',
            'due_date' => 'required|date',
            'payment_date' => 'nullable|date',
            'amount' => 'required|numeric|min:0',
            'receipt_no' => 'nullable|string|max:64',
            'notes' => 'nullable|string',
        ]);

        $vehicleTax->update($validated);

        return redirect()->route('vehicle-taxes.index')
            ->with('success', 'Vehicle tax record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehicleTax $vehicleTax): RedirectResponse
    {
        $vehicleTax->delete();

        return redirect()->route('vehicle-taxes.index')
            ->with('success', 'Vehicle tax record deleted successfully.');
    }

    /**
     * Get vehicle taxes as JSON for API calls.
     */
    public function api(Request $request): JsonResponse
    {
        $query = VehicleTax::with(['asset']);

        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->get('asset_id'));
        }

        $vehicleTaxes = $query->orderBy('due_date', 'desc')->get();

        return response()->json($vehicleTaxes);
    }
}
