<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::orderBy('is_active', 'desc')
            ->paginate(10);
        return view('dashboard.employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Employee::create([
            'name' => $request->name,
        ]);

        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $employee->load(['assets' => function($query) {
            $query->with(['location', 'logs' => function($q) {
                $q->latest()->limit(5)->with('user');
            }]);
        }]);
        
        return view('dashboard.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        return view('dashboard.employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:employees,name,' . $employee->id,
            'description' => 'nullable|string|max:1000'
        ]);

        $employee->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Deactivate the specified resource.
     */
    public function destroy(Employee $employee)
    {
        // Check if employee has assets
        if ($employee->assets()->count() > 0) {
            return redirect()->route('employees.index')
                ->with('error', 'Cannot deactivate employee that has assets assigned to it.');
        }

        $employee->update(['is_active' => false]);

        return redirect()->route('employees.index')
            ->with('success', 'Employee deactivated successfully.');
    }

    /**
     * Activate the specified resource.
     */
    public function activate(Employee $employee)
    {
        $employee->update(['is_active' => true]);

        return redirect()->route('employees.index')
            ->with('success', 'Employee activated successfully.');
    }
}
