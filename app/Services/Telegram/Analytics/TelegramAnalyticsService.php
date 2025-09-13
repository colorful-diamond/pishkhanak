<?php

namespace App\Services\Telegram\Analytics;

use App\Services\Telegram\Core\UpdateContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Telegram Analytics Service
 * 
 * Comprehensive analytics and metrics collection for Telegram bot interactions
 * with Persian language support and financial services compliance.
 */
class TelegramAnalyticsService
{
    private const CACHE_TTL = 3600; // 1 hour
    private const BATCH_SIZE = 100;

    public function __construct(
        private TelegramMetricsCalculator $metricsCalculator,
        private TelegramUserSegmentation $userSegmentation
    ) {}

    /**
     * Track user interaction event
     */
    public function trackUserEvent(UpdateContext $context, array $eventData = []): void
    {
        try {
            $startTime = microtime(true);
            
            $eventRecord = [
                'telegram_user_id' => $context->getUserId(),
                'event_type' => $context->getType(),
                'event_action' => $this->getEventAction($context),
                'event_data' => json_encode(array_merge($eventData, [
                    'chat_type' => $context->getChatType(),
                    'has_persian_text' => $context->hasPersianText(),
                    'message_length' => $context->hasText() ? mb_strlen($context->getText()) : 0,
                ])),
                'session_id' => $this->getOrCreateSessionId($context->getUserId()),
                'message_id' => $context->getMessageId(),
                'success' => true,
                'language_code' => $context->getLanguageCode() ?? 'fa',
                'created_at' => now(),
            ];

            // Add processing time if available
            if (isset($eventData['processing_time_ms'])) {
                $eventRecord['processing_time_ms'] = $eventData['processing_time_ms'];
            }

            // Add error information if applicable
            if (isset($eventData['error'])) {
                $eventRecord['success'] = false;
                $eventRecord['error_code'] = $eventData['error_code'] ?? 'unknown';
            }

            // Batch insert for performance
            $this->queueEventForBatchInsert('telegram_user_events', $eventRecord);
            
            // Update session activity
            $this->updateSessionActivity($eventRecord['session_id'], $context);
            
            // Update real-time metrics
            $this->updateRealTimeMetrics($eventRecord);
            
        } catch (\Exception $e) {
            Log::error('Failed to track user event', [
                'user_id' => $context->getUserId(),
                'event_type' => $context->getType(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Track service usage
     */
    public function trackServiceUsage(
        string $userId, 
        int $serviceId, 
        string $actionType, 
        array $data = []
    ): void {
        try {
            $record = [
                'telegram_user_id' => $userId,
                'service_id' => $serviceId,
                'action_type' => $actionType,
                'step_number' => $data['step_number'] ?? null,
                'step_name' => $data['step_name'] ?? null,
                'time_spent_seconds' => $data['time_spent_seconds'] ?? null,
                'input_data' => isset($data['input_data']) ? json_encode($data['input_data']) : null,
                'output_data' => isset($data['output_data']) ? json_encode($data['output_data']) : null,
                'success' => $data['success'] ?? true,
                'error_reason' => $data['error_reason'] ?? null,
                'amount' => $data['amount'] ?? null,
                'payment_method' => $data['payment_method'] ?? null,
                'created_at' => now(),
            ];

            $this->queueEventForBatchInsert('telegram_service_analytics', $record);
            
            // Update service conversion funnel metrics
            $this->updateServiceFunnelMetrics($serviceId, $actionType, $data['success'] ?? true);
            
        } catch (\Exception $e) {
            Log::error('Failed to track service usage', [
                'user_id' => $userId,
                'service_id' => $serviceId,
                'action_type' => $actionType,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Track payment flow event
     */
    public function trackPaymentEvent(
        string $userId,
        string $paymentGateway,
        string $flowStep,
        array $data = []
    ): void {
        try {
            $record = [
                'telegram_user_id' => $userId,
                'transaction_id' => $data['transaction_id'] ?? null,
                'service_id' => $data['service_id'] ?? null,
                'payment_gateway' => $paymentGateway,
                'flow_step' => $flowStep,
                'amount' => $data['amount'] ?? 0,
                'currency' => $data['currency'] ?? 'IRR',
                'step_duration_seconds' => $data['step_duration_seconds'] ?? null,
                'error_code' => $data['error_code'] ?? null,
                'error_message' => $data['error_message'] ?? null,
                'gateway_response' => isset($data['gateway_response']) ? 
                    json_encode($data['gateway_response']) : null,
                'user_agent_hash' => isset($data['user_agent']) ? 
                    hash('sha256', $data['user_agent']) : null,
                'created_at' => now(),
            ];

            $this->queueEventForBatchInsert('telegram_payment_analytics', $record);
            
            // Update payment conversion metrics
            $this->updatePaymentConversionMetrics($paymentGateway, $flowStep, $data);
            
        } catch (\Exception $e) {
            Log::error('Failed to track payment event', [
                'user_id' => $userId,
                'payment_gateway' => $paymentGateway,
                'flow_step' => $flowStep,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Track command performance
     */
    public function trackCommandPerformance(
        string $commandName,
        int $processingTimeMs,
        bool $success,
        string $errorCode = null
    ): void {
        try {
            $today = now()->format('Y-m-d');
            $cacheKey = "command_metrics:{$commandName}:{$today}";
            
            $metrics = Cache::get($cacheKey, [
                'execution_count' => 0,
                'success_count' => 0,
                'error_count' => 0,
                'total_processing_time' => 0,
                'error_breakdown' => [],
            ]);
            
            $metrics['execution_count']++;
            $metrics['total_processing_time'] += $processingTimeMs;
            
            if ($success) {
                $metrics['success_count']++;
            } else {
                $metrics['error_count']++;
                $errorCode = $errorCode ?? 'unknown';
                $metrics['error_breakdown'][$errorCode] = 
                    ($metrics['error_breakdown'][$errorCode] ?? 0) + 1;
            }
            
            Cache::put($cacheKey, $metrics, self::CACHE_TTL);
            
            // Periodically flush to database
            if ($metrics['execution_count'] % 50 === 0) {
                $this->flushCommandMetrics($commandName, $today, $metrics);
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to track command performance', [
                'command_name' => $commandName,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get user analytics dashboard data
     */
    public function getUserDashboardData(array $filters = []): array
    {
        try {
            $timeRange = $this->getTimeRange($filters);
            
            return [
                'overview' => $this->getOverviewMetrics($timeRange),
                'user_growth' => $this->getUserGrowthMetrics($timeRange),
                'engagement' => $this->getEngagementMetrics($timeRange),
                'service_usage' => $this->getServiceUsageMetrics($timeRange),
                'command_popularity' => $this->getCommandPopularityMetrics($timeRange),
                'conversion_funnel' => $this->getConversionFunnelMetrics($timeRange),
                'geographic_distribution' => $this->getGeographicMetrics($timeRange),
                'performance_metrics' => $this->getPerformanceMetrics($timeRange),
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to get user dashboard data', [
                'filters' => $filters,
                'error' => $e->getMessage(),
            ]);
            
            return [];
        }
    }

    /**
     * Get service analytics dashboard data
     */
    public function getServiceDashboardData(int $serviceId = null, array $filters = []): array
    {
        try {
            $timeRange = $this->getTimeRange($filters);
            
            $query = DB::table('telegram_service_analytics');
            
            if ($serviceId) {
                $query->where('service_id', $serviceId);
            }
            
            $query->whereBetween('created_at', [$timeRange['start'], $timeRange['end']]);
            
            return [
                'service_performance' => $this->getServicePerformanceMetrics($query, $timeRange),
                'conversion_rates' => $this->getServiceConversionMetrics($query, $timeRange),
                'user_journey' => $this->getServiceUserJourneyMetrics($query, $timeRange),
                'revenue_analytics' => $this->getServiceRevenueMetrics($query, $timeRange),
                'abandonment_analysis' => $this->getServiceAbandonmentMetrics($query, $timeRange),
                'error_analysis' => $this->getServiceErrorMetrics($query, $timeRange),
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to get service dashboard data', [
                'service_id' => $serviceId,
                'filters' => $filters,
                'error' => $e->getMessage(),
            ]);
            
            return [];
        }
    }

    /**
     * Get payment analytics dashboard data
     */
    public function getPaymentDashboardData(array $filters = []): array
    {
        try {
            $timeRange = $this->getTimeRange($filters);
            
            return [
                'payment_overview' => $this->getPaymentOverviewMetrics($timeRange),
                'gateway_performance' => $this->getGatewayPerformanceMetrics($timeRange),
                'conversion_funnel' => $this->getPaymentConversionFunnel($timeRange),
                'fraud_detection' => $this->getFraudDetectionMetrics($timeRange),
                'transaction_patterns' => $this->getTransactionPatternMetrics($timeRange),
                'error_analysis' => $this->getPaymentErrorMetrics($timeRange),
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to get payment dashboard data', [
                'filters' => $filters,
                'error' => $e->getMessage(),
            ]);
            
            return [];
        }
    }

    /**
     * Calculate user engagement score
     */
    public function calculateUserEngagementScore(string $userId): float
    {
        try {
            $userBehavior = DB::table('telegram_user_behavior')
                ->where('telegram_user_id', $userId)
                ->first();
                
            if (!$userBehavior) {
                return 0.0;
            }
            
            return $this->userSegmentation->calculateEngagementScore([
                'total_sessions' => $userBehavior->total_sessions,
                'total_commands' => $userBehavior->total_commands,
                'avg_session_duration' => $userBehavior->avg_session_duration,
                'days_active' => $userBehavior->days_active,
                'successful_transactions' => $userBehavior->successful_transactions,
                'total_spent' => $userBehavior->total_spent,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to calculate user engagement score', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            
            return 0.0;
        }
    }

    /**
     * Update user behavior analytics (runs periodically)
     */
    public function updateUserBehaviorAnalytics(string $userId = null): void
    {
        try {
            $query = DB::table('telegram_user_events as e')
                ->select([
                    'e.telegram_user_id',
                    DB::raw('COUNT(DISTINCT e.session_id) as total_sessions'),
                    DB::raw('COUNT(*) as total_commands'),
                    DB::raw('MIN(e.created_at) as first_interaction_at'),
                    DB::raw('MAX(e.created_at) as last_interaction_at'),
                    DB::raw('COUNT(DISTINCT DATE(e.created_at)) as days_active'),
                ])
                ->groupBy('e.telegram_user_id');
                
            if ($userId) {
                $query->where('e.telegram_user_id', $userId);
            }
            
            $userStats = $query->get();
            
            foreach ($userStats as $stats) {
                $this->updateIndividualUserBehavior($stats);
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to update user behavior analytics', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Private helper methods
     */
    private function getEventAction(UpdateContext $context): string
    {
        if ($context->isCommand()) {
            return $context->getCommand();
        }
        
        if ($context->getType() === 'callback_query') {
            return 'callback_' . ($context->getCallbackData() ?? 'unknown');
        }
        
        return $context->getType();
    }

    private function getOrCreateSessionId(string $userId): string
    {
        $cacheKey = "active_session:{$userId}";
        $sessionId = Cache::get($cacheKey);
        
        if (!$sessionId) {
            $sessionId = hash('sha256', $userId . microtime(true));
            
            // Create session record
            DB::table('telegram_user_sessions')->insert([
                'session_id' => $sessionId,
                'telegram_user_id' => $userId,
                'started_at' => now(),
                'last_activity_at' => now(),
                'total_events' => 0,
                'successful_events' => 0,
            ]);
            
            Cache::put($cacheKey, $sessionId, 1800); // 30 minutes
        }
        
        return $sessionId;
    }

    private function updateSessionActivity(string $sessionId, UpdateContext $context): void
    {
        DB::table('telegram_user_sessions')
            ->where('session_id', $sessionId)
            ->update([
                'last_activity_at' => now(),
                'total_events' => DB::raw('total_events + 1'),
                'successful_events' => DB::raw('successful_events + 1'),
            ]);
    }

    private function queueEventForBatchInsert(string $table, array $record): void
    {
        $queueKey = "batch_insert:{$table}";
        $queue = Cache::get($queueKey, []);
        $queue[] = $record;
        
        if (count($queue) >= self::BATCH_SIZE) {
            DB::table($table)->insert($queue);
            Cache::forget($queueKey);
        } else {
            Cache::put($queueKey, $queue, 300); // 5 minutes
        }
    }

    private function updateRealTimeMetrics(array $eventRecord): void
    {
        $metricsKey = 'real_time_metrics:' . now()->format('Y-m-d-H');
        $metrics = Cache::get($metricsKey, [
            'total_events' => 0,
            'successful_events' => 0,
            'unique_users' => [],
        ]);
        
        $metrics['total_events']++;
        
        if ($eventRecord['success']) {
            $metrics['successful_events']++;
        }
        
        $metrics['unique_users'][$eventRecord['telegram_user_id']] = true;
        
        Cache::put($metricsKey, $metrics, 3600);
    }

    private function getTimeRange(array $filters): array
    {
        $start = isset($filters['start_date']) ? 
            Carbon::parse($filters['start_date']) : 
            now()->subDays(7);
            
        $end = isset($filters['end_date']) ? 
            Carbon::parse($filters['end_date']) : 
            now();
            
        return ['start' => $start, 'end' => $end];
    }

    // Additional private methods for metrics calculation would be implemented here
    // ... (truncated for brevity, but would include all the specific metric calculation methods)
}