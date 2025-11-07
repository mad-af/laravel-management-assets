<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\Company;
use App\Models\UserBranch;
use App\Models\UserCompany;
use App\Support\SessionKey;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BranchSwitcher extends Component
{
    /** @var array<string, array<int, array{id:string,name:string,company_id:string}>> */
    public array $grouped = [];

    public ?string $selectedBranch = null;

    public function mount(): void
    {
        $this->loadBranches();
        $this->setSelectedBranch();
    }

    /**
     * Ambil daftar company milik user dan branch tiap company (dibatasi oleh user_branches), lalu buat array grouped.
     */
    protected function loadBranches(): void
    {
        $user = Auth::user();

        // Ambil daftar company/branch yang dimiliki user via tabel pivot secara langsung.
        // Ini menghindari diagnostic "Undefined method" pada objek Auth::user().
        $companyIds = $user
            ? UserCompany::where('user_id', $user->id)->pluck('company_id')
            : collect();

        $allowedBranchIds = $user
            ? UserBranch::where('user_id', $user->id)->pluck('branch_id')
            : collect();

        $companies = Company::whereIn('id', $companyIds)->where('is_active', true)->get();

        $grouped = [];

        foreach ($companies as $company) {
            // Filter branch berdasarkan branch yang memang dimiliki oleh user
            $branches = Branch::where('company_id', $company->id)
                ->whereIn('id', $allowedBranchIds)
                ->where('is_active', true)
                ->orderByRaw('CASE WHEN id = ? THEN 0 ELSE 1 END', [$company->hq_branch_id])
                ->orderBy('name')
                ->get(['id', 'name', 'company_id']);

            if ($branches->isNotEmpty()) {
                $grouped[$company->name] = $branches
                    ->map(fn ($b) => [
                        'id' => $b->id,
                        'name' => $b->name,
                        'company_id' => $b->company_id,
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

            // Pastikan company_id juga tersimpan di session
            $companyId = $this->getCompanyIdByBranchId($sessionBranch);
            if ($companyId && ! session_get(SessionKey::CompanyId)) {
                session_put(SessionKey::CompanyId, $companyId);
            }

            return;
        }

        // Jika tidak ada di session atau tidak valid, ambil default
        $defaultBranch = $this->getDefaultBranch();
        if ($defaultBranch) {
            $this->selectedBranch = $defaultBranch;
            session_put(SessionKey::BranchId, $defaultBranch);

            // Simpan company_id untuk default branch
            $companyId = $this->getCompanyIdByBranchId($defaultBranch);
            if ($companyId) {
                session_put(SessionKey::CompanyId, $companyId);
            }
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
        // Cari company_id dari branch yang dipilih
        $companyId = $this->getCompanyIdByBranchId($value);

        // Simpan ke session
        session_put(SessionKey::BranchId, $value);
        if ($companyId) {
            session_put(SessionKey::CompanyId, $companyId);
        }

        // Reload halaman untuk memperbarui semua data
        $this->js('window.location.reload()');
    }

    /**
     * Ambil company_id berdasarkan branch_id yang dipilih
     */
    protected function getCompanyIdByBranchId(string $branchId): ?string
    {
        foreach ($this->grouped as $branches) {
            foreach ($branches as $branch) {
                if ($branch['id'] == $branchId) {
                    return $branch['company_id'];
                }
            }
        }

        return null;
    }

    public function render()
    {
        return view('livewire.branch-switcher');
    }
}
