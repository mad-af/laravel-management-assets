<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use App\Models\UserBranch;
use App\Models\UserCompany;
use Illuminate\Database\Seeder;

class UserBranchSeeder extends Seeder
{
    /**
     * Seed the user_branches pivot based on users' assigned companies.
     */
    public function run(): void
    {
        // Process users in chunks to avoid memory issues on large datasets
        User::query()
            ->select(['id'])
            ->orderBy('id')
            ->chunk(200, function ($users) {
                foreach ($users as $user) {
                    // Companies assigned to this user via pivot model
                    $companyIds = UserCompany::query()
                        ->where('user_id', $user->id)
                        ->pluck('company_id')
                        ->filter()
                        ->unique()
                        ->values()
                        ->all();

                    // All active branches under those companies
                    $branchIds = [];
                    if (! empty($companyIds)) {
                        $branchIds = Branch::query()
                            ->whereIn('company_id', $companyIds)
                            ->where('is_active', true)
                            ->pluck('id')
                            ->filter()
                            ->unique()
                            ->values()
                            ->all();
                    }

                    // Sync branches for the user (adds/removes as needed)
                    UserBranch::syncForUser($user, $branchIds);
                }
            });
    }
}