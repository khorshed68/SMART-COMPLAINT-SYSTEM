<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Complaint extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'priority',
        'status',
        'assigned_to',
        'resolution_notes',
        'attachment',
        'location',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    /**
     * Submitter of the complaint.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Assigned admin.
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Category of the complaint.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Timeline updates/comments.
     */
    public function updates(): HasMany
    {
        return $this->hasMany(ComplaintUpdate::class);
    }

    /**
     * Notifications related to this complaint.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Scope filter by status.
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope filter by priority.
     */
    public function scopePriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope filter by category.
     */
    public function scopeCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope filter by search term.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /**
     * Scope filter by owner user_id.
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Accessor for status CSS color class or color code.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'Pending' => '#f39c12',
            'In Progress' => '#3498db',
            'Resolved' => '#2ecc71',
            'Rejected' => '#e74c3c',
            default => '#95a5a6'
        };
    }

    /**
     * Accessor for priority CSS color class or color code.
     */
    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'High' => '#e74c3c',
            'Medium' => '#f39c12',
            'Low' => '#2ecc71',
            default => '#95a5a6'
        };
    }
}
