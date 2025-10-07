<?php

namespace App\Models;

use App\Enums\VehicleTaxTypeEnum;
use Carbon\Carbon;
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
        'due_date',
        'year',
        'amount',
        'receipt_no',
        'notes',
    ];

    protected $casts = [
        'paid_date' => 'date',
        'due_date' => 'date',
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

    /**
     * Create a new VehicleTaxHistory based on VehicleTaxType with smart date calculation
     */
    public static function createFromTaxType(VehicleTaxType $vehicleTaxType): self
    {
        // Check for the latest VehicleTaxHistory for this asset and tax type
        $latestHistory = static::where('asset_id', $vehicleTaxType->asset_id)
            ->whereHas('vehicleTaxType', function ($query) use ($vehicleTaxType) {
                $query->where('tax_type', $vehicleTaxType->tax_type);
            })
            ->orderBy('due_date', 'desc')
            ->first();

        // Calculate the next due date based on tax type and latest history
        $nextDueDate = $vehicleTaxType->due_date;
        if ($latestHistory) {
            $baseDate = $latestHistory->due_date;

            if ($vehicleTaxType->tax_type === VehicleTaxTypeEnum::PKB_TAHUNAN) {
                // PKB Tahunan: add 1 year
                $nextDueDate = Carbon::parse($baseDate)->addYear();
            } elseif ($vehicleTaxType->tax_type === VehicleTaxTypeEnum::KIR) {
                // KIR: add 6 months
                $nextDueDate = Carbon::parse($baseDate)->addMonths(6);
            }
        }

        return static::create([
            'vehicle_tax_type_id' => $vehicleTaxType->id,
            'asset_id' => $vehicleTaxType->asset_id,
            'year' => $nextDueDate ? $nextDueDate->year : now()->year,
            'due_date' => $nextDueDate,
            'paid_date' => null,
            'amount' => null,
            'receipt_no' => null,
            'notes' => null,
        ]);
    }
}
