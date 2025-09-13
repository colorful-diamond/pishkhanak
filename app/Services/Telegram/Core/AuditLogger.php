<?php

namespace App\Services\Telegram\Core;

use App\Models\TelegramAuditLog;
use App\Models\TelegramSecurityEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Comprehensive Audit Logger for Telegram Admin Panel
 * 
 * Provides secure, detailed logging of all admin actions with
 * support for compliance requirements and security monitoring.
 */
class AuditLogger
{
    /**
     * Log admin action with full context
     */
    public function logAdminAction(
        int $adminId,
        string $action,
        ?string $resourceType = null,
        ?string $resourceId = null,
        array $context = [],
        ?array $oldValues = null,
        ?array $newValues = null,
        bool $success = true,
        ?string $errorMessage = null
    ): void {
        try {
            TelegramAuditLog::create([
                'admin_id' => $adminId,
                'action' => $action,
                'resource_type' => $resourceType,
                'resource_id' => $resourceId,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_hash' => $this->hashIp($context['ip'] ?? null),
                'user_agent_hash' => $this->hashUserAgent($context['user_agent'] ?? null),
                'success' => $success,
                'error_message' => $errorMessage,
            ]);

            // Also log to Laravel log for critical actions
            if ($this->isCriticalAction($action)) {
                Log::info('Critical admin action', [
                    'admin_id' => $adminId,
                    'action' => $action,
                    'resource_type' => $resourceType,
                    'resource_id' => $resourceId,
                    'success' => $success,
                    'timestamp' => now()->toISOString(),
                ]);
            }

        } catch (\Exception $e) {
            // Don't let audit logging failure break the main operation
            Log::error('Audit logging failed', [
                'admin_id' => $adminId,
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Log user management actions
     */
    public function logUserAction(int $adminId, string $action, string $userId, array $details = []): void
    {
        $this->logAdminAction(
            adminId: $adminId,
            action: $action,
            resourceType: 'user',
            resourceId: $userId,
            context: $details,
            newValues: $details
        );
    }

    /**
     * Log wallet management actions
     */
    public function logWalletAction(
        int $adminId,
        string $action,
        string $walletId,
        array $details = []
    ): void {
        $this->logAdminAction(
            adminId: $adminId,
            action: $action,
            resourceType: 'wallet',
            resourceId: $walletId,
            context: $details,
            newValues: $details
        );

        // Special handling for financial operations
        if (in_array($action, ['balance_adjustment', 'freeze', 'unfreeze'])) {
            $this->logSecurityEvent('wallet_operation', 'info', [
                'admin_id' => $adminId,
                'action' => $action,
                'wallet_id' => $walletId,
                'details' => $details,
            ]);
        }
    }

    /**
     * Log ticket management actions
     */
    public function logTicketAction(int $adminId, string $action, string $ticketId, array $details = []): void
    {
        $this->logAdminAction(
            adminId: $adminId,
            action: $action,
            resourceType: 'ticket',
            resourceId: $ticketId,
            context: $details,
            newValues: $details
        );
    }

    /**
     * Log configuration changes
     */
    public function logConfigChange(
        int $adminId,
        string $configKey,
        $oldValue,
        $newValue,
        array $context = []
    ): void {
        $this->logAdminAction(
            adminId: $adminId,
            action: 'config_update',
            resourceType: 'config',
            resourceId: $configKey,
            context: $context,
            oldValues: ['value' => $oldValue],
            newValues: ['value' => $newValue]
        );

        // Log as security event for sensitive configs
        $sensitiveConfigs = [
            'telegram.bot_token',
            'telegram.webhook_secret',
            'services.openai.key',
            'app.key',
            'database'
        ];

        if (str_contains($configKey, 'secret') || str_contains($configKey, 'key') || 
            collect($sensitiveConfigs)->some(fn($pattern) => str_contains($configKey, $pattern))) {
            
            $this->logSecurityEvent('sensitive_config_change', 'warning', [
                'admin_id' => $adminId,
                'config_key' => $configKey,
                'changed_at' => now()->toISOString(),
            ]);
        }
    }

    /**
     * Log AI content generation
     */
    public function logAIGeneration(int $adminId, string $templateId, array $parameters): void
    {
        $this->logAdminAction(
            adminId: $adminId,
            action: 'ai_generate',
            resourceType: 'ai_template',
            resourceId: $templateId,
            newValues: [
                'parameters' => $parameters,
                'generated_at' => now()->toISOString(),
            ]
        );
    }

    /**
     * Log security events
     */
    public function logSecurityEvent(
        string $eventType,
        string $severity = 'info',
        array $details = []
    ): void {
        try {
            TelegramSecurityEvent::create([
                'event_type' => $eventType,
                'admin_id' => $details['admin_id'] ?? null,
                'telegram_user_id' => $details['telegram_user_id'] ?? null,
                'ip_hash' => $this->hashIp($details['ip'] ?? null),
                'details' => $details,
                'severity' => $severity,
            ]);

            // Log critical events to Laravel log as well
            if ($severity === 'critical' || $severity === 'error') {
                Log::warning('Security event', [
                    'event_type' => $eventType,
                    'severity' => $severity,
                    'details' => $details,
                    'timestamp' => now()->toISOString(),
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Security event logging failed', [
                'event_type' => $eventType,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Log authentication attempts
     */
    public function logAuthAttempt(
        string $telegramUserId,
        bool $success,
        ?string $errorMessage = null,
        array $context = []
    ): void {
        $eventType = $success ? 'auth_success' : 'auth_failure';
        $severity = $success ? 'info' : 'warning';

        $this->logSecurityEvent($eventType, $severity, [
            'telegram_user_id' => $telegramUserId,
            'success' => $success,
            'error_message' => $errorMessage,
            'ip' => $context['ip'] ?? null,
            'user_agent' => $context['user_agent'] ?? null,
        ]);
    }

    /**
     * Log permission denials
     */
    public function logPermissionDenial(
        int $adminId,
        string $command,
        array $context = []
    ): void {
        $this->logSecurityEvent('permission_denied', 'warning', [
            'admin_id' => $adminId,
            'command' => $command,
            'context' => $context,
        ]);
    }

    /**
     * Log rate limiting events
     */
    public function logRateLimitHit(
        string $identifier,
        string $limitType,
        array $context = []
    ): void {
        $this->logSecurityEvent('rate_limit_hit', 'info', [
            'identifier' => $identifier,
            'limit_type' => $limitType,
            'context' => $context,
        ]);
    }

    /**
     * Get audit statistics
     */
    public function getAuditStats(int $days = 30): array
    {
        $fromDate = now()->subDays($days);

        return [
            'total_actions' => TelegramAuditLog::where('created_at', '>=', $fromDate)->count(),
            'successful_actions' => TelegramAuditLog::successful()
                ->where('created_at', '>=', $fromDate)->count(),
            'failed_actions' => TelegramAuditLog::failed()
                ->where('created_at', '>=', $fromDate)->count(),
            'critical_actions' => TelegramAuditLog::critical()
                ->where('created_at', '>=', $fromDate)->count(),
            'unique_admins' => TelegramAuditLog::where('created_at', '>=', $fromDate)
                ->distinct('admin_id')->count(),
            'security_events' => TelegramSecurityEvent::where('created_at', '>=', $fromDate)->count(),
            'critical_security_events' => TelegramSecurityEvent::where('severity', 'critical')
                ->where('created_at', '>=', $fromDate)->count(),
        ];
    }

    /**
     * Get action summary by type
     */
    public function getActionSummary(int $days = 7): array
    {
        return TelegramAuditLog::select('action', DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->get()
            ->pluck('count', 'action')
            ->toArray();
    }

    /**
     * Get recent critical actions
     */
    public function getRecentCriticalActions(int $limit = 10): array
    {
        return TelegramAuditLog::with('admin')
            ->critical()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($log) {
                return [
                    'action' => $log->action_display,
                    'admin' => $log->admin?->display_name ?? 'نامشخص',
                    'resource' => $log->resource_type_display,
                    'success' => $log->success,
                    'time' => $log->created_at->diffForHumans(),
                    'changes' => $log->changes_summary,
                ];
            })
            ->toArray();
    }

    /**
     * Check if action is considered critical
     */
    private function isCriticalAction(string $action): bool
    {
        $criticalActions = [
            'user_delete',
            'admin_create', 
            'admin_role_change',
            'token_create',
            'token_revoke',
            'config_update',
            'wallet_balance_adjust',
            'system_maintenance',
            'broadcast_send'
        ];

        return in_array($action, $criticalActions);
    }

    /**
     * Hash IP address for privacy compliance
     */
    private function hashIp(?string $ip): ?string
    {
        return $ip ? hash('sha256', $ip . config('app.key')) : null;
    }

    /**
     * Hash user agent for privacy compliance
     */
    private function hashUserAgent(?string $userAgent): ?string
    {
        return $userAgent ? hash('sha256', $userAgent . config('app.key')) : null;
    }

    /**
     * Cleanup old audit logs (for compliance)
     */
    public function cleanupOldLogs(int $retentionDays = 90): int
    {
        $cutoffDate = now()->subDays($retentionDays);
        
        return TelegramAuditLog::where('created_at', '<', $cutoffDate)->delete();
    }

    /**
     * Export audit logs for compliance
     */
    public function exportLogs(
        \DateTime $startDate,
        \DateTime $endDate,
        ?string $adminId = null,
        ?string $action = null
    ): array {
        $query = TelegramAuditLog::with('admin')
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($adminId) {
            $query->where('admin_id', $adminId);
        }

        if ($action) {
            $query->where('action', $action);
        }

        return $query->orderBy('created_at')
            ->get()
            ->map(function ($log) {
                return [
                    'timestamp' => $log->created_at->toISOString(),
                    'admin' => $log->admin?->display_name ?? 'نامشخص',
                    'action' => $log->action_display,
                    'resource_type' => $log->resource_type_display,
                    'resource_id' => $log->resource_id,
                    'success' => $log->success ? 'موفق' : 'ناموفق',
                    'changes' => $log->changes_summary,
                    'error' => $log->error_message,
                ];
            })
            ->toArray();
    }
}