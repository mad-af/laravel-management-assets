<?php

namespace App\Models;

use App\Enums\AssetStatus;
use App\Enums\AssetCondition;
use App\Enums\AssetLogAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
        'status' => AssetStatus::class,
        'condition' => AssetCondition::class,
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
     * Get the location histories for the asset.
     */
    public function locationHistories(): HasMany
    {
        return $this->hasMany(AssetLocationHistory::class)->orderBy('changed_at', 'desc');
    }

    /**
     * Get the vehicle profile for the asset.
     */
    public function vehicleProfile(): HasOne
    {
        return $this->hasOne(VehicleProfile::class, 'asset_id');
    }

    /**
     * Get the vehicle odometer logs for the asset.
     */
    public function vehicleOdometerLogs(): HasMany
    {
        return $this->hasMany(VehicleOdometerLog::class)->orderBy('read_at', 'desc');
    }

    /**
     * Check if asset is a vehicle.
     */
    public function isVehicle(): bool
    {
        return $this->vehicleProfile()->exists();
    }

    /**
     * Scope a query to only include assets with a specific status.
     */
    public function scopeByStatus($query, AssetStatus|string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include assets with a specific condition.
     */
    public function scopeByCondition($query, AssetCondition|string $condition)
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
            'maintenance' => 'badge-info',
            AssetStatus::CHECKED_OUT->value => 'badge-primary',
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

        static::creating(function ($asset) {
            if (empty($asset->tag_code)) {
                $ulid = (string) Str::ulid(); // 26 char
                $short = substr($ulid, 0, 6) . substr($ulid, -6); // 12 char
                $asset->tag_code = $short;
            }
        });

        static::created(function ($asset) {
            if (Auth::check()) {
                $asset->logs()->create([
                    'user_id' => Auth::id(),
                    'action' => AssetLogAction::CREATED,
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
                        'action' => AssetLogAction::UPDATED,
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
                    'action' => AssetLogAction::DELETED,
                    'notes' => 'Asset deleted',
                ]);
            }
        });
    }

    /**
     * Check if asset is available for checkout
     */
    public function isAvailable(): bool
    {
        return $this->status->isAvailable();
    }

    /**
     * Check if asset is checked out
     */
    public function isCheckedOut(): bool
    {
        return $this->status === AssetStatus::CHECKED_OUT;
    }

    /**
     * Check if asset is under maintenance
     */
    public function isUnderMaintenance(): bool
    {
        return $this->status === AssetStatus::MAINTENANCE;
    }

    /**
     * Check if asset is damaged
     */
    public function isDamaged(): bool
    {
        return $this->status === AssetStatus::DAMAGED;
    }

    /**
     * Check if asset is lost
     */
    public function isLost(): bool
    {
        return $this->status === AssetStatus::LOST;
    }

    /**
     * Check if asset condition needs attention
     */
    public function needsAttention(): bool
    {
        return $this->condition->needsAttention();
    }

    /**
     * Get condition score
     */
    public function getConditionScore(): int
    {
        return $this->condition->score();
    }

    /**
     * Scope to get available assets
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', AssetStatus::ACTIVE);
    }

    /**
     * Scope to get assets that need attention
     */
    public function scopeNeedsAttention($query)
    {
        return $query->whereIn('condition', [AssetCondition::FAIR, AssetCondition::POOR]);
    }
}
