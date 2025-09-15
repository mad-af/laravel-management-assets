<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'code',
        'tax_id',
        'address',
        'phone',
        'email',
        'website',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}
