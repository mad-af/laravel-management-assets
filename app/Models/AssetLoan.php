<?php

namespace App\Models;

use App\Enums\LoanCondition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetLoan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'asset_id',
        'borrower_name',
        'checkout_at',
        'due_at',
        'checkin_at',
        'condition_out',
        'condition_in',
        'notes',
    ];

    protected $casts = [
        'checkout_at' => 'datetime',
        'due_at' => 'datetime',
        'checkin_at' => 'datetime',
        'condition_out' => LoanCondition::class,
        'condition_in' => LoanCondition::class,
    ];

    /**
     * Get the asset that is being loaned.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }



    /**
     * Check if the loan is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->checkin_at === null && $this->due_at < now();
    }

    /**
     * Check if the loan is active (not returned).
     */
    public function isActive(): bool
    {
        return $this->checkin_at === null;
    }

    /**
     * Scope a query to only include active loans.
     */
    public function scopeActive($query)
    {
        return $query->whereNull('checkin_at');
    }

    /**
     * Scope a query to only include overdue loans.
     */
    public function scopeOverdue($query)
    {
        return $query->whereNull('checkin_at')->where('due_at', '<', now());
    }

    /**
     * Check if asset was damaged during loan
     */
    public function wasDamaged(): bool
    {
        if (!$this->condition_in || !$this->condition_out) {
            return false;
        }
        
        return $this->condition_in->isDamaged() && !$this->condition_out->isDamaged();
    }

    /**
     * Check if condition deteriorated during loan
     */
    public function conditionDeteriorated(): bool
    {
        if (!$this->condition_in || !$this->condition_out) {
            return false;
        }
        
        // Convert to asset conditions for comparison
        $outCondition = $this->condition_out->toAssetCondition();
        $inCondition = $this->condition_in->toAssetCondition();
        
        return $inCondition->score() < $outCondition->score();
    }

    /**
     * Get condition change description
     */
    public function getConditionChange(): ?string
    {
        if (!$this->condition_in || !$this->condition_out) {
            return null;
        }
        
        if ($this->condition_out === $this->condition_in) {
            return 'No change';
        }
        
        return "From {$this->condition_out->label()} to {$this->condition_in->label()}";
    }

    /**
     * Scope to get loans with condition changes
     */
    public function scopeWithConditionChanges($query)
    {
        return $query->whereNotNull('condition_in')
                    ->whereNotNull('condition_out')
                    ->whereColumn('condition_in', '!=', 'condition_out');
    }

    /**
     * Scope to get loans where asset was damaged
     */
    public function scopeDamaged($query)
    {
        return $query->whereNotNull('condition_in')
                    ->whereIn('condition_in', [LoanCondition::DAMAGED, LoanCondition::POOR]);
    }
}
