<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.companies.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.companies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:companies,name',
            'code' => 'required|string|max:50|unique:companies,code',
            'tax_id' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only([
            'name', 'code', 'tax_id', 'address', 'phone', 'email', 'website'
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = time() . '_' . Str::slug($request->name) . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('storage/companies'), $logoName);
            $data['logo'] = 'companies/' . $logoName;
        }

        Company::create($data);

        return redirect()->route('companies.index')
            ->with('success', 'Company created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        $company->load(['users', 'assets', 'categories', 'locations']);
        return view('dashboard.companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        return view('dashboard.companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:companies,name,' . $company->id,
            'code' => 'required|string|max:50|unique:companies,code,' . $company->id,
            'tax_id' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only([
            'name', 'code', 'tax_id', 'address', 'phone', 'email', 'website'
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($company->logo && file_exists(public_path('storage/' . $company->logo))) {
                unlink(public_path('storage/' . $company->logo));
            }

            $logo = $request->file('logo');
            $logoName = time() . '_' . Str::slug($request->name) . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('storage/companies'), $logoName);
            $data['logo'] = 'companies/' . $logoName;
        }

        $company->update($data);

        return redirect()->route('companies.index')
            ->with('success', 'Company updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        // Check if company has related data
        if ($company->users()->count() > 0 || $company->assets()->count() > 0) {
            return redirect()->route('companies.index')
                ->with('error', 'Cannot delete company with existing users or assets.');
        }

        // Delete logo if exists
        if ($company->logo && file_exists(public_path('storage/' . $company->logo))) {
            unlink(public_path('storage/' . $company->logo));
        }

        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', 'Company deleted successfully.');
    }

    /**
     * Activate company.
     */
    public function activate(Company $company)
    {
        $company->update(['is_active' => true]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Company activated successfully.'
            ]);
        }

        return redirect()->route('companies.index')
            ->with('success', 'Company activated successfully.');
    }

    /**
     * Deactivate company.
     */
    public function deactivate(Company $company)
    {
        $company->update(['is_active' => false]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Company deactivated successfully.'
            ]);
        }

        return redirect()->route('companies.index')
            ->with('success', 'Company deactivated successfully.');
    }
}