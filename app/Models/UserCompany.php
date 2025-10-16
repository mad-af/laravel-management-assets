<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCompany extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'company_id',
        'company_role',
    ];

    protected $casts = [
        'company_role' => 'string',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Sinkronisasi relasi perusahaan untuk user, mengisi kolom 'id' (UUID) via HasUuids
    public static function syncForUser(User $user, array $companyIds): void
    {
        $companyIds = collect($companyIds)
            ->filter(fn ($id) => ! empty($id))
            ->unique()
            ->values()
            ->all();

        if (count($companyIds) > 0) {
            static::where('user_id', $user->id)
                ->whereNotIn('company_id', $companyIds)
                ->delete();
        } else {
            static::where('user_id', $user->id)->delete();
        }

        foreach ($companyIds as $companyId) {
            static::firstOrCreate([
                'user_id' => $user->id,
                'company_id' => $companyId,
            ]);
        }
    }
}
