<?php

namespace App\Models;

use App\Enums\VehicleTaxTypeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleTaxType extends Model
{
    use HasUuids;

    protected $fillable = [
        'asset_id',
        'tax_type',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
        'tax_type' => VehicleTaxTypeEnum::class,
    ];

    /**
     * Get the asset that owns the vehicle tax type.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get the vehicle tax histories for the vehicle tax type.
     */
    public function vehicleTaxHistories(): HasMany
    {
        return $this->hasMany(VehicleTaxHistory::class);
    }
}
