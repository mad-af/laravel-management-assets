<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasUuids, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * Get the asset logs for the user.
     */
    public function assetLogs(): HasMany
    {
        return $this->hasMany(AssetLog::class);
    }

    /**
     * Get the asset logs for the user (alias for consistency).
     */
    public function logs(): HasMany
    {
        return $this->hasMany(AssetLog::class);
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function userCompanies(): HasMany
    {
        return $this->hasMany(UserCompany::class);
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'user_companies', 'user_id', 'company_id');
    }

    public function userBranches(): HasMany
    {
        return $this->hasMany(related: UserBranch::class);
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'user_branches', 'user_id', 'branch_id');
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Isi password default saat pembuatan jika belum di-set
        static::creating(function ($user) {
            if (empty($user->password)) {
                // Cast 'password' => 'hashed' akan otomatis meng-hash nilai ini saat save
                $user->password = 'password';
            }
        });
    }
}
