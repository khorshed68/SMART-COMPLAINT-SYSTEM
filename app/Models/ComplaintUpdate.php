<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintUpdate extends Model
{
    // No updated_at field in migration, only created_at
    const UPDATED_AT = null;

    protected $fillable = [
        'complaint_id',
        'updated_by',
        'old_status',
        'new_status',
        'comment',
        'update_type',
        'created_at',
    ];

    /**
     * Get complaint being updated.
     */
    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    /**
     * Get user who performed the update.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
