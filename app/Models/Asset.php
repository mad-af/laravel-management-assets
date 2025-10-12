<?php

namespace App\Models;

use App\Enums\AssetCondition;
use App\Enums\AssetLogAction;
use App\Enums\AssetStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

class Asset extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'code',
        'tag_code',
        'name',
        'category_id',
        'company_id',
        'branch_id',
        'brand',
        'model',
        'image',
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
     * Get the branch that owns the asset.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the company that owns the asset.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
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
     * Get the branch histories for the asset.
     */
    public function branchHistories(): HasMany
    {
        return $this->hasMany(AssetBranchHistory::class)->orderBy('created_at', 'desc');
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
     * Get the odometer logs for the asset (alias for vehicleOdometerLogs).
     */
    public function odometerLogs(): HasMany
    {
        return $this->vehicleOdometerLogs();
    }

    /**
     * Get the maintenances for the asset.
     */
    public function maintenances(): HasMany
    {
        return $this->hasMany(AssetMaintenance::class)->orderBy('scheduled_date', 'desc');
    }

    /**
     * Get the vehicle tax types for the asset.
     */
    public function vehicleTaxTypes(): HasMany
    {
        return $this->hasMany(VehicleTaxType::class)->orderBy('due_date', 'desc');
    }

    /**
     * Get the vehicle tax histories for the asset.
     */
    public function vehicleTaxHistories(): HasMany
    {
        return $this->hasMany(VehicleTaxHistory::class)->orderBy('due_date', 'desc');
    }

    /**
     * Get the latest vehicle tax record.
     */
    public function isVehicle(): bool
    {
        return $this->vehicleProfile()->exists();
    }

    /**
     * Scope untuk kendaraan yang pajak tahunannya sudah terlambat (overdue)
     */
    public function scopeOverdue($query)
    {
        return $query->whereHas('vehicleTaxHistories', function ($q) {
            $q->whereNull('paid_date')
                ->where('due_date', '<', now());
        });
    }

    /**
     * Scope untuk kendaraan yang pajak tahunannya akan jatuh tempo dalam 3 bulan
     */
    public function scopeDueSoon(Builder $query): Builder
    {
        return $query->whereHas('vehicleTaxHistories', function ($q) {
            $q->whereNull('paid_date')
                ->where('due_date', '>', now());
        });
    }

    /**
     * Scope untuk kendaraan yang sudah membayar pajak (tidak masuk kategori due_soon atau overdue)
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->whereHas('vehicleTaxTypes')
            ->whereDoesntHave('vehicleTaxHistories', function ($q) {
                // Tidak memiliki pajak yang overdue
                $q->whereNull('paid_date')
                    ->where('due_date', '<', now());
            })
            ->whereDoesntHave('vehicleTaxHistories', function ($q) {
                // Tidak memiliki pajak yang due soon
                $q->whereNull('paid_date')
                    ->where('due_date', '>', now());
            });
    }

    /**
     * Scope untuk kendaraan yang tidak memiliki vehicle tax types
     */
    public function scopeNotValid(Builder $query): Builder
    {
        return $query->whereDoesntHave('vehicleTaxTypes');
    }

    /**
     * Scope untuk mengambil asset yang merupakan kendaraan berdasarkan kategori
     */
    public function scopeVehicles(Builder $query): Builder
    {
        return $query->whereHas('category', function ($q) {
            $q->where('name', 'Kendaraan');
        });
    }

    /**
     * Scope untuk filter berdasarkan branch tertentu
     */
    public function scopeForBranch(Builder $query, ?string $branchId): Builder
    {
        if ($branchId) {
            return $query->where('branch_id', $branchId);
        }

        return $query;
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

                if (! empty($changes) && Auth::check()) {
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
}
