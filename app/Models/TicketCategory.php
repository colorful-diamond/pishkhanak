<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'is_active',
        'auto_assign_to',
        'required_fields',
        'estimated_response_time', // in minutes
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'required_fields' => 'array',
        'estimated_response_time' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get tickets in this category
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'category_id');
    }

    /**
     * Get the user this category auto-assigns to
     */
    public function autoAssignUser()
    {
        return $this->belongsTo(User::class, 'auto_assign_to');
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for sorted categories
     */
    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get formatted estimated response time
     */
    public function getFormattedResponseTimeAttribute(): string
    {
        if (!$this->estimated_response_time) {
            return 'نامحدود';
        }

        $hours = floor($this->estimated_response_time / 60);
        $minutes = $this->estimated_response_time % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours} ساعت و {$minutes} دقیقه";
        } elseif ($hours > 0) {
            return "{$hours} ساعت";
        } else {
            return "{$minutes} دقیقه";
        }
    }

    /**
     * Get ticket count for this category
     */
    public function getTicketCountAttribute(): int
    {
        return $this->tickets()->count();
    }

    /**
     * Get open ticket count for this category
     */
    public function getOpenTicketCountAttribute(): int
    {
        return $this->tickets()->whereIn('status', ['open', 'in_progress', 'waiting_for_user'])->count();
    }
} 