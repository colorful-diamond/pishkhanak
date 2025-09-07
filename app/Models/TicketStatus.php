<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketStatus extends Model
{
    use HasFactory;

    protected $table = 'ticket_statuses';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'is_active',
        'is_default',
        'is_closed',
        'is_resolved',
        'requires_user_action',
        'auto_close_after', // minutes
        'sort_order',
        'next_status_options', // JSON array of allowed next statuses
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'is_closed' => 'boolean',
        'is_resolved' => 'boolean',
        'requires_user_action' => 'boolean',
        'auto_close_after' => 'integer',
        'sort_order' => 'integer',
        'next_status_options' => 'array',
    ];

    /**
     * Get tickets with this status
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'status_id');
    }

    /**
     * Scope for active statuses
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for open statuses (not closed or resolved)
     */
    public function scopeOpen($query)
    {
        return $query->where('is_closed', false)->where('is_resolved', false);
    }

    /**
     * Scope for closed statuses
     */
    public function scopeClosed($query)
    {
        return $query->where('is_closed', true);
    }

    /**
     * Scope for resolved statuses
     */
    public function scopeResolved($query)
    {
        return $query->where('is_resolved', true);
    }

    /**
     * Scope for sorted statuses
     */
    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get the default status
     */
    public static function getDefault()
    {
        return static::where('is_default', true)->first();
    }

    /**
     * Get formatted auto close time
     */
    public function getFormattedAutoCloseTimeAttribute(): string
    {
        if (!$this->auto_close_after) {
            return 'خودکار نیست';
        }

        $hours = floor($this->auto_close_after / 60);
        $minutes = $this->auto_close_after % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours} ساعت و {$minutes} دقیقه";
        } elseif ($hours > 0) {
            return "{$hours} ساعت";
        } else {
            return "{$minutes} دقیقه";
        }
    }

    /**
     * Get ticket count for this status
     */
    public function getTicketCountAttribute(): int
    {
        return $this->tickets()->count();
    }

    /**
     * Check if a specific status can transition to this status
     */
    public function canTransitionFrom(TicketStatus $fromStatus): bool
    {
        if (!$fromStatus->next_status_options) {
            return true; // No restrictions
        }

        return in_array($this->id, $fromStatus->next_status_options);
    }

    /**
     * Get available next statuses
     */
    public function getNextStatusOptions()
    {
        if (!$this->next_status_options) {
            return static::active()->where('id', '!=', $this->id)->get();
        }

        return static::active()->whereIn('id', $this->next_status_options)->get();
    }

    /**
     * Get CSS classes for this status
     */
    public function getCssClassesAttribute(): string
    {
        if ($this->is_closed) {
            return 'text-gray-700 bg-gray-100 border-gray-200';
        } elseif ($this->is_resolved) {
            return 'text-green-700 bg-green-100 border-green-200';
        } elseif ($this->requires_user_action) {
            return 'text-purple-700 bg-purple-100 border-purple-200';
        } else {
            return 'text-blue-700 bg-blue-100 border-blue-200';
        }
    }

    /**
     * Check if this status indicates the ticket is effectively closed
     */
    public function isEffectivelyClosed(): bool
    {
        return $this->is_closed || $this->is_resolved;
    }

    /**
     * Check if this status requires customer action
     */
    public function requiresCustomerAction(): bool
    {
        return $this->requires_user_action;
    }
} 