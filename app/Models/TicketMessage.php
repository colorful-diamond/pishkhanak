<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'is_internal',
        'attachments',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'attachments' => 'array',
    ];

    /**
     * Get the ticket that owns the message
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user that sent the message
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get attachments for this message
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    /**
     * Check if message is from support team
     */
    public function isFromSupport(): bool
    {
        return $this->user->hasRole('admin') || $this->user->hasRole('support');
    }

    /**
     * Get formatted message with line breaks
     */
    public function getFormattedMessage(): string
    {
        return nl2br(e($this->message));
    }

    /**
     * Get formatted file size
     */
    public function getFileSizeAttribute()
    {
        return $this->formatFileSize($this->attributes['file_size'] ?? 0);
    }

    /**
     * Format file size in human readable format
     */
    private function formatFileSize($bytes)
    {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
} 