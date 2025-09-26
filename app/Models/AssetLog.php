<?php

namespace App\Models;

use App\Enums\AssetLogAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetLog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'asset_id',
        'user_id',
        'action',
        'changed_fields',
        'notes',
    ];

    protected $casts = [
        'changed_fields' => 'array',
        'action' => AssetLogAction::class,
    ];

    /**
     * Get the asset that owns the log.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get the user that created the log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
