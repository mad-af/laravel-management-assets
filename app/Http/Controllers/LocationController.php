<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = Location::active()->withCount('assets')->paginate(10);
        return view('dashboard.locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.locations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:locations,name',
        ]);

        Location::create([
            'name' => $request->name,
        ]);

        return redirect()->route('locations.index')
            ->with('success', 'Location created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        $location->load(['assets' => function($query) {
            $query->with(['category', 'assetLogs' => function($q) {
                $q->latest()->limit(5)->with('user');
            }]);
        }]);
        
        return view('dashboard.locations.show', compact('location'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location)
    {
        return view('dashboard.locations.edit', compact('location'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:locations,name,' . $location->id,
            'description' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:500'
        ]);

        $location->update([
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address
        ]);

        return redirect()->route('locations.index')
            ->with('success', 'Location updated successfully.');
    }

    /**
     * Deactivate the specified resource.
     */
    public function destroy(Location $location)
    {
        // Check if location has assets
        if ($location->assets()->count() > 0) {
            return redirect()->route('locations.index')
                ->with('error', 'Cannot deactivate location that has assets assigned to it.');
        }

        $location->update(['is_active' => false]);

        return redirect()->route('locations.index')
            ->with('success', 'Location deactivated successfully.');
    }
}
