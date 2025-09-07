<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'ticket_hash',
        'user_id',
        'subject',
        'description',
        'priority',
        'status',
        'category',
        'priority_id',
        'status_id',
        'category_id',
        'department',
        'assigned_to',
        'resolved_at',
        'closed_at',
        'response_time',
        'resolution_time',
        'first_response_at',
        'escalation_count',
        'escalated_at',
        'escalated_from_priority_id',
        'custom_fields',
        'customer_satisfaction_rating',
        'customer_satisfaction_comment',
        'tags',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'first_response_at' => 'datetime',
        'escalated_at' => 'datetime',
        'custom_fields' => 'array',
        'escalation_count' => 'integer',
        'customer_satisfaction_rating' => 'integer',
        'tags' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = 'TKT-' . date('Y') . '-' . str_pad(static::whereYear('created_at', date('Y'))->count() + 1, 6, '0', STR_PAD_LEFT);
            }
            
            if (empty($ticket->ticket_hash)) {
                $ticket->ticket_hash = static::generateTicketHash();
            }
        });
    }

    /**
     * Generate a unique ticket hash
     */
    public static function generateTicketHash(): string
    {
        do {
            $hash = 'TKT_' . strtoupper(Str::random(24));
        } while (static::where('ticket_hash', $hash)->exists());

        return $hash;
    }

    /**
     * Find ticket by hash
     */
    public static function findByHash(string $hash): ?static
    {
        return static::where('ticket_hash', $hash)->first();
    }

    /**
     * Get the route key name for Laravel route model binding
     */
    public function getRouteKeyName(): string
    {
        return 'ticket_hash';
    }

    /**
     * Get the user that owns the ticket
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the assigned agent
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the ticket category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    /**
     * Get the ticket priority
     */
    public function priority(): BelongsTo
    {
        return $this->belongsTo(TicketPriority::class, 'priority_id');
    }

    /**
     * Get the ticket status
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(TicketStatus::class, 'status_id');
    }

    /**
     * Get the support agent for this ticket
     * Note: Support agent functionality removed
     */
    // public function supportAgent(): BelongsTo
    // {
    //     return $this->belongsTo(SupportAgent::class, 'assigned_to', 'user_id');
    // }

    /**
     * Get ticket activities
     */
    public function activities(): HasMany
    {
        return $this->hasMany(TicketActivity::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get all messages for this ticket
     */
    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get public messages (visible to user)
     */
    public function publicMessages(): HasMany
    {
        return $this->hasMany(TicketMessage::class)->where('is_internal', false)->orderBy('created_at', 'asc');
    }

    /**
     * Get attachments for this ticket
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    /**
     * Get the latest message
     */
    public function latestMessage(): BelongsTo
    {
        return $this->belongsTo(TicketMessage::class)->latest();
    }

    /**
     * Check if ticket is open
     */
    public function isOpen(): bool
    {
        return in_array($this->status, ['open', 'in_progress', 'waiting_for_user']);
    }

    /**
     * Check if ticket is resolved
     */
    public function isResolved(): bool
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

    /**
     * Get priority color
     */
    public function getPriorityColor(): string
    {
        return match($this->priority) {
            'low' => 'text-green-600 bg-green-100',
            'medium' => 'text-yellow-600 bg-yellow-100',
            'high' => 'text-orange-600 bg-orange-100',
            'urgent' => 'text-red-600 bg-red-100',
            default => 'text-gray-600 bg-gray-100',
        };
    }

    /**
     * Get status color
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'open' => 'text-blue-600 bg-blue-100',
            'in_progress' => 'text-yellow-600 bg-yellow-100',
            'waiting_for_user' => 'text-purple-600 bg-purple-100',
            'resolved' => 'text-green-600 bg-green-100',
            'closed' => 'text-gray-600 bg-gray-100',
            default => 'text-gray-600 bg-gray-100',
        };
    }

    /**
     * Get status text in Persian
     */
    public function getStatusText(): string
    {
        return match($this->status) {
            'open' => 'باز',
            'in_progress' => 'در حال بررسی',
            'waiting_for_user' => 'در انتظار پاسخ کاربر',
            'resolved' => 'حل شده',
            'closed' => 'بسته',
            default => 'نامشخص',
        };
    }

    /**
     * Get priority text in Persian
     */
    public function getPriorityText(): string
    {
        return match($this->priority) {
            'low' => 'کم',
            'medium' => 'متوسط',
            'high' => 'زیاد',
            'urgent' => 'فوری',
            default => 'نامشخص',
        };
    }

    /**
     * Get category text in Persian
     */
    public function getCategoryText(): string
    {
        return match($this->category) {
            'technical' => 'فنی',
            'billing' => 'مالی',
            'general' => 'عمومی',
            'bug_report' => 'گزارش خطا',
            'feature_request' => 'درخواست ویژگی',
            default => 'عمومی',
        };
    }

    /**
     * Scope for open tickets
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress', 'waiting_for_user']);
    }

    /**
     * Scope for resolved tickets
     */
    public function scopeResolved($query)
    {
        return $query->whereIn('status', ['resolved', 'closed']);
    }

    /**
     * Scope for user tickets
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for assigned tickets
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }
} 