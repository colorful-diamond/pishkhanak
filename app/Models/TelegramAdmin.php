<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TelegramAdmin extends Model
{
    use HasFactory;

    protected $fillable = [
        'telegram_user_id',
        'username',
        'first_name',
        'last_name',
        'role',
        'permissions',
        'is_active',
        'last_login_at',
        'failed_login_attempts',
        'locked_until',
        'created_by'
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
        'failed_login_attempts' => 'integer'
    ];

    protected $hidden = [
        'id'
    ];

    // Permission levels with numeric values for easy comparison
    private const PERMISSION_LEVELS = [
        'read_only' => 1,
        'support' => 2,
        'moderator' => 3,
        'admin' => 4,
        'super_admin' => 5
    ];

    /**
     * Get all audit logs for this admin
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(TelegramAuditLog::class, 'admin_id');
    }

    /**
     * Get all active sessions for this admin
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(TelegramAdminSession::class, 'admin_id');
    }

    /**
     * Get security events for this admin
     */
    public function securityEvents(): HasMany
    {
        return $this->hasMany(TelegramSecurityEvent::class, 'admin_id');
    }

    /**
     * Get posts created by this admin
     */
    public function posts(): HasMany
    {
        return $this->hasMany(TelegramPost::class, 'created_by');
    }

    /**
     * Get AI templates created by this admin
     */
    public function aiTemplates(): HasMany
    {
        return $this->hasMany(AiContentTemplate::class, 'created_by');
    }

    /**
     * Admin who created this admin
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(self::class, 'created_by', 'telegram_user_id');
    }

    /**
     * Check if admin has specific permission
     */
    public function hasPermission(string $permission): bool
    {
        // Super admin has all permissions
        if ($this->role === 'super_admin') {
            return true;
        }

        // Check explicit permissions
        $permissions = $this->permissions ?? [];
        return in_array($permission, $permissions);
    }

    /**
     * Check if admin can access specific command
     */
    public function canAccessCommand(string $command): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->isLocked()) {
            return false;
        }

        $requiredLevel = $this->getCommandPermissionLevel($command);
        $userLevel = self::PERMISSION_LEVELS[$this->role] ?? 0;

        return $userLevel >= $requiredLevel;
    }

    /**
     * Check if admin account is locked
     */
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Lock admin account for security
     */
    public function lockAccount(int $minutes = 30): void
    {
        $this->update([
            'locked_until' => now()->addMinutes($minutes),
            'failed_login_attempts' => $this->failed_login_attempts + 1
        ]);
    }

    /**
     * Unlock admin account
     */
    public function unlockAccount(): void
    {
        $this->update([
            'locked_until' => null,
            'failed_login_attempts' => 0
        ]);
    }

    /**
     * Update last login timestamp
     */
    public function recordLogin(): void
    {
        $this->update([
            'last_login_at' => now(),
            'failed_login_attempts' => 0,
            'locked_until' => null
        ]);
    }

    /**
     * Get permission level required for command
     */
    private function getCommandPermissionLevel(string $command): int
    {
        $commandPermissions = config('telegram-admin.command_permissions', []);
        $requiredRole = $commandPermissions[$command] ?? 'admin';
        
        return self::PERMISSION_LEVELS[$requiredRole] ?? 4;
    }

    /**
     * Get formatted display name
     */
    public function getDisplayNameAttribute(): string
    {
        $name = $this->first_name;
        if ($this->last_name) {
            $name .= ' ' . $this->last_name;
        }
        if ($this->username) {
            $name .= " (@{$this->username})";
        }
        return $name;
    }

    /**
     * Get role display name in Persian
     */
    public function getRoleDisplayAttribute(): string
    {
        $roleNames = [
            'super_admin' => 'مدیر کل',
            'admin' => 'مدیر',
            'moderator' => 'مدیر محتوا',
            'support' => 'پشتیبان',
            'read_only' => 'مشاهده‌گر'
        ];

        return $roleNames[$this->role] ?? $this->role;
    }

    /**
     * Scope for active admins only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for admins with specific role or higher
     */
    public function scopeWithRoleOrHigher($query, string $role)
    {
        $minimumLevel = self::PERMISSION_LEVELS[$role] ?? 1;
        $allowedRoles = array_keys(array_filter(
            self::PERMISSION_LEVELS,
            fn($level) => $level >= $minimumLevel
        ));

        return $query->whereIn('role', $allowedRoles);
    }

    /**
     * Scope for recently active admins
     */
    public function scopeRecentlyActive($query, int $days = 30)
    {
        return $query->where('last_login_at', '>=', now()->subDays($days));
    }
}