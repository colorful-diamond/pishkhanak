<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class TicketActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'action',
        'description',
        'old_values',
        'new_values',
        'is_public',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'is_public' => 'boolean',
    ];

    /**
     * Get the ticket this activity belongs to
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user who performed this action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create an activity log entry
     */
    public static function log(Ticket $ticket, string $action, string $description, ?User $user = null, array $oldValues = [], array $newValues = [], bool $isPublic = true): self
    {
        return static::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user?->id ?? Auth::id(),
            'action' => $action,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'is_public' => $isPublic,
        ]);
    }

    /**
     * Get formatted action description
     */
    public function getFormattedDescriptionAttribute(): string
    {
        $userName = $this->user?->name ?? 'سیستم';
        
        return match($this->action) {
            'created' => "{$userName} تیکت را ایجاد کرد",
            'status_changed' => "{$userName} وضعیت تیکت را تغییر داد",
            'priority_changed' => "{$userName} اولویت تیکت را تغییر داد",
            'assigned' => "{$userName} تیکت را اختصاص داد",
            'unassigned' => "{$userName} اختصاص تیکت را لغو کرد",
            'message_added' => "{$userName} پیام جدیدی اضافه کرد",
            'escalated' => "{$userName} تیکت را ارجاع داد",
            'closed' => "{$userName} تیکت را بست",
            'reopened' => "{$userName} تیکت را مجدداً باز کرد",
            default => $this->description
        };
    }

    /**
     * Get icon for this activity
     */
    public function getIconAttribute(): string
    {
        return match($this->action) {
            'created' => 'heroicon-o-plus-circle',
            'status_changed' => 'heroicon-o-arrow-path',
            'priority_changed' => 'heroicon-o-exclamation-triangle',
            'assigned' => 'heroicon-o-user-plus',
            'unassigned' => 'heroicon-o-user-minus',
            'message_added' => 'heroicon-o-chat-bubble-left',
            'escalated' => 'heroicon-o-arrow-trending-up',
            'closed' => 'heroicon-o-lock-closed',
            'reopened' => 'heroicon-o-lock-open',
            default => 'heroicon-o-information-circle'
        };
    }

    /**
     * Get color for this activity
     */
    public function getColorAttribute(): string
    {
        return match($this->action) {
            'created' => 'green',
            'status_changed' => 'blue',
            'priority_changed' => 'orange',
            'assigned' => 'purple',
            'unassigned' => 'gray',
            'message_added' => 'blue',
            'escalated' => 'red',
            'closed' => 'gray',
            'reopened' => 'green',
            default => 'gray'
        };
    }
} 