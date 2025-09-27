<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = Branch::withCount('assets')
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->paginate(10);
        return view('dashboard.branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.branches.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:locations,name',
            'is_active' => 'boolean',
        ]);

        Branch::create([
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('locations.index')
            ->with('success', 'Location created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Branch $branch)
    {
        $branch->load(['assets' => function($query) {
            $query->with(['category', 'logs' => function($q) {
                $q->latest()->limit(5)->with('user');
            }]);
        }]);
        
        return view('dashboard.branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Branch $branch)
    {
        return view('dashboard.branches.edit', compact('branch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:branches,name,' . $branch->id,
            'is_active' => 'boolean',
        ]);

        $branch->update([
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('branches.index')
            ->with('success', 'Branch updated successfully.');
    }

    /**
     * Deactivate the specified resource.
     */
    public function destroy(Branch $branch)
    {
        // Check if branch has assets
        if ($branch->assets()->count() > 0) {
            return redirect()->route('branches.index')
                ->with('error', 'Cannot deactivate branch that has assets assigned to it.');
        }

        $branch->update(['is_active' => false]);

        return redirect()->route('branches.index')
            ->with('success', 'Branch deactivated successfully.');
    }

    /**
     * Activate the specified resource.
     */
    public function activate(Branch $branch)
    {
        $branch->update(['is_active' => true]);

        return redirect()->route('branches.index')
            ->with('success', 'Branch activated successfully.');
    }
}
