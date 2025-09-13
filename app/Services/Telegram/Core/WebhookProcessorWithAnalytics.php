<?php

namespace App\Services\Telegram\Core;

use App\Services\PersianTextValidator;
use App\Services\TelegramAdminAuth;
use App\Services\Telegram\Analytics\EventTracker;
use App\Services\Telegram\Analytics\TelegramAnalyticsService;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

/**
 * Enhanced Telegram Webhook Processor with Analytics Integration
 * 
 * Extends the base WebhookProcessor with comprehensive analytics tracking,
 * performance monitoring, and business intelligence data collection.
 */
class WebhookProcessorWithAnalytics extends WebhookProcessor
{
    public function __construct(
        private MessageRouter $router,
        private PersianTextValidator $textValidator,
        private TelegramAdminAuth $adminAuth,
        private EventTracker $eventTracker,
        private TelegramAnalyticsService $analyticsService,
        private ?LoggerInterface $logger = null
    ) {
        parent::__construct($router, $textValidator, $adminAuth, $logger);
    }

    /**
     * Enhanced process method with analytics integration
     */
    public function process(array $update): ProcessingResult
    {
        $startTime = microtime(true);
        $updateId = $update['update_id'] ?? null;

        try {
            // Step 1: Basic structure validation
            $validationResult = $this->validateUpdateStructure($update);
            if (!$validationResult->isSuccess()) {
                $this->trackEventWithResult($update, 'validation_failed', $validationResult, $startTime);
                return $validationResult;
            }

            // Step 2: Extract update context
            $context = $this->extractUpdateContext($update);
            
            // Step 3: Track incoming update
            $this->eventTracker->trackEvent($context, 'webhook_received', [
                'update_id' => $updateId,
                'processing_start' => $startTime,
            ]);

            // Step 4: Security validation with analytics
            $securityResult = $this->performSecurityValidationWithAnalytics($context);
            if (!$securityResult->isSuccess()) {
                $this->trackEventWithResult($context, 'security_failed', $securityResult, $startTime);
                return $securityResult;
            }

            // Step 5: Persian text validation with analytics
            if ($context->hasText()) {
                $textValidation = $this->validatePersianTextWithAnalytics($context);
                if (!$textValidation->isSuccess()) {
                    $this->trackEventWithResult($context, 'text_validation_failed', $textValidation, $startTime);
                    return $textValidation;
                }
            }

            // Step 6: Route to appropriate handler with performance tracking
            $routingStartTime = microtime(true);
            $routingResult = $this->router->route($context);
            $routingTime = round((microtime(true) - $routingStartTime) * 1000, 2);

            // Step 7: Track command execution
            if ($context->isCommand()) {
                $this->eventTracker->trackCommand(
                    $context,
                    $context->getCommand(),
                    $routingTime,
                    $routingResult->isSuccess(),
                    [
                        'error_code' => $routingResult->getErrorCode(),
                        'routing_time_ms' => $routingTime,
                    ]
                );
            }

            // Step 8: Log comprehensive processing metrics
            $this->logEnhancedProcessingMetrics($updateId, $context, $startTime, true, $routingTime);
            
            // Step 9: Track successful completion
            $this->trackEventWithResult($context, 'webhook_processed', $routingResult, $startTime);

            return $routingResult;

        } catch (\Exception $e) {
            $processingTime = round((microtime(true) - $startTime) * 1000, 2);
            
            $this->logger->error('Enhanced webhook processing error', [
                'update_id' => $updateId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime,
            ]);

            // Track error event
            if (isset($context)) {
                $this->trackEventWithResult($context, 'processing_error', 
                    ProcessingResult::error($e->getMessage(), ProcessingResult::ERROR_INTERNAL), 
                    $startTime
                );
            }

            $this->logEnhancedProcessingMetrics($updateId, $context ?? null, $startTime, false);

            return ProcessingResult::error(
                'Internal processing error',
                ProcessingResult::ERROR_INTERNAL,
                ['update_id' => $updateId]
            );
        }
    }

