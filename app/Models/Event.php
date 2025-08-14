<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'description',
        'category',
        'event_date',
        'event_time',
        'location',
        'is_deadline',
        'is_active',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime',
        'is_deadline' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Category constants
     */
    const CATEGORY_UNIVERSITY = 'university';
    const CATEGORY_INTERNAL_CAMPUS = 'internal_campus';
    const CATEGORY_EXTERNAL_PARTNERS = 'external_partners';

    /**
     * Get the user who created the event.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for active events
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for upcoming events
     */
    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', now()->toDateString());
    }

    /**
     * Scope for events by category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
