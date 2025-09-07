<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketPriority extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'level', // 1-10, higher = more urgent
        'is_active',
        'auto_escalate_after', // minutes
        'escalate_to_priority_id',
        'sort_order',
        'icon',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'level' => 'integer',
        'auto_escalate_after' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get tickets with this priority
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'priority_id');
    }

    /**
     * Get the priority this escalates to
     */
    public function escalatesToPriority()
    {
        return $this->belongsTo(TicketPriority::class, 'escalate_to_priority_id');
    }

    /**
     * Get priorities that escalate to this one
     */
    public function escalatedFromPriorities()
    {
        return $this->hasMany(TicketPriority::class, 'escalate_to_priority_id');
    }

    /**
     * Scope for active priorities
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for sorted priorities (by level, then sort_order)
     */
    public function scopeSorted($query)
    {
        return $query->orderBy('level', 'desc')->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get formatted auto escalation time
     */
    public function getFormattedEscalationTimeAttribute(): string
    {
        if (!$this->auto_escalate_after) {
            return 'خودکار نیست';
        }

        $hours = floor($this->auto_escalate_after / 60);
        $minutes = $this->auto_escalate_after % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours} ساعت و {$minutes} دقیقه";
        } elseif ($hours > 0) {
            return "{$hours} ساعت";
        } else {
            return "{$minutes} دقیقه";
        }
    }

    /**
     * Get ticket count for this priority
     */
    public function getTicketCountAttribute(): int
    {
        return $this->tickets()->count();
    }

    /**
     * Get open ticket count for this priority
     */
    public function getOpenTicketCountAttribute(): int
    {
        return $this->tickets()->whereIn('status', ['open', 'in_progress', 'waiting_for_user'])->count();
    }

    /**
     * Check if this priority is urgent (level >= 8)
     */
    public function isUrgent(): bool
    {
        return $this->level >= 8;
    }

    /**
     * Check if this priority is high (level >= 6)
     */
    public function isHigh(): bool
    {
        return $this->level >= 6;
    }

    /**
     * Get CSS classes for this priority
     */
    public function getCssClassesAttribute(): string
    {
        if ($this->isUrgent()) {
            return 'text-red-700 bg-red-100 border-red-200';
        } elseif ($this->isHigh()) {
            return 'text-orange-700 bg-orange-100 border-orange-200';
        } elseif ($this->level >= 4) {
            return 'text-yellow-700 bg-yellow-100 border-yellow-200';
        } else {
            return 'text-green-700 bg-green-100 border-green-200';
        }
    }
} 