<?php

namespace App\Services\Telegram\Analytics;

use App\Services\Telegram\Core\UpdateContext;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Telegram Event Tracker
 * 
 * High-performance event tracking system with async processing,
 * Persian language support, and financial services compliance.
 */
class EventTracker
{
    private const REDIS_KEY_PREFIX = 'telegram_events:';
    private const BATCH_SIZE = 100;
    private const FLUSH_INTERVAL = 60; // seconds

    public function __construct(
        private TelegramAnalyticsService $analyticsService
    ) {}

    /**
     * Track user interaction event (async)
     */
    public function trackEvent(
        UpdateContext $context,
        string $eventType,
        array $eventData = [],
        bool $async = true
    ): void {
        try {
            $event = [
                'user_id' => $context->getUserId(),
                'chat_id' => $context->getChatId(),
                'event_type' => $eventType,
                'event_data' => $eventData,
                'context' => [
                    'update_type' => $context->getType(),
                    'command' => $context->getCommand(),
                    'message_id' => $context->getMessageId(),
                    'language_code' => $context->getLanguageCode() ?? 'fa',
                    'chat_type' => $context->getChatType(),
                    'has_persian_text' => $context->hasPersianText(),
                ],
                'timestamp' => microtime(true),
                'session_id' => $this->getSessionId($context->getUserId()),
            ];

            if ($async) {
                $this->queueEvent($event);
            } else {
                $this->processEventSync($event, $context);
            }

        } catch (\Exception $e) {
            Log::error('Failed to track event', [
                'user_id' => $context->getUserId(),
                'event_type' => $eventType,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Track command execution
     */
    public function trackCommand(
        UpdateContext $context,
        string $commandName,
        int $processingTimeMs,
        bool $success,
        array $metadata = []
    ): void {
        $this->trackEvent($context, 'command_execution', [
            'command_name' => $commandName,
            'processing_time_ms' => $processingTimeMs,
            'success' => $success,
            'metadata' => $metadata,
        ]);

        // Also update command metrics
        $this->analyticsService->trackCommandPerformance(
            $commandName,
            $processingTimeMs,
            $success,
            $metadata['error_code'] ?? null
        );
    }

    /**
     * Track service interaction
     */
    public function trackServiceInteraction(
        string $userId,
        int $serviceId,
        string $action,
        array $data = []
    ): void {
        try {
            $this->analyticsService->trackServiceUsage($userId, $serviceId, $action, $data);
            
            // Also queue for real-time analytics
            $this->queueServiceEvent([
                'user_id' => $userId,
                'service_id' => $serviceId,
                'action' => $action,
                'data' => $data,
                'timestamp' => microtime(true),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to track service interaction', [
                'user_id' => $userId,
                'service_id' => $serviceId,
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Track payment flow
     */
    public function trackPayment(
        string $userId,
        string $gateway,
        string $step,
        array $paymentData = []
    ): void {
        try {
            $this->analyticsService->trackPaymentEvent($userId, $gateway, $step, $paymentData);
            
            // Real-time fraud detection
            if (in_array($step, ['initiated', 'processing'])) {
                Queue::push(new \App\Jobs\AnalyzePaymentFraud([
                    'user_id' => $userId,
                    'gateway' => $gateway,
                    'payment_data' => $paymentData,
                ]));
            }

        } catch (\Exception $e) {
            Log::error('Failed to track payment', [
                'user_id' => $userId,
                'gateway' => $gateway,
                'step' => $step,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Track user behavior for segmentation
     */
    public function trackUserBehavior(
        string $userId,
        array $behaviorData
    ): void {
        try {
            $redisKey = self::REDIS_KEY_PREFIX . "user_behavior:{$userId}";
            
            $existingData = Redis::hgetall($redisKey);
            $updatedData = array_merge($existingData, $behaviorData);
            
            Redis::hmset($redisKey, $updatedData);
            Redis::expire($redisKey, 86400); // 24 hours
            
            // Periodic batch update to database
            if (mt_rand(1, 100) <= 5) { // 5% chance
                Queue::push(new \App\Jobs\UpdateUserBehaviorAnalytics($userId));
            }

        } catch (\Exception $e) {
            Log::error('Failed to track user behavior', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Track A/B test events
     */
    public function trackABTest(
        string $userId,
        int $testId,
        string $variant,
        string $eventType,
        array $eventData = []
    ): void {
        try {
            $event = [
                'user_id' => $userId,
                'test_id' => $testId,
                'variant' => $variant,
                'event_type' => $eventType,
                'event_data' => $eventData,
                'timestamp' => microtime(true),
            ];

            Queue::push(new \App\Jobs\ProcessABTestEvent($event));

        } catch (\Exception $e) {
            Log::error('Failed to track A/B test event', [
                'user_id' => $userId,
                'test_id' => $testId,
                'variant' => $variant,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Track bot performance metrics
     */
    public function trackBotPerformance(array $metrics): void
    {
        try {
            $redisKey = self::REDIS_KEY_PREFIX . 'bot_performance:' . date('Y-m-d-H');
            
            foreach ($metrics as $metric => $value) {
                Redis::hincrby($redisKey, $metric, $value);
            }
            
            Redis::expire($redisKey, 86400);

        } catch (\Exception $e) {
            Log::error('Failed to track bot performance', [
                'metrics' => $metrics,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get real-time analytics data
     */
    public function getRealTimeData(array $metrics = []): array
    {
        try {
            $data = [];
            $now = now();
            
            $defaultMetrics = [
                'active_users_1h',
                'commands_per_minute',
                'success_rate',
                'avg_response_time',
                'payment_conversions_1h',
            ];
            
            $metricsToFetch = empty($metrics) ? $defaultMetrics : $metrics;
            
            foreach ($metricsToFetch as $metric) {
                $data[$metric] = $this->getRealTimeMetric($metric, $now);
            }
            
            return [
                'timestamp' => $now->toISOString(),
                'metrics' => $data,
                'status' => 'active',
            ];

        } catch (\Exception $e) {
            Log::error('Failed to get real-time data', [
                'requested_metrics' => $metrics,
                'error' => $e->getMessage(),
            ]);
            
            return ['error' => 'Failed to fetch real-time data'];
        }
    }

    /**
     * Flush pending events to database
     */
    public function flushPendingEvents(): int
    {
        try {
            $flushedCount = 0;
            
            // Flush user events
            $flushedCount += $this->flushEventsFromRedis('user_events');
            
            // Flush service events
            $flushedCount += $this->flushEventsFromRedis('service_events');
            
            // Flush command metrics
            $flushedCount += $this->flushCommandMetrics();
            
            Log::info('Flushed pending events to database', [
                'flushed_count' => $flushedCount,
            ]);
            
            return $flushedCount;

        } catch (\Exception $e) {
            Log::error('Failed to flush pending events', [
                'error' => $e->getMessage(),
            ]);
            
            return 0;
        }
    }

    /**
     * Private helper methods
     */
    private function queueEvent(array $event): void
    {
        $redisKey = self::REDIS_KEY_PREFIX . 'queue:user_events';
        Redis::lpush($redisKey, json_encode($event));
        
        // Set TTL to prevent infinite queue growth
        Redis::expire($redisKey, 3600);
        
        // Check if we should flush
        $queueLength = Redis::llen($redisKey);
        if ($queueLength >= self::BATCH_SIZE) {
            Queue::push(new \App\Jobs\FlushAnalyticsEvents());
        }
    }

    private function processEventSync(array $event, UpdateContext $context): void
    {
        $this->analyticsService->trackUserEvent($context, $event['event_data']);
    }

    private function queueServiceEvent(array $event): void
    {
        $redisKey = self::REDIS_KEY_PREFIX . 'queue:service_events';
        Redis::lpush($redisKey, json_encode($event));
        Redis::expire($redisKey, 3600);
    }

    private function getSessionId(string $userId): string
    {
        $sessionKey = "session_id:{$userId}";
        $sessionId = Redis::get($sessionKey);
        
        if (!$sessionId) {
            $sessionId = hash('sha256', $userId . microtime(true));
            Redis::setex($sessionKey, 1800, $sessionId); // 30 minutes
        }
        
        return $sessionId;
    }

    private function getRealTimeMetric(string $metric, \Carbon\Carbon $timestamp): mixed
    {
        $redisKey = self::REDIS_KEY_PREFIX . "realtime:{$metric}";
        
        return match ($metric) {
            'active_users_1h' => $this->getActiveUsers($timestamp->subHour()),
            'commands_per_minute' => $this->getCommandsPerMinute($timestamp),
            'success_rate' => $this->getSuccessRate($timestamp),
            'avg_response_time' => $this->getAverageResponseTime($timestamp),
            'payment_conversions_1h' => $this->getPaymentConversions($timestamp->subHour()),
            default => Redis::get($redisKey) ?? 0,
        };
    }

    private function getActiveUsers(\Carbon\Carbon $since): int
    {
        $redisKey = self::REDIS_KEY_PREFIX . 'active_users:' . $since->format('Y-m-d-H');
        return Redis::scard($redisKey) ?? 0;
    }

    private function getCommandsPerMinute(\Carbon\Carbon $timestamp): int
    {
        $redisKey = self::REDIS_KEY_PREFIX . 'commands:' . $timestamp->format('Y-m-d-H-i');
        return Redis::get($redisKey) ?? 0;
    }

    private function getSuccessRate(\Carbon\Carbon $timestamp): float
    {
        $hour = $timestamp->format('Y-m-d-H');
        $totalKey = self::REDIS_KEY_PREFIX . "total_commands:{$hour}";
        $successKey = self::REDIS_KEY_PREFIX . "successful_commands:{$hour}";
        
        $total = Redis::get($totalKey) ?? 0;
        $successful = Redis::get($successKey) ?? 0;
        
        return $total > 0 ? ($successful / $total) * 100 : 0;
    }

    private function flushEventsFromRedis(string $eventType): int
    {
        $redisKey = self::REDIS_KEY_PREFIX . "queue:{$eventType}";
        $events = Redis::lrange($redisKey, 0, self::BATCH_SIZE - 1);
        
        if (empty($events)) {
            return 0;
        }
        
        $decodedEvents = array_map('json_decode', $events);
        
        // Insert to database
        Queue::push(new \App\Jobs\ProcessAnalyticsEvents($eventType, $decodedEvents));
        
        // Remove processed events from queue
        Redis::ltrim($redisKey, count($events), -1);
        
        return count($events);
    }
}