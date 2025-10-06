<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class VehicleProfile extends Model
{
    use HasUuids;

    protected $primaryKey = 'asset_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'asset_id',
        'year_purchase',
        'year_manufacture',
        'current_odometer_km',
        'last_service_date',
        'service_target_odometer_km',
        'next_service_date',
        'annual_tax_due_date',
        'plate_no',
        'vin',
    ];

    protected $casts = [
        'asset_id' => 'string',
        'last_service_date' => 'date',
        'next_service_date' => 'date',
        'annual_tax_due_date' => 'date',
        'current_odometer_km' => 'integer',
        'service_target_odometer_km' => 'integer',
        'year_purchase' => 'integer',
        'year_manufacture' => 'integer',
    ];

    /**
     * Get the asset that owns the vehicle profile.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    /**
     * Scope untuk kendaraan yang pajak tahunannya sudah terlambat (overdue)
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('annual_tax_due_date', '<', now())
            ->whereNotNull('annual_tax_due_date');
    }

    /**
     * Scope untuk kendaraan yang pajak tahunannya akan jatuh tempo dalam 3 bulan
     * ATAU memiliki vehicle_taxes yang sudah 9 bulan dan belum dibayar
     */
    public function scopeDueSoon(Builder $query): Builder
    {
        return $query->where(function ($q) {
            // Vehicle profile due soon (dalam 3 bulan)
            $q->where('annual_tax_due_date', '>', now())
                ->where('annual_tax_due_date', '<=', now()->addMonths(3))
                ->whereNotNull('annual_tax_due_date');
        })->orWhereHas('asset.vehicleTaxes', function ($q) {
            // ATAU vehicle taxes yang sudah 9 bulan dan belum dibayar
            $q->where('due_date', '<=', now()->subMonths(9))
                ->whereNull('payment_date');
        });
    }

    /**
     * Scope untuk kendaraan yang sudah membayar pajak dalam 9 bulan terakhir
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->whereHas('asset.vehicleTaxes', function ($q) {
            $q->whereNotNull('payment_date')
                ->where('due_date', '>=', now()->subMonths(9));
        });
    }

    /**
     * Scope untuk kendaraan yang tidak memiliki tanggal jatuh tempo pajak tahunan
     */
    public function scopeNotValid(Builder $query): Builder
    {
        return $query->whereNull('annual_tax_due_date');
    }
}
