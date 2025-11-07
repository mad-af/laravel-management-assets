<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBranch extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'branch_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // Sinkronisasi relasi cabang untuk user, mengisi kolom 'id' (UUID) via HasUuids
    public static function syncForUser(User $user, array $branchIds): void
    {
        $branchIds = collect($branchIds)
            ->filter(fn ($id) => ! empty($id))
            ->unique()
            ->values()
            ->all();

        if (count($branchIds) > 0) {
            static::where('user_id', $user->id)
                ->whereNotIn('branch_id', $branchIds)
                ->delete();
        } else {
            static::where('user_id', $user->id)->delete();
        }

        foreach ($branchIds as $branchId) {
            static::firstOrCreate([
                'user_id' => $user->id,
                'branch_id' => $branchId,
            ]);
        }
    }
}
