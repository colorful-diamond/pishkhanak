<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ConversationManager
{
    protected const REDIS_PREFIX = 'ai_chat:';
    protected const CONVERSATION_PREFIX = 'conversation:';
    protected const FIELD_DATA_PREFIX = 'field_data:';
    protected const CONTEXT_PREFIX = 'context:';
    protected const DEFAULT_TTL = 7200; // 2 hours
    protected const LONG_TTL = 86400; // 24 hours
    
    protected string $sessionId;
    protected ?int $userId;
    protected string $ipAddress;

    public function __construct(string $sessionId = null, int $userId = null, string $ipAddress = null)
    {
        // Only generate new session ID if none provided AND we're not in a web request with existing session
        $this->sessionId = $sessionId ?: $this->generateSessionId();
        $this->userId = $userId ?: Auth::id();
        $this->ipAddress = $ipAddress ?: request()->ip();
        
        // Log session initialization for debugging
        Log::info('ConversationManager initialized', [
            'session_id' => $this->sessionId,
            'user_id' => $this->userId,
            'ip_address' => $this->ipAddress,
            'provided_session_id' => $sessionId,
            'generated_new' => $sessionId ? false : true
        ]);
    }

    /**
     * Generate unique session ID
     */
    protected function generateSessionId(): string
    {
        return 'sess_' . Str::random(32) . '_' . time();
    }

    /**
     * Get Redis key for conversation
     */
    protected function getConversationKey(): string
    {
        return self::REDIS_PREFIX . self::CONVERSATION_PREFIX . $this->sessionId;
    }

    /**
     * Get Redis key for field data
     */
    protected function getFieldDataKey(): string
    {
        return self::REDIS_PREFIX . self::FIELD_DATA_PREFIX . $this->sessionId;
    }

    /**
     * Get Redis key for conversation context
     */
    protected function getContextKey(): string
    {
        return self::REDIS_PREFIX . self::CONTEXT_PREFIX . $this->sessionId;
    }

    /**
     * Store conversation message
     */
    public function storeMessage(string $message, bool $isUser = true, array $metadata = []): void
    {
        try {
            // Ensure message is a string
            if (!is_string($message)) {
                if (is_array($message)) {
                    $message = json_encode($message);
                } else {
                    $message = (string) $message;
                }
            }
            
            $messageData = [
                'id' => Str::uuid(),
                'message' => $message,
                'is_user' => $isUser,
                'timestamp' => Carbon::now()->toISOString(),
                'user_id' => $this->userId,
                'ip_address' => $this->ipAddress,
                'metadata' => $metadata
            ];

            $key = $this->getConversationKey();
            Redis::lpush($key, json_encode($messageData));
            Redis::expire($key, self::DEFAULT_TTL);

            // Keep only last 100 messages
            Redis::ltrim($key, 0, 99);

        } catch (\Exception $e) {
            Log::error('خطا در ذخیره پیام گفتگو', [
                'session_id' => $this->sessionId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get conversation history
     */
    public function getConversationHistory(int $limit = 50): array
    {
        try {
            $key = $this->getConversationKey();
            $messages = Redis::lrange($key, 0, $limit - 1);

            return array_map(function($messageJson) {
                $message = json_decode($messageJson, true);
                return [
                    'id' => $message['id'],
                    'message' => $message['message'],
                    'is_user' => $message['is_user'],
                    'timestamp' => $message['timestamp'],
                    'metadata' => $message['metadata'] ?? []
                ];
            }, array_reverse($messages));

        } catch (\Exception $e) {
            Log::error('خطا در بازیابی تاریخچه گفتگو', [
                'session_id' => $this->sessionId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get conversation history formatted for AI API calls
     */
    public function getConversationHistoryForAPI(int $limit = 20): array
    {
        try {
            $history = $this->getConversationHistory($limit);
            $apiMessages = [];
            
            foreach ($history as $message) {
                $role = $message['is_user'] ? 'user' : 'model';
                $apiMessages[] = [
                    'role' => $role,
                    'content' => $message['message']
                ];
            }
            
            return $apiMessages;
            
        } catch (\Exception $e) {
            Log::error('خطا در تبدیل تاریخچه گفتگو برای API', [
                'session_id' => $this->sessionId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Store field data during conversation
     */
    public function storeFieldData(string $fieldName, $value, array $metadata = []): void
    {
        try {
            // Ensure value is serializable
            if (is_array($value) || is_object($value)) {
                $value = json_encode($value);
            } elseif (!is_scalar($value) && !is_null($value)) {
                $value = (string) $value;
            }
            
            $fieldData = [
                'value' => $value,
                'timestamp' => Carbon::now()->toISOString(),
                'validated' => $metadata['validated'] ?? false,
                'validation_errors' => $metadata['validation_errors'] ?? [],
                'field_type' => $metadata['field_type'] ?? 'text',
                'metadata' => $metadata
            ];

            $key = $this->getFieldDataKey();
            Redis::hset($key, $fieldName, json_encode($fieldData));
            Redis::expire($key, self::LONG_TTL);

        } catch (\Exception $e) {
            Log::error('خطا در ذخیره داده فیلد', [
                'session_id' => $this->sessionId,
                'field_name' => $fieldName,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get field data
     */
    public function getFieldData(string $fieldName = null): array
    {
        try {
            $key = $this->getFieldDataKey();
            
            if ($fieldName) {
                $data = Redis::hget($key, $fieldName);
                return $data ? json_decode($data, true) : [];
            }

            $allData = Redis::hgetall($key);
            $result = [];
            
            foreach ($allData as $field => $data) {
                $result[$field] = json_decode($data, true);
            }
            
            return $result;

        } catch (\Exception $e) {
            Log::error('خطا در بازیابی داده فیلد', [
                'session_id' => $this->sessionId,
                'field_name' => $fieldName,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Remove field data
     */
    public function removeFieldData(string $fieldName): void
    {
        try {
            $key = $this->getFieldDataKey();
            Redis::hdel($key, $fieldName);
        } catch (\Exception $e) {
            Log::error('خطا در حذف داده فیلد', [
                'session_id' => $this->sessionId,
                'field_name' => $fieldName,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Store conversation context
     */
    public function storeContext(array $context): void
    {
        try {
            $contextData = [
                'context' => $context,
                'timestamp' => Carbon::now()->toISOString(),
                'user_id' => $this->userId,
                'ip_address' => $this->ipAddress
            ];

            $key = $this->getContextKey();
            Redis::set($key, json_encode($contextData));
            Redis::expire($key, self::DEFAULT_TTL);

        } catch (\Exception $e) {
            Log::error('خطا در ذخیره context گفتگو', [
                'session_id' => $this->sessionId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get conversation context
     */
    public function getContext(): array
    {
        try {
            $key = $this->getContextKey();
            $data = Redis::get($key);
            
            if (!$data) {
                return [];
            }

            $contextData = json_decode($data, true);
            return $contextData['context'] ?? [];

        } catch (\Exception $e) {
            Log::error('خطا در بازیابی context گفتگو', [
                'session_id' => $this->sessionId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Update conversation context
     */
    public function updateContext(array $updates): void
    {
        $currentContext = $this->getContext();
        $newContext = array_merge($currentContext, $updates);
        $this->storeContext($newContext);
    }

    /**
     * Set service context
     */
    public function setServiceContext(string $serviceSlug, array $serviceData = []): void
    {
        $context = [
            'current_service' => $serviceSlug,
            'service_data' => $serviceData,
            'step' => 'field_collection',
            'updated_at' => Carbon::now()->toISOString()
        ];

        $this->updateContext($context);
    }

    /**
     * Get current service from context
     */
    public function getCurrentService(): ?string
    {
        $context = $this->getContext();
        return $context['current_service'] ?? null;
    }

    /**
     * Get service data from context
     */
    public function getServiceData(): array
    {
        $context = $this->getContext();
        return $context['service_data'] ?? [];
    }

    /**
     * Set conversation step
     */
    public function setStep(string $step): void
    {
        $this->updateContext(['step' => $step]);
    }

    /**
     * Get current conversation step
     */
    public function getCurrentStep(): string
    {
        $context = $this->getContext();
        return $context['step'] ?? 'initial';
    }

    /**
     * Check if field is completed
     */
    public function isFieldCompleted(string $fieldName): bool
    {
        $fieldData = $this->getFieldData($fieldName);
        return !empty($fieldData) && ($fieldData['validated'] ?? false);
    }

    /**
     * Get completed fields
     */
    public function getCompletedFields(): array
    {
        $allFields = $this->getFieldData();
        $completed = [];

        foreach ($allFields as $fieldName => $fieldData) {
            if ($fieldData['validated'] ?? false) {
                $completed[$fieldName] = $fieldData['value'];
            }
        }

        return $completed;
    }

    /**
     * Get missing fields for a service
     */
    public function getMissingFields(array $requiredFields): array
    {
        $completedFields = $this->getCompletedFields();
        $missing = [];

        foreach ($requiredFields as $fieldName => $fieldInfo) {
            if (!isset($completedFields[$fieldName])) {
                $missing[] = $fieldName;
            }
        }

        return $missing;
    }

    /**
     * Check if all required fields are completed
     */
    public function areAllFieldsCompleted(array $requiredFields): bool
    {
        $missing = $this->getMissingFields($requiredFields);
        return empty($missing);
    }

    /**
     * Generate service URL with parameters
     */
    public function generateServiceUrl(string $serviceSlug): string
    {
        $completedFields = $this->getCompletedFields();
        $baseUrl = url("/services/{$serviceSlug}");

        if (empty($completedFields)) {
            return $baseUrl;
        }

        $params = [];
        foreach ($completedFields as $fieldName => $value) {
            $params[$fieldName] = $value;
        }

        return $baseUrl . '?' . http_build_query($params);
    }

    /**
     * Clear conversation data
     */
    public function clearConversation(): void
    {
        try {
            Redis::del($this->getConversationKey());
            Redis::del($this->getFieldDataKey());
            Redis::del($this->getContextKey());
        } catch (\Exception $e) {
            Log::error('خطا در پاک کردن گفتگو', [
                'session_id' => $this->sessionId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Extend conversation TTL
     */
    public function extendTTL(int $seconds = null): void
    {
        $ttl = $seconds ?? self::DEFAULT_TTL;
        
        try {
            Redis::expire($this->getConversationKey(), $ttl);
            Redis::expire($this->getFieldDataKey(), $ttl);
            Redis::expire($this->getContextKey(), $ttl);
        } catch (\Exception $e) {
            Log::error('خطا در تمدید TTL گفتگو', [
                'session_id' => $this->sessionId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get conversation statistics
     */
    public function getConversationStats(): array
    {
        try {
            return [
                'session_id' => $this->sessionId,
                'message_count' => Redis::llen($this->getConversationKey()),
                'field_count' => Redis::hlen($this->getFieldDataKey()),
                'has_context' => Redis::exists($this->getContextKey()),
                'current_service' => $this->getCurrentService(),
                'current_step' => $this->getCurrentStep(),
                'completed_fields' => count($this->getCompletedFields()),
                'ttl' => Redis::ttl($this->getConversationKey())
            ];
        } catch (\Exception $e) {
            Log::error('خطا در دریافت آمار گفتگو', [
                'session_id' => $this->sessionId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get session ID
     */
    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    /**
     * Set user ID
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * Get user ID
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * Static method to create manager from session
     */
    public static function fromSession(string $sessionId): self
    {
        return new self($sessionId);
    }

    /**
     * Static method to create manager for user
     */
    public static function forUser(int $userId): self
    {
        return new self(null, $userId);
    }
} 