<?php

namespace App\Services\Telegram\Core;

use App\Models\TelegramAdmin;
use App\Models\TelegramAdminSession;
use App\Models\TelegramSecurityEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Telegram Admin Authentication Service
 * 
 * Handles secure authentication, session management, and authorization
 * for Telegram admin panel access with comprehensive security features.
 */
class AdminAuthService
{
    private const SESSION_DURATION = 3600; // 1 hour in seconds
    private const MAX_FAILED_ATTEMPTS = 5;
    private const LOCKOUT_DURATION = 1800; // 30 minutes in seconds

    public function __construct(
        private AuditLogger $auditLogger
    ) {}

    /**
     * Authenticate admin user and create session
     */
    public function authenticate(string $telegramUserId, array $context = []): AuthenticationResult
    {
        DB::beginTransaction();
        
        try {
            // Find admin user
            $admin = TelegramAdmin::where('telegram_user_id', $telegramUserId)->first();
            
            if (!$admin) {
                $this->logSecurityEvent('auth_attempt_invalid_user', 'warning', [
                    'telegram_user_id' => $telegramUserId,
                    'ip_hash' => $this->hashIp($context['ip'] ?? null),
                ]);
                
                return AuthenticationResult::failed('کاربر مجاز نیست');
            }

            // Check if account is active
            if (!$admin->is_active) {
                $this->logSecurityEvent('auth_attempt_inactive_user', 'error', [
                    'admin_id' => $admin->id,
                    'telegram_user_id' => $telegramUserId,
                ]);
                
                return AuthenticationResult::failed('حساب کاربری غیرفعال است');
            }

            // Check if account is locked
            if ($admin->isLocked()) {
                $this->logSecurityEvent('auth_attempt_locked_user', 'error', [
                    'admin_id' => $admin->id,
                    'telegram_user_id' => $telegramUserId,
                    'locked_until' => $admin->locked_until,
                ]);
                
                $minutes = $admin->locked_until->diffInMinutes(now());
                return AuthenticationResult::failed("حساب کاربری قفل شده است. {$minutes} دقیقه منتظر بمانید");
            }

            // Create session
            $session = $this->createSession($admin, $context);
            
            // Record successful login
            $admin->recordLogin();
            
            // Log successful authentication
            $this->auditLogger->logAdminAction(
                $admin->id,
                'admin_login',
                null,
                null,
                ['ip_hash' => $this->hashIp($context['ip'] ?? null)]
            );

            $this->logSecurityEvent('auth_success', 'info', [
                'admin_id' => $admin->id,
                'telegram_user_id' => $telegramUserId,
                'session_id' => $session->id,
            ]);

            DB::commit();
            
            return AuthenticationResult::success($admin, $session);

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Admin authentication error', [
                'telegram_user_id' => $telegramUserId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return AuthenticationResult::failed('خطا در احراز هویت');
        }
    }

    /**
     * Validate session and get admin
     */
    public function validateSession(string $sessionToken): ?TelegramAdmin
    {
        // Check cache first
        $cacheKey = "telegram_session:{$sessionToken}";
        $cachedAdminId = Cache::get($cacheKey);
        
        if ($cachedAdminId) {
            $admin = TelegramAdmin::find($cachedAdminId);
            if ($admin && $admin->is_active && !$admin->isLocked()) {
                return $admin;
            }
        }

        // Check database
        $session = TelegramAdminSession::with('admin')
            ->where('session_token', $sessionToken)
            ->where('expires_at', '>', now())
            ->first();

        if (!$session || !$session->admin || !$session->admin->is_active || $session->admin->isLocked()) {
            return null;
        }

        // Update last activity
        $session->update(['last_activity_at' => now()]);
        
        // Cache admin for 5 minutes
        Cache::put($cacheKey, $session->admin->id, 300);
        
        return $session->admin;
    }

    /**
     * Verify admin has permission for specific action
     */
    public function verifyPermission(string $telegramUserId, string $command, array $context = []): bool
    {
        try {
            $admin = TelegramAdmin::where('telegram_user_id', $telegramUserId)->first();
            
            if (!$admin) {
                $this->logSecurityEvent('permission_check_invalid_user', 'warning', [
                    'telegram_user_id' => $telegramUserId,
                    'command' => $command,
                ]);
                return false;
            }

            $hasPermission = $admin->canAccessCommand($command);
            
            // Log permission checks for sensitive commands
            $sensitiveCommands = [
                'config', 'security', 'tokens', 'admin_create', 'user_delete'
            ];
            
            if (in_array($command, $sensitiveCommands)) {
                $this->auditLogger->logAdminAction(
                    $admin->id,
                    'permission_check',
                    'command',
                    $command,
                    [
                        'granted' => $hasPermission,
                        'admin_role' => $admin->role,
                        'ip_hash' => $this->hashIp($context['ip'] ?? null),
                    ]
                );
            }

            if (!$hasPermission) {
                $this->logSecurityEvent('permission_denied', 'warning', [
                    'admin_id' => $admin->id,
                    'telegram_user_id' => $telegramUserId,
                    'command' => $command,
                    'admin_role' => $admin->role,
                ]);
            }

            return $hasPermission;

        } catch (\Exception $e) {
            Log::error('Permission verification error', [
                'telegram_user_id' => $telegramUserId,
                'command' => $command,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Logout admin and invalidate session
     */
    public function logout(string $sessionToken): bool
    {
        try {
            $session = TelegramAdminSession::with('admin')
                ->where('session_token', $sessionToken)
                ->first();

            if ($session) {
                // Log logout
                if ($session->admin) {
                    $this->auditLogger->logAdminAction(
                        $session->admin->id,
                        'admin_logout',
                        'session',
                        $session->id
                    );
                }

                // Remove from cache
                Cache::forget("telegram_session:{$sessionToken}");
                
                // Delete session
                $session->delete();
                
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Logout error', [
                'session_token' => substr($sessionToken, 0, 8) . '...',
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Create admin session
     */
    private function createSession(TelegramAdmin $admin, array $context): TelegramAdminSession
    {
        // Clean up expired sessions
        $this->cleanupExpiredSessions($admin);
        
        // Generate secure session token
        $sessionToken = $this->generateSecureToken();
        
        // Create session record
        $session = TelegramAdminSession::create([
            'admin_id' => $admin->id,
            'session_token' => $sessionToken,
            'ip_hash' => $this->hashIp($context['ip'] ?? null),
            'user_agent_hash' => $this->hashUserAgent($context['user_agent'] ?? null),
            'expires_at' => now()->addSeconds(self::SESSION_DURATION),
        ]);

        // Cache session
        Cache::put("telegram_session:{$sessionToken}", $admin->id, self::SESSION_DURATION);
        
        return $session;
    }

    /**
     * Generate cryptographically secure session token
     */
    private function generateSecureToken(): string
    {
        return hash('sha256', Str::random(64) . microtime(true) . random_bytes(32));
    }

    /**
     * Clean up expired sessions for admin
     */
    private function cleanupExpiredSessions(TelegramAdmin $admin): void
    {
        TelegramAdminSession::where('admin_id', $admin->id)
            ->where('expires_at', '<', now())
            ->delete();
    }

    /**
     * Hash IP address for privacy
     */
    private function hashIp(?string $ip): ?string
    {
        return $ip ? hash('sha256', $ip . config('app.key')) : null;
    }

    /**
     * Hash user agent for privacy
     */
    private function hashUserAgent(?string $userAgent): ?string
    {
        return $userAgent ? hash('sha256', $userAgent . config('app.key')) : null;
    }

    /**
     * Log security event
     */
    private function logSecurityEvent(string $eventType, string $severity, array $details): void
    {
        TelegramSecurityEvent::create([
            'event_type' => $eventType,
            'admin_id' => $details['admin_id'] ?? null,
            'telegram_user_id' => $details['telegram_user_id'] ?? null,
            'ip_hash' => $details['ip_hash'] ?? null,
            'details' => $details,
            'severity' => $severity,
        ]);
    }

    /**
     * Get admin statistics
     */
    public function getAdminStats(): array
    {
        return [
            'total_admins' => TelegramAdmin::count(),
            'active_admins' => TelegramAdmin::active()->count(),
            'online_admins' => TelegramAdminSession::where('expires_at', '>', now())->distinct('admin_id')->count(),
            'recent_logins' => TelegramAdmin::recentlyActive(7)->count(),
            'locked_accounts' => TelegramAdmin::whereNotNull('locked_until')
                ->where('locked_until', '>', now())
                ->count(),
        ];
    }

    /**
     * Get recent security events
     */
    public function getRecentSecurityEvents(int $limit = 10): array
    {
        return TelegramSecurityEvent::with('admin')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($event) {
                return [
                    'type' => $event->event_type,
                    'severity' => $event->severity,
                    'admin' => $event->admin?->display_name ?? 'نامشخص',
                    'details' => $event->details,
                    'time' => $event->created_at->diffForHumans(),
                ];
            })
            ->toArray();
    }
}

/**
 * Authentication Result Value Object
 */
class AuthenticationResult
{
    private function __construct(
        public readonly bool $success,
        public readonly ?string $message = null,
        public readonly ?TelegramAdmin $admin = null,
        public readonly ?TelegramAdminSession $session = null
    ) {}

    public static function success(TelegramAdmin $admin, TelegramAdminSession $session): self
    {
        return new self(true, null, $admin, $session);
    }

    public static function failed(string $message): self
    {
        return new self(false, $message);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getAdmin(): ?TelegramAdmin
    {
        return $this->admin;
    }

    public function getSession(): ?TelegramAdminSession
    {
        return $this->session;
    }
}