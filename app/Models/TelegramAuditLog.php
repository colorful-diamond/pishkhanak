<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TelegramAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'action',
        'resource_type',
        'resource_id',
        'old_values',
        'new_values',
        'ip_hash',
        'user_agent_hash',
        'success',
        'error_message'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'success' => 'boolean'
    ];

    /**
     * Admin who performed the action
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(TelegramAdmin::class, 'admin_id');
    }

    /**
     * Get action display name in Persian
     */
    public function getActionDisplayAttribute(): string
    {
        $actionNames = [
            // User Management
            'user_view' => 'مشاهده کاربر',
            'user_ban' => 'مسدود کردن کاربر',
            'user_unban' => 'رفع مسدودیت کاربر',
            'user_delete' => 'حذف کاربر',
            'user_edit' => 'ویرایش کاربر',

            // Wallet Management
            'wallet_view' => 'مشاهده کیف پول',
            'wallet_balance_adjust' => 'تعدیل موجودی',
            'wallet_freeze' => 'مسدود کردن کیف پول',
            'wallet_unfreeze' => 'رفع مسدودیت کیف پول',
            'wallet_transaction_view' => 'مشاهده تراکنش‌ها',

            // Ticket Management
            'ticket_view' => 'مشاهده تیکت',
            'ticket_create' => 'ایجاد تیکت',
            'ticket_assign' => 'تخصیص تیکت',
            'ticket_resolve' => 'حل تیکت',
            'ticket_escalate' => 'ارجاع تیکت',
            'ticket_delete' => 'حذف تیکت',

            // Post Management
            'post_create' => 'ایجاد پست',
            'post_edit' => 'ویرایش پست',
            'post_publish' => 'انتشار پست',
            'post_schedule' => 'زمان‌بندی پست',
            'post_delete' => 'حذف پست',

            // AI Content
            'ai_generate' => 'تولید محتوای AI',
            'ai_template_create' => 'ایجاد الگوی AI',
            'ai_template_edit' => 'ویرایش الگوی AI',

            // Configuration
            'config_view' => 'مشاهده تنظیمات',
            'config_update' => 'بروزرسانی تنظیمات',
            'settings_change' => 'تغییر تنظیمات',

            // Security
            'admin_login' => 'ورود مدیر',
            'admin_logout' => 'خروج مدیر',
            'admin_create' => 'ایجاد مدیر',
            'admin_role_change' => 'تغییر نقش مدیر',
            'token_create' => 'ایجاد توکن',
            'token_revoke' => 'لغو توکن',

            // System
            'system_backup' => 'پشتیبان‌گیری سیستم',
            'system_maintenance' => 'تعمیر و نگهداری',
            'broadcast_send' => 'ارسال پیام همگانی',
        ];

        return $actionNames[$this->action] ?? $this->action;
    }

    /**
     * Get resource type display name in Persian
     */
    public function getResourceTypeDisplayAttribute(): string
    {
        if (!$this->resource_type) {
            return '';
        }

        $resourceTypes = [
            'user' => 'کاربر',
            'wallet' => 'کیف پول',
            'ticket' => 'تیکت',
            'post' => 'پست',
            'admin' => 'مدیر',
            'token' => 'توکن',
            'config' => 'تنظیمات',
            'template' => 'الگو',
            'system' => 'سیستم'
        ];

        return $resourceTypes[$this->resource_type] ?? $this->resource_type;
    }

    /**
     * Get formatted changes summary
     */
    public function getChangesSummaryAttribute(): string
    {
        if (!$this->old_values && !$this->new_values) {
            return '';
        }

        $changes = [];

        if ($this->old_values && $this->new_values) {
            $oldValues = $this->old_values;
            $newValues = $this->new_values;

            foreach ($newValues as $field => $newValue) {
                $oldValue = $oldValues[$field] ?? null;
                if ($oldValue !== $newValue) {
                    $changes[] = "{$field}: {$oldValue} → {$newValue}";
                }
            }
        } elseif ($this->new_values) {
            foreach ($this->new_values as $field => $value) {
                $changes[] = "{$field}: {$value}";
            }
        }

        return implode(', ', $changes);
    }

    /**
     * Scope for successful actions only
     */
    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    /**
     * Scope for failed actions only
     */
    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    /**
     * Scope for specific action types
     */
    public function scopeOfAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for specific resource types
     */
    public function scopeOfResourceType($query, string $resourceType)
    {
        return $query->where('resource_type', $resourceType);
    }

    /**
     * Scope for recent logs
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for critical actions that require attention
     */
    public function scopeCritical($query)
    {
        $criticalActions = [
            'user_delete', 'admin_create', 'admin_role_change',
            'token_create', 'config_update', 'system_maintenance'
        ];

        return $query->whereIn('action', $criticalActions);
    }
}