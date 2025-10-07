<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleTaxHistory extends Model
{
    use HasUuids;

    protected $fillable = [
        'vehicle_tax_type_id',
        'asset_id',
        'paid_date',
        'year',
        'amount',
        'receipt_no',
        'notes',
    ];

    protected $casts = [
        'paid_date' => 'date',
        'amount' => 'decimal:2',
        'year' => 'integer',
    ];

    /**
     * Get the vehicle tax type that owns the vehicle tax history.
     */
    public function vehicleTaxType(): BelongsTo
    {
        return $this->belongsTo(VehicleTaxType::class);
    }

    /**
     * Get the asset that owns the vehicle tax history.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
