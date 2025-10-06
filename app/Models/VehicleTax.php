<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class VehicleTax extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'asset_id',
        'tax_period_start',
        'tax_period_end',
        'due_date',
        'payment_date',
        'amount',
        'receipt_no',
        'notes',
    ];

    protected $casts = [
        'tax_period_start' => 'date',
        'tax_period_end' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the asset that owns the vehicle tax.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