    /**
     * Enhanced security validation with analytics
     */
    private function performSecurityValidationWithAnalytics(UpdateContext $context): ProcessingResult
    {
        $securityStartTime = microtime(true);
        
        try {
            // Check if this is an admin command
            if ($context->isCommand() && $this->isAdminCommand($context->getText())) {
                $isAuthorized = $this->adminAuth->verifyAdmin(
                    $context->getUserId(),
                    $context->getText()
                );

                if (!$isAuthorized) {
                    // Track unauthorized admin attempt
                    $this->eventTracker->trackEvent($context, 'unauthorized_admin_attempt', [
                        'command' => $context->getText(),
                        'security_check_time_ms' => round((microtime(true) - $securityStartTime) * 1000, 2),
                    ]);

                    $this->logger->warning('Unauthorized admin command attempt', [
                        'user_id' => $context->getUserId(),
                        'command' => $context->getText(),
                        'chat_id' => $context->getChatId(),
                    ]);

                    return ProcessingResult::error(
                        'Unauthorized access to admin command',
                        ProcessingResult::ERROR_UNAUTHORIZED
                    );
                }

                // Track successful admin authentication
                $this->eventTracker->trackEvent($context, 'admin_authenticated', [
                    'command' => $context->getText(),
                    'security_check_time_ms' => round((microtime(true) - $securityStartTime) * 1000, 2),
                ]);
            }

            // Track user behavior for segmentation
            $this->trackUserBehaviorMetrics($context);

            // Additional security analytics
            $this->performSecurityAnalytics($context);

            return ProcessingResult::success();

        } catch (\Exception $e) {
            $this->logger->error('Security validation error with analytics', [
                'user_id' => $context->getUserId(),
                'error' => $e->getMessage(),
            ]);

            return ProcessingResult::error('Security validation failed', ProcessingResult::ERROR_INTERNAL);
        }
    }

    /**
     * Enhanced Persian text validation with analytics
     */
    private function validatePersianTextWithAnalytics(UpdateContext $context): ProcessingResult
    {
        $validationStartTime = microtime(true);
        
        try {
            $text = $context->getText();
            if (empty($text)) {
                return ProcessingResult::success();
            }

            // Determine validation context based on message type
            $validationContext = $this->determineValidationContext($context);

            // Track text characteristics for analytics
            $textAnalytics = [
                'text_length' => mb_strlen($text),
                'word_count' => str_word_count($text),
                'has_persian_chars' => preg_match('/[\x{0600}-\x{06FF}]/u', $text),
                'has_english_chars' => preg_match('/[a-zA-Z]/', $text),
                'has_numbers' => preg_match('/[0-9۰-۹]/', $text),
                'validation_context' => $validationContext,
            ];

            $sanitizedText = $this->textValidator->sanitizePersianInput($text, $validationContext);
            
            // Update context with sanitized text
            $context->setSanitizedText($sanitizedText);

            // Track successful text validation
            $this->eventTracker->trackEvent($context, 'text_validated', array_merge($textAnalytics, [
                'validation_time_ms' => round((microtime(true) - $validationStartTime) * 1000, 2),
                'sanitization_applied' => $sanitizedText !== $text,
            ]));

            return ProcessingResult::success();

        } catch (\InvalidArgumentException $e) {
            // Track text validation failure
            $this->eventTracker->trackEvent($context, 'text_validation_failed', [
                'error' => $e->getMessage(),
                'original_text_length' => mb_strlen($context->getText()),
                'validation_time_ms' => round((microtime(true) - $validationStartTime) * 1000, 2),
            ]);

            $this->logger->warning('Persian text validation failed with analytics', [
                'user_id' => $context->getUserId(),
                'chat_id' => $context->getChatId(),
                'error' => $e->getMessage(),
                'original_text_length' => mb_strlen($context->getText()),
            ]);

            return ProcessingResult::error(
                'متن ارسالی شامل کاراکترهای غیرمجاز است',
                ProcessingResult::ERROR_INVALID_INPUT,
                ['validation_error' => $e->getMessage()]
            );
        }
    }

