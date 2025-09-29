<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\Company;
use App\Support\SessionKey;
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
        $this->setSelectedBranch();
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
                        'id' => $b->id,
                        'name' => $b->name,
                    ])
                    ->toArray();
            }
        }

        $this->grouped = $grouped;
    }

    /**
     * Set selected branch dari session atau default ke HQ branch
     */
    protected function setSelectedBranch(): void
    {
        // Cek apakah ada branch yang tersimpan di session
        $sessionBranch = session_get(SessionKey::BranchId);

        if ($sessionBranch && $this->isBranchValid($sessionBranch)) {
            $this->selectedBranch = $sessionBranch;

            return;
        }

        // Jika tidak ada di session atau tidak valid, ambil default
        $defaultBranch = $this->getDefaultBranch();
        if ($defaultBranch) {
            $this->selectedBranch = $defaultBranch;
            session_put(SessionKey::BranchId, $defaultBranch);
        }
    }

    /**
     * Cek apakah branch ID valid (ada dalam grouped options)
     */
    protected function isBranchValid(string $branchId): bool
    {
        foreach ($this->grouped as $branches) {
            foreach ($branches as $branch) {
                if ($branch['id'] == $branchId) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Ambil default branch (HQ dari perusahaan pertama)
     */
    protected function getDefaultBranch(): ?string
    {
        if (empty($this->grouped)) {
            return null;
        }

        // Ambil perusahaan pertama
        $firstCompanyBranches = reset($this->grouped);

        // Cari branch dengan nama 'HQ' atau 'Pusat' (case insensitive)
        foreach ($firstCompanyBranches as $branch) {
            if (in_array(strtolower($branch['name']), ['hq', 'pusat', 'head office', 'kantor pusat'])) {
                return $branch['id'];
            }
        }

        // Jika tidak ada HQ, ambil branch pertama
        return $firstCompanyBranches[0]['id'] ?? null;
    }

    /**
     * Dipanggil otomatis saat user memilih branch.
     * Bisa digunakan untuk filter global (mis. simpan ke session atau emit event).
     */
    public function updatedSelectedBranch($value): void
    {
        // Simpan ke session
        session_put(SessionKey::BranchId, $value);
    }

    public function render()
    {
        return view('livewire.branch-switcher');
    }
}
