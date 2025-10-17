<?php

namespace App\Models;

use App\Enums\AssetCondition;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetLoan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'asset_id',
        'employee_id',
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
        'condition_out' => AssetCondition::class,
        'condition_in' => AssetCondition::class,
    ];

    /**
     * Get the asset that is being loaned.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get the employee who borrowed the asset.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Helper: whether the loan is currently active (not returned).
     */
    public function isActive(): bool
    {
        return $this->checkin_at === null;
    }

    /**
     * Helper: whether the active loan is overdue based on due_at.
     */
    public function isOverdue(): bool
    {
        return $this->checkin_at === null && $this->due_at !== null && $this->due_at->isPast();
    }

    /**
     * Scope: only active loans (not returned).
     */
    public function scopeActive($query)
    {
        return $query->whereNull('checkin_at');
    }

    /**
     * Scope: active loans that are overdue.
     */
    public function scopeOverdue($query)
    {
        return $query->whereNull('checkin_at')
            ->where('due_at', '<', now());
    }
}