    /**
     * Track user behavior metrics for segmentation
     */
    private function trackUserBehaviorMetrics(UpdateContext $context): void
    {
        try {
            $behaviorData = [
                'last_interaction' => time(),
                'total_interactions' => 1, // Will be incremented in Redis
                'interaction_type' => $context->getType(),
                'is_command_user' => $context->isCommand(),
                'preferred_language' => $context->getLanguageCode() ?? 'fa',
                'chat_type' => $context->getChatType(),
                'has_persian_content' => $context->hasPersianText(),
            ];

            $this->eventTracker->trackUserBehavior($context->getUserId(), $behaviorData);

        } catch (\Exception $e) {
            $this->logger->error('Failed to track user behavior metrics', [
                'user_id' => $context->getUserId(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Perform additional security analytics
     */
    private function performSecurityAnalytics(UpdateContext $context): void
    {
        try {
            // Track rate limiting patterns
            $this->trackRateLimitingPatterns($context);
            
            // Analyze message patterns for spam detection
            $this->analyzeMessagePatterns($context);
            
            // Geographic analysis if location data available
            if ($context->hasLocation()) {
                $this->trackGeographicPatterns($context);
            }

        } catch (\Exception $e) {
            $this->logger->error('Security analytics failed', [
                'user_id' => $context->getUserId(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Track event with processing result
     */
    private function trackEventWithResult(
        $contextOrUpdate, 
        string $eventType, 
        ProcessingResult $result, 
        float $startTime
    ): void {
        try {
            $processingTime = round((microtime(true) - $startTime) * 1000, 2);
            
            $eventData = [
                'success' => $result->isSuccess(),
                'processing_time_ms' => $processingTime,
                'error_code' => $result->getErrorCode(),
                'error_message' => $result->getError(),
                'result_data' => $result->getData(),
            ];

            if ($contextOrUpdate instanceof UpdateContext) {
                $this->eventTracker->trackEvent($contextOrUpdate, $eventType, $eventData);
            } else {
                // For cases where we don't have context yet (early validation failures)
                $updateId = $contextOrUpdate['update_id'] ?? 'unknown';
                $userId = $this->extractUserIdFromUpdate($contextOrUpdate);
                
                $this->eventTracker->trackBotPerformance([
                    'failed_updates' => 1,
                    'total_processing_time_ms' => $processingTime,
                ]);
            }

        } catch (\Exception $e) {
            $this->logger->error('Failed to track event with result', [
                'event_type' => $eventType,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Enhanced processing metrics logging
     */
    private function logEnhancedProcessingMetrics(
        ?int $updateId, 
        ?UpdateContext $context, 
        float $startTime, 
        bool $success, 
        float $routingTime = 0
    ): void {
        $processingTime = round((microtime(true) - $startTime) * 1000, 2);
        $memoryUsage = round(memory_get_usage(true) / 1024 / 1024, 2);
        $peakMemoryUsage = round(memory_get_peak_usage(true) / 1024 / 1024, 2);

        $logData = [
            'update_id' => $updateId,
            'success' => $success,
            'processing_time_ms' => $processingTime,
            'routing_time_ms' => $routingTime,
            'memory_usage_mb' => $memoryUsage,
            'peak_memory_mb' => $peakMemoryUsage,
            'timestamp' => now()->toISOString(),
        ];

        if ($context) {
            $logData = array_merge($logData, [
                'update_type' => $context->getType(),
                'user_id' => $context->getUserId(),
                'chat_id' => $context->getChatId(),
                'is_command' => $context->isCommand(),
                'command' => $context->getCommand(),
                'has_persian_text' => $context->hasPersianText(),
                'is_admin' => $context->isFromAdmin(),
                'message_length' => $context->hasText() ? mb_strlen($context->getText()) : 0,
                'chat_type' => $context->getChatType(),
            ]);
        }

        // Log with appropriate level
        if ($success) {
            $this->logger->info('Enhanced webhook processed successfully', $logData);
        } else {
            $this->logger->error('Enhanced webhook processing failed', $logData);
        }

        // Performance monitoring
        if ($processingTime > 150) { // 150ms threshold
            $this->logger->warning('Slow webhook processing detected', [
                'processing_time_ms' => $processingTime,
                'routing_time_ms' => $routingTime,
                'update_id' => $updateId,
                'update_type' => $context?->getType(),
                'memory_usage_mb' => $memoryUsage,
            ]);
        }

        // Track bot performance metrics
        $this->eventTracker->trackBotPerformance([
            'total_updates' => 1,
            $success ? 'successful_updates' : 'failed_updates' => 1,
            'total_processing_time_ms' => $processingTime,
            'total_memory_usage_mb' => $memoryUsage,
        ]);
    }

    /**
     * Additional helper methods for analytics
     */
    private function trackRateLimitingPatterns(UpdateContext $context): void
    {
        // Implementation for rate limiting pattern analysis
        // This would track user request frequency and identify potential spam
    }

    private function analyzeMessagePatterns(UpdateContext $context): void
    {
        // Implementation for message pattern analysis
        // This would identify suspicious message patterns or content
    }

    private function trackGeographicPatterns(UpdateContext $context): void
    {
        // Implementation for geographic pattern tracking
        // This would analyze location-based usage patterns
    }

    private function extractUserIdFromUpdate(array $update): ?string
    {
        // Extract user ID from various update types
        if (isset($update['message']['from']['id'])) {
            return (string) $update['message']['from']['id'];
        }
        
        if (isset($update['callback_query']['from']['id'])) {
            return (string) $update['callback_query']['from']['id'];
        }
        
        if (isset($update['inline_query']['from']['id'])) {
            return (string) $update['inline_query']['from']['id'];
        }
        
        return null;
    }
}