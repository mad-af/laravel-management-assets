<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Asset extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'code',
        'tag_code',
        'name',
        'category_id',
        'location_id',
        'status',
        'condition',
        'value',
        'purchase_date',
        'description',
        'last_seen_at',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'purchase_date' => 'date',
        'last_seen_at' => 'datetime',
    ];

    /**
     * Get the category that owns the asset.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the location that owns the asset.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the asset logs for the asset.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(AssetLog::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the asset loans for the asset.
     */
    public function loans(): HasMany
    {
        return $this->hasMany(AssetLoan::class)->orderBy('checkout_at', 'desc');
    }

    /**
     * Get the current active loan for the asset.
     */
    public function currentLoan(): HasMany
    {
        return $this->hasMany(AssetLoan::class)->whereNull('checkin_at');
    }

    /**
     * Scope a query to only include assets with a specific status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include assets with a specific condition.
     */
    public function scopeByCondition($query, $condition)
    {
        return $query->where('condition', $condition);
    }

    /**
     * Scope a query to only include assets in a specific category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to only include assets in a specific location.
     */
    public function scopeByLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    /**
     * Get the status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'badge-success',
            'damaged' => 'badge-warning',
            'lost' => 'badge-error',
            'under_maintenance' => 'badge-info',
            'checked_out' => 'badge-primary',
            default => 'badge-ghost',
        };
    }

    /**
     * Get the condition badge color.
     */
    public function getConditionBadgeColorAttribute(): string
    {
        return match($this->condition) {
            'excellent' => 'badge-success',
            'good' => 'badge-primary',
            'fair' => 'badge-warning',
            'poor' => 'badge-error',
            default => 'badge-ghost',
        };
    }

    /**
     * Get the formatted value.
     */
    public function getFormattedValueAttribute(): string
    {
        return 'Rp ' . number_format($this->value, 0, ',', '.');
    }

    /**
     * Get the age in days since purchase.
     */
    public function getAgeInDaysAttribute(): ?int
    {
        return $this->purchase_date ? $this->purchase_date->diffInDays(now()) : null;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($asset) {
            if (Auth::check()) {
                $asset->logs()->create([
                    'user_id' => Auth::id(),
                    'action' => 'created',
                    'notes' => 'Asset created',
                ]);
            }
        });

        static::updated(function ($asset) {
            if ($asset->wasChanged()) {
                $changes = [];
                foreach ($asset->getChanges() as $key => $newValue) {
                    if ($key !== 'updated_at') {
                        $changes[$key] = [
                            'old' => $asset->getOriginal($key),
                            'new' => $newValue,
                        ];
                    }
                }

                if (!empty($changes) && Auth::check()) {
                    $asset->logs()->create([
                        'user_id' => Auth::id(),
                        'action' => 'updated',
                        'changed_fields' => $changes,
                        'notes' => 'Asset updated',
                    ]);
                }
            }
        });

        static::deleted(function ($asset) {
            if (Auth::check()) {
                $asset->logs()->create([
                    'user_id' => Auth::id(),
                    'action' => 'deleted',
                    'notes' => 'Asset deleted',
                ]);
            }
        });
    }
}
