<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'document_id',
        'type',
        'title',
        'message',
        'is_read',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Notification type constants
     */
    const TYPE_DOCUMENT_RECEIVED = 'document_received';
    const TYPE_DOCUMENT_FORWARDED = 'document_forwarded';
    const TYPE_DOCUMENT_REVIEWED = 'document_reviewed';
    const TYPE_DOCUMENT_RELEASED = 'document_released';
    const TYPE_DOCUMENT_SENT = 'document_sent';
    const TYPE_DOCUMENT_ASSIGNED = 'document_assigned';
    const TYPE_DOCUMENT_REJECTED = 'document_rejected';

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the document related to the notification.
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): bool
    {
        $this->is_read = true;
        $this->read_at = now();
        return $this->save();
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
