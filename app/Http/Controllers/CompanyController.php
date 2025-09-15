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
        $companies = Company::withCount(['users', 'assets'])
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->paginate(10);
        return view('dashboard.companies.index', compact('companies'));
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only([
            'name', 'code', 'tax_id', 'address', 'phone', 'email', 'website'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . Str::slug($request->name) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('storage/companies'), $imageName);
            $data['image'] = 'companies/' . $imageName;
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only([
            'name', 'code', 'tax_id', 'address', 'phone', 'email', 'website'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($company->image && file_exists(public_path('storage/' . $company->image))) {
                unlink(public_path('storage/' . $company->image));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . Str::slug($request->name) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('storage/companies'), $imageName);
            $data['image'] = 'companies/' . $imageName;
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

        // Delete image if exists
        if ($company->image && file_exists(public_path('storage/' . $company->image))) {
            unlink(public_path('storage/' . $company->image));
        }

        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', 'Company deleted successfully.');
    }

    /**
     * Toggle company active status.
     */
    public function activate(Company $company)
    {
        $company->update([
            'is_active' => !$company->is_active
        ]);

        $status = $company->is_active ? 'activated' : 'deactivated';
        return redirect()->route('companies.index')
            ->with('success', "Company {$status} successfully.");
    }
}