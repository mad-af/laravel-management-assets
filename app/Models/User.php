<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
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
        'remember_token',
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

    /**
     * Get the asset loans where this user is the borrower.
     */
    public function assetLoans(): HasMany
    {
        return $this->hasMany(AssetLoan::class, 'borrower_id');
    }

    /**
     * Get the active asset loans for the user.
     */
    public function activeLoans(): HasMany
    {
        return $this->hasMany(AssetLoan::class, 'borrower_id')->whereNull('checkin_at');
    }
}
