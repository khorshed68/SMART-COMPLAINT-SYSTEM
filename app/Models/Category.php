<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
        'icon',
        'color',
    ];

    /**
     * Get related complaints.
     */
    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }

    /**
     * Accessor for complaint count.
     */
    public function getComplaintCountAttribute(): int
    {
        return $this->complaints()->count();
    }

    /**
     * Scope to load category with complaint count.
     */
    public function scopeWithComplaintCount(Builder $query): Builder
    {
        return $query->withCount('complaints');
    }
}
