<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Branch;
use App\Models\UserBranch;

class UserBranchSeeder extends Seeder
{
    /**
     * Seed user_branches so each user is linked to all branches
     * of the companies assigned to them via user_companies.
     */
    public function run(): void
    {
        // Load users with companies to reduce queries
        User::with('userCompanies')
            ->get()
            ->each(function (User $user) {
                // Company IDs assigned to this user
                $companyIds = $user->userCompanies->pluck('company_id')->filter()->unique()->values()->all();

                if (empty($companyIds)) {
                    // If no companies, clear any existing branches
                    UserBranch::syncForUser($user, []);
                    return;
                }

                // All branch IDs under the assigned companies
                $branchIds = Branch::whereIn('company_id', $companyIds)
                    ->pluck('id')
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                // Sync branches for the user (create missing, remove extras)
                UserBranch::syncForUser($user, $branchIds);
            });
    }
}