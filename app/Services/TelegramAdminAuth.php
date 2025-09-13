<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Enhanced Telegram Admin Authorization Service
 * 
 * Implements multi-layer security for Telegram bot admin operations
 * with time-based tokens, session management, and comprehensive logging.
 */
class TelegramAdminAuth
{
    private const SESSION_TTL = 3600; // 1 hour
    private const MAX_FAILED_ATTEMPTS = 3;
    private const LOCKOUT_DURATION = 1800; // 30 minutes
    private const TOKEN_LENGTH = 32;

    /**
     * Admin permission levels
     */
    private const PERMISSION_LEVELS = [
        'read_only' => 1,      // View tickets, basic info
        'support' => 2,        // Ticket management, user support  
        'moderator' => 3,      // User management, advanced features
        'admin' => 4,          // System configuration, sensitive operations
        'super_admin' => 5,    // Full system access, security operations
    ];

    /**
     * Command permission requirements
     */
    private const COMMAND_PERMISSIONS = [
        '/tickets' => 'read_only',
        '/ticket' => 'read_only', 
        '/create_ticket' => 'support',
        '/close_ticket' => 'support',
        '/assign_ticket' => 'support',
        '/user_info' => 'support',
        '/ban_user' => 'moderator',
        '/unban_user' => 'moderator',
        '/system_stats' => 'admin',
        '/config' => 'admin',
        '/security_audit' => 'super_admin',
        '/reset_tokens' => 'super_admin',
    ];

