<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class TicketTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'subject',
        'content',
        'category_id',
        'is_active',
        'is_public', // Can be used by all agents
        'created_by',
        'variables', // JSON array of available variables
        'usage_count',
        'sort_order',
        'auto_close_ticket',
        'auto_change_status_to',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'variables' => 'array',
        'usage_count' => 'integer',
        'sort_order' => 'integer',
        'auto_close_ticket' => 'boolean',
    ];

    /**
     * Get the category this template belongs to
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class);
    }

    /**
     * Get the user who created this template
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the status to change to when using this template
     */
    public function autoChangeStatus(): BelongsTo
    {
        return $this->belongsTo(TicketStatus::class, 'auto_change_status_to');
    }

    /**
     * Scope for active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for public templates
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }



    /**
     * Scope for templates by category
     */
    public function scopeForCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope for sorted templates
     */
    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Process template content with variables
     */
    public function processContent(array $variables = []): string
    {
        $content = $this->content;
        
        // Default variables
        $defaultVariables = [
            '{{USER_NAME}}' => $variables['user_name'] ?? '',
            '{{TICKET_NUMBER}}' => $variables['ticket_number'] ?? '',
            '{{SITE_NAME}}' => config('app.name'),
            '{{SUPPORT_EMAIL}}' => config('mail.support_email', 'support@' . config('app.url')),
            '{{DATE}}' => now()->format('Y/m/d'),
            '{{TIME}}' => now()->format('H:i'),
            '{{AGENT_NAME}}' => $variables['agent_name'] ?? Auth::user()?->name ?? '',
        ];

        // Merge with custom variables
        $allVariables = array_merge($defaultVariables, $variables);

        // Replace variables in content
        foreach ($allVariables as $placeholder => $value) {
            $content = str_replace($placeholder, $value, $content);
        }

        return $content;
    }

    /**
     * Process template subject with variables
     */
    public function processSubject(array $variables = []): string
    {
        $subject = $this->subject ?? '';
        
        // Default variables
        $defaultVariables = [
            '{{USER_NAME}}' => $variables['user_name'] ?? '',
            '{{TICKET_NUMBER}}' => $variables['ticket_number'] ?? '',
            '{{SITE_NAME}}' => config('app.name'),
        ];

        // Merge with custom variables
        $allVariables = array_merge($defaultVariables, $variables);

        // Replace variables in subject
        foreach ($allVariables as $placeholder => $value) {
            $subject = str_replace($placeholder, $value, $subject);
        }

        return $subject;
    }

    /**
     * Increment usage count
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Get available variables for this template
     */
    public function getAvailableVariables(): array
    {
        $defaultVariables = [
            '{{USER_NAME}}' => 'نام کاربر',
            '{{TICKET_NUMBER}}' => 'شماره تیکت',
            '{{SITE_NAME}}' => 'نام سایت',
            '{{SUPPORT_EMAIL}}' => 'ایمیل پشتیبانی',
            '{{DATE}}' => 'تاریخ جاری',
            '{{TIME}}' => 'زمان جاری',
            '{{AGENT_NAME}}' => 'نام کارشناس پشتیبانی',
        ];

        // Merge with custom variables from this template
        $customVariables = $this->variables ? 
            array_combine(
                array_keys($this->variables),
                array_values($this->variables)
            ) : [];

        return array_merge($defaultVariables, $customVariables);
    }

    /**
     * Check if template can be used by a specific user
     */
    public function canBeUsedBy(User $user): bool
    {
        return $this->is_active && ($this->is_public || $this->created_by === $user->id);
    }
} 