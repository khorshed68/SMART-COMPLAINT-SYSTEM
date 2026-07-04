<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'category_id',
        'start_time',
        'end_time',
        'created_by',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Scope to return only active announcements.
     */
    public function scopeActive($query)
    {
        $now = Carbon::now();
        return $query->where('start_time', '<=', $now)
                     ->where('end_time', '>=', $now);
    }

    /**
     * Relationship with Category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relationship with User (Creator).
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