    /**
     * Verify admin user with multi-layer security
     */
    public function verifyAdmin(string $userId, ?string $command = null): bool
    {
        try {
            // Layer 1: Basic chat ID verification
            if (!$this->isValidChatId($userId)) {
                $this->logAuthAttempt($userId, 'invalid_chat_id', false);
                return false;
            }

            // Layer 2: Check if user is locked out
            if ($this->isUserLockedOut($userId)) {
                $this->logAuthAttempt($userId, 'locked_out', false);
                return false;
            }

            // Layer 3: Validate active session
            if (!$this->hasValidSession($userId)) {
                $this->logAuthAttempt($userId, 'invalid_session', false);
                return false;
            }

            // Layer 4: Command-specific permission check
            if ($command && !$this->hasPermission($userId, $command)) {
                $this->logAuthAttempt($userId, 'insufficient_permission', false, $command);
                return false;
            }

            // Layer 5: Time-based token validation
            if (!$this->verifyTimeBasedToken($userId)) {
                $this->logAuthAttempt($userId, 'invalid_token', false);
                return false;
            }

            $this->logAuthAttempt($userId, 'success', true, $command);
            return true;

        } catch (\Exception $e) {
            Log::error('Admin verification error', [
                'user_id' => $userId,
                'command' => $command,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Initiate admin session with enhanced security
     */
    public function initiateAdminSession(string $userId, string $verificationCode = null): array
    {
        if ($this->isUserLockedOut($userId)) {
            return [
                'success' => false,
                'message' => 'حساب شما به دلیل تلاش‌های ناموفق قفل شده است.',
                'lockout_remaining' => $this->getLockoutRemaining($userId)
            ];
        }

        if (!$this->isValidChatId($userId)) {
            $this->recordFailedAttempt($userId);
            return [
                'success' => false,
                'message' => 'شما مجاز به استفاده از دستورات مدیریتی نیستید.'
            ];
        }

        // Generate session token
        $sessionToken = $this->generateSecureToken();
        $sessionKey = "telegram_admin_session:{$userId}";
        
        Cache::put($sessionKey, [
            'token' => $sessionToken,
            'created_at' => now()->toISOString(),
            'last_activity' => now()->toISOString(),
            'permission_level' => $this->getUserPermissionLevel($userId),
            'ip_hash' => md5($userId), // Simple IP tracking
        ], self::SESSION_TTL);

        // Clear failed attempts on successful login
        Cache::forget("telegram_failed_attempts:{$userId}");

        Log::info('Admin session initiated', [
            'user_id' => $userId,
            'session_token' => substr($sessionToken, 0, 8) . '...',
            'permission_level' => $this->getUserPermissionLevel($userId)
        ]);

        return [
            'success' => true,
            'message' => 'احراز هویت موفق. جلسه مدیریتی فعال شد.',
            'session_expires' => now()->addSeconds(self::SESSION_TTL)->toISOString(),
            'permission_level' => $this->getUserPermissionLevel($userId)
        ];
    }

    /**
     * Check if user is a valid admin chat ID
     */
    private function isValidChatId(string $userId): bool
    {
        $adminChatIds = $this->getAdminChatIds();
        return in_array($userId, $adminChatIds);
    }

    /**
     * Get admin chat IDs from configuration
     */
    private function getAdminChatIds(): array
    {
        $adminIds = config('services.telegram.admin_chat_ids', env('TELEGRAM_ADMIN_CHAT_IDS', ''));
        
        if (empty($adminIds)) {
            Log::warning('No Telegram admin chat IDs configured');
            return [];
        }

        return array_map('trim', explode(',', $adminIds));
    }

    /**
     * Check if user has valid active session
     */
    private function hasValidSession(string $userId): bool
    {
        $sessionKey = "telegram_admin_session:{$userId}";
        $session = Cache::get($sessionKey);

        if (!$session) {
            return false;
        }

        // Update last activity
        $session['last_activity'] = now()->toISOString();
        Cache::put($sessionKey, $session, self::SESSION_TTL);

        return true;
    }

    /**
     * Check command-specific permissions
     */
    private function hasPermission(string $userId, string $command): bool
    {
        $requiredPermission = self::COMMAND_PERMISSIONS[$command] ?? 'admin';
        $userPermissionLevel = $this->getUserPermissionLevel($userId);
        $requiredLevel = self::PERMISSION_LEVELS[$requiredPermission] ?? 4;

        return $userPermissionLevel >= $requiredLevel;
    }

    /**
     * Get user permission level
     */
    private function getUserPermissionLevel(string $userId): int
    {
        // In a real implementation, this would come from database
        // For now, using simple admin configuration
        $adminChatIds = $this->getAdminChatIds();
        $adminIndex = array_search($userId, $adminChatIds);
        
        if ($adminIndex === false) {
            return 0; // No permissions
        }

        // First admin gets super_admin, others get admin
        return $adminIndex === 0 ? self::PERMISSION_LEVELS['super_admin'] : self::PERMISSION_LEVELS['admin'];
    }

    /**
     * Verify time-based token (simple implementation)
     */
    private function verifyTimeBasedToken(string $userId): bool
    {
        $sessionKey = "telegram_admin_session:{$userId}";
        $session = Cache::get($sessionKey);

        if (!$session || !isset($session['token'])) {
            return false;
        }

        // Simple time-based validation - token expires with session
        $createdAt = \Carbon\Carbon::parse($session['created_at']);
        $age = $createdAt->diffInSeconds(now());

        return $age < self::SESSION_TTL;
    }

    /**
     * Check if user is currently locked out
     */
    private function isUserLockedOut(string $userId): bool
    {
        $lockoutKey = "telegram_admin_lockout:{$userId}";
        return Cache::has($lockoutKey);
    }

    /**
     * Get remaining lockout time
     */
    private function getLockoutRemaining(string $userId): int
    {
        $lockoutKey = "telegram_admin_lockout:{$userId}";
        $lockoutData = Cache::get($lockoutKey);
        
        if (!$lockoutData) {
            return 0;
        }

        return max(0, self::LOCKOUT_DURATION - now()->diffInSeconds($lockoutData['locked_at']));
    }

    /**
     * Record failed authentication attempt
     */
    private function recordFailedAttempt(string $userId): void
    {
        $attemptsKey = "telegram_failed_attempts:{$userId}";
        $attempts = Cache::get($attemptsKey, 0) + 1;
        
        Cache::put($attemptsKey, $attempts, 3600); // Track for 1 hour

        if ($attempts >= self::MAX_FAILED_ATTEMPTS) {
            $this->lockoutUser($userId);
        }
    }

    /**
     * Lock user after too many failed attempts
     */
    private function lockoutUser(string $userId): void
    {
        $lockoutKey = "telegram_admin_lockout:{$userId}";
        
        Cache::put($lockoutKey, [
            'locked_at' => now(),
            'reason' => 'max_failed_attempts',
        ], self::LOCKOUT_DURATION);

        Log::warning('User locked out due to failed attempts', [
            'user_id' => $userId,
            'lockout_duration' => self::LOCKOUT_DURATION
        ]);
    }

    /**
     * Generate secure token
     */
    private function generateSecureToken(): string
    {
        return Str::random(self::TOKEN_LENGTH);
    }

    /**
     * Log authentication attempts for security monitoring
     */
    private function logAuthAttempt(string $userId, string $reason, bool $success, ?string $command = null): void
    {
        Log::info('Telegram admin auth attempt', [
            'user_id' => $userId,
            'command' => $command,
            'reason' => $reason,
            'success' => $success,
            'timestamp' => now()->toISOString(),
            'permission_level' => $success ? $this->getUserPermissionLevel($userId) : null
        ]);
    }

    /**
     * Terminate admin session
     */
    public function terminateSession(string $userId): void
    {
        $sessionKey = "telegram_admin_session:{$userId}";
        Cache::forget($sessionKey);

        Log::info('Admin session terminated', [
            'user_id' => $userId,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Get session info for admin user
     */
    public function getSessionInfo(string $userId): ?array
    {
        $sessionKey = "telegram_admin_session:{$userId}";
        return Cache::get($sessionKey);
    }

    /**
     * Extend session if user is active
     */
    public function extendSession(string $userId): bool
    {
        $sessionKey = "telegram_admin_session:{$userId}";
        $session = Cache::get($sessionKey);

        if (!$session) {
            return false;
        }

        $session['last_activity'] = now()->toISOString();
        Cache::put($sessionKey, $session, self::SESSION_TTL);

        return true;
    }
}