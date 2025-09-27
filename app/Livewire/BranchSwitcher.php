<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BranchSwitcher extends Component
{
    /** @var array<string, array<int, array{id:string,name:string}>> */
    public array $grouped = [];

    public ?string $selectedBranch = null;

    public function mount(): void
    {
        $this->loadBranches();
    }

    /**
     * Ambil daftar company milik user dan branch tiap company, lalu buat array grouped.
     */
    protected function loadBranches(): void
    {
        $user = Auth::user();

        // ambil id company milik user, sesuaikan jika relasi user->companies berbeda
        $companyIds = method_exists($user, 'companies')
            ? $user->companies()->pluck('company_id')
            : collect();

        $companies = Company::whereIn('id', $companyIds)->where('is_active', true)->get();

        $grouped = [];

        foreach ($companies as $company) {
            $branches = Branch::where('company_id', $company->id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']);

            if ($branches->isNotEmpty()) {
                $grouped[$company->name] = $branches
                    ->map(fn ($b) => [
                        'id'   => $b->id,
                        'name' => $b->name,
                    ])
                    ->toArray();
            }
        }

        $this->grouped = $grouped;
    }

    /**
     * Dipanggil otomatis saat user memilih branch.
     * Bisa digunakan untuk filter global (mis. simpan ke session atau emit event).
     */
    public function updatedSelectedBranch($value): void
    {
        // Contoh: simpan ke session agar bisa dipakai di page lain
        session(['selected_branch' => $value]);

        // Atau kirim event ke komponen lain
        $this->dispatch('branch-switched', branchId: $value);
    }

    public function render()
    {
        return view('livewire.branch-switcher');
    }
}
