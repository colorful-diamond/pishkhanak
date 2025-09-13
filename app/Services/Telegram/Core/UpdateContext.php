<?php

namespace App\Services\Telegram\Core;

/**
 * Telegram Update Context Value Object
 * 
 * Encapsulates all information about a Telegram update
 * providing type-safe access to update data
 */
class UpdateContext
{
    private array $originalUpdate;
    private string $type;
    private ?array $message = null;
    private ?array $callbackQuery = null;
    private ?array $inlineQuery = null;
    private ?array $editedMessage = null;
    private ?string $sanitizedText = null;

    public function __construct(array $update)
    {
        $this->originalUpdate = $update;
        $this->extractUpdateData($update);
    }

    /**
     * Create from raw Telegram update array
     */
    public static function fromArray(array $update): self
    {
        return new self($update);
    }

    /**
     * Extract and categorize update data
     */
    private function extractUpdateData(array $update): void
    {
        if (isset($update['message'])) {
            $this->type = 'message';
            $this->message = $update['message'];
        } elseif (isset($update['callback_query'])) {
            $this->type = 'callback_query';
            $this->callbackQuery = $update['callback_query'];
            // Callback queries also contain message info
            $this->message = $update['callback_query']['message'] ?? null;
        } elseif (isset($update['inline_query'])) {
            $this->type = 'inline_query';
            $this->inlineQuery = $update['inline_query'];
        } elseif (isset($update['edited_message'])) {
            $this->type = 'edited_message';
            $this->editedMessage = $update['edited_message'];
        } elseif (isset($update['channel_post'])) {
            $this->type = 'channel_post';
            $this->message = $update['channel_post'];
        } elseif (isset($update['edited_channel_post'])) {
            $this->type = 'edited_channel_post';
            $this->message = $update['edited_channel_post'];
        } else {
            $this->type = 'unknown';
        }
    }

    /**
     * Get update type
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get original update array
     */
    public function getOriginalUpdate(): array
    {
        return $this->originalUpdate;
    }

    /**
     * Get update ID
     */
    public function getUpdateId(): ?int
    {
        return $this->originalUpdate['update_id'] ?? null;
    }

    /**
     * Get user ID from update
     */
    public function getUserId(): ?string
    {
        $from = $this->getFrom();
        return $from['id'] ?? null;
    }

    /**
     * Get user information
     */
    public function getFrom(): ?array
    {
        if ($this->type === 'callback_query') {
            return $this->callbackQuery['from'] ?? null;
        } elseif ($this->type === 'inline_query') {
            return $this->inlineQuery['from'] ?? null;
        } else {
            return $this->message['from'] ?? null;
        }
    }

    /**
     * Get chat ID
     */
    public function getChatId(): ?string
    {
        $chat = $this->getChat();
        return $chat['id'] ?? null;
    }

    /**
     * Get chat information
     */
    public function getChat(): ?array
    {
        return $this->message['chat'] ?? null;
    }

    /**
     * Get message text
     */
    public function getText(): ?string
    {
        if ($this->type === 'callback_query') {
            return $this->callbackQuery['data'] ?? null;
        } elseif ($this->type === 'inline_query') {
            return $this->inlineQuery['query'] ?? null;
        } else {
            return $this->message['text'] ?? $this->message['caption'] ?? null;
        }
    }

    /**
     * Get sanitized text (after Persian validation)
     */
    public function getSanitizedText(): ?string
    {
        return $this->sanitizedText ?? $this->getText();
    }

    /**
     * Set sanitized text
     */
    public function setSanitizedText(string $text): void
    {
        $this->sanitizedText = $text;
    }

    /**
     * Check if update contains text
     */
    public function hasText(): bool
    {
        return !empty($this->getText());
    }

    /**
     * Check if text contains Persian characters
     */
    public function hasPersianText(): bool
    {
        $text = $this->getText();
        if (empty($text)) {
            return false;
        }

        return preg_match('/[\x{0600}-\x{06FF}]/u', $text) === 1;
    }

    /**
     * Check if message is a command
     */
    public function isCommand(): bool
    {
        $text = $this->getText();
        return !empty($text) && str_starts_with($text, '/');
    }

    /**
     * Get command name (without /)
     */
    public function getCommand(): ?string
    {
        if (!$this->isCommand()) {
            return null;
        }

        $text = $this->getText();
        $parts = explode(' ', $text, 2);
        $command = substr($parts[0], 1); // Remove leading /
        
        // Remove bot username if present (command@botname format)
        $command = explode('@', $command)[0];
        
        return strtolower($command);
    }

    /**
     * Get user name (first name or username)
     */
    public function getUserName(): string
    {
        $from = $this->getFrom();
        if (!$from) {
            return 'کاربر';
        }

        return $from['first_name'] ?? $from['username'] ?? 'کاربر';
    }

    /**
     * Get metadata value
     */
    public function getMetadata(string $key, $default = null)
    {
        // This would be set by middleware or other components
        // For now, return default values
        return $default;
    }

    /**
     * Get command arguments
     */
    public function getCommandArgs(): array
    {
        if (!$this->isCommand()) {
            return [];
        }

        $text = $this->getText();
        $parts = explode(' ', $text, 2);
        
        if (count($parts) < 2) {
            return [];
        }

        return array_filter(explode(' ', $parts[1]));
    }

    /**
     * Get command arguments as string
     */
    public function getCommandArgsString(): ?string
    {
        if (!$this->isCommand()) {
            return null;
        }

        $text = $this->getText();
        $parts = explode(' ', $text, 2);
        
        return $parts[1] ?? null;
    }

    /**
     * Check if message is from private chat
     */
    public function isPrivateChat(): bool
    {
        $chat = $this->getChat();
        return isset($chat['type']) && $chat['type'] === 'private';
    }

    /**
     * Check if message is from group chat
     */
    public function isGroupChat(): bool
    {
        $chat = $this->getChat();
        return isset($chat['type']) && in_array($chat['type'], ['group', 'supergroup']);
    }

    /**
     * Check if message is from channel
     */
    public function isChannelPost(): bool
    {
        return $this->type === 'channel_post' || $this->type === 'edited_channel_post';
    }

    /**
     * Check if user is admin (based on configured admin chat IDs)
     */
    public function isFromAdmin(): bool
    {
        $userId = $this->getUserId();
        if (empty($userId)) {
            return false;
        }

        $adminIds = config('services.telegram.admin_chat_ids', env('TELEGRAM_ADMIN_CHAT_IDS', ''));
        $adminChatIds = array_filter(array_map('trim', explode(',', $adminIds)));
        
        return in_array($userId, $adminChatIds);
    }

    /**
     * Get message ID
     */
    public function getMessageId(): ?int
    {
        return $this->message['message_id'] ?? null;
    }

    /**
     * Get message date
     */
    public function getMessageDate(): ?int
    {
        return $this->message['date'] ?? null;
    }

    /**
     * Check if message has photo
     */
    public function hasPhoto(): bool
    {
        return isset($this->message['photo']);
    }

    /**
     * Get photo array
     */
    public function getPhoto(): ?array
    {
        return $this->message['photo'] ?? null;
    }

    /**
     * Check if message has document
     */
    public function hasDocument(): bool
    {
        return isset($this->message['document']);
    }

    /**
     * Get document information
     */
    public function getDocument(): ?array
    {
        return $this->message['document'] ?? null;
    }

    /**
     * Check if message has location
     */
    public function hasLocation(): bool
    {
        return isset($this->message['location']);
    }

    /**
     * Get location information
     */
    public function getLocation(): ?array
    {
        return $this->message['location'] ?? null;
    }

    /**
     * Check if message has contact
     */
    public function hasContact(): bool
    {
        return isset($this->message['contact']);
    }

    /**
     * Get contact information
     */
    public function getContact(): ?array
    {
        return $this->message['contact'] ?? null;
    }

    /**
     * Get callback query data
     */
    public function getCallbackData(): ?string
    {
        return $this->callbackQuery['data'] ?? null;
    }

    /**
     * Get callback query ID
     */
    public function getCallbackQueryId(): ?string
    {
        return $this->callbackQuery['id'] ?? null;
    }

    /**
     * Get inline query
     */
    public function getInlineQuery(): ?string
    {
        return $this->inlineQuery['query'] ?? null;
    }

    /**
     * Get inline query ID
     */
    public function getInlineQueryId(): ?string
    {
        return $this->inlineQuery['id'] ?? null;
    }

    /**
     * Get reply to message
     */
    public function getReplyToMessage(): ?array
    {
        return $this->message['reply_to_message'] ?? null;
    }

    /**
     * Check if message is reply
     */
    public function isReply(): bool
    {
        return !empty($this->getReplyToMessage());
    }

    /**
     * Get forward information
     */
    public function getForwardFrom(): ?array
    {
        return $this->message['forward_from'] ?? null;
    }

    /**
     * Check if message is forwarded
     */
    public function isForwarded(): bool
    {
        return !empty($this->getForwardFrom()) || !empty($this->message['forward_date'] ?? null);
    }

    /**
     * Get entities (mentions, hashtags, URLs, etc.)
     */
    public function getEntities(): array
    {
        return $this->message['entities'] ?? [];
    }

    /**
     * Check if message has entities of specific type
     */
    public function hasEntityType(string $type): bool
    {
        foreach ($this->getEntities() as $entity) {
            if ($entity['type'] === $type) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get entities of specific type
     */
    public function getEntitiesByType(string $type): array
    {
        return array_filter($this->getEntities(), fn($entity) => $entity['type'] === $type);
    }

    /**
     * Get mentioned users
     */
    public function getMentions(): array
    {
        return $this->getEntitiesByType('mention');
    }

    /**
     * Get hashtags
     */
    public function getHashtags(): array
    {
        return $this->getEntitiesByType('hashtag');
    }

    /**
     * Get URLs
     */
    public function getUrls(): array
    {
        return $this->getEntitiesByType('url');
    }

    /**
     * Convert to array for logging/debugging
     */
    public function toArray(): array
    {
        return [
            'update_id' => $this->getUpdateId(),
            'type' => $this->getType(),
            'user_id' => $this->getUserId(),
            'chat_id' => $this->getChatId(),
            'text' => $this->getText(),
            'is_command' => $this->isCommand(),
            'command' => $this->getCommand(),
            'has_persian_text' => $this->hasPersianText(),
            'is_private_chat' => $this->isPrivateChat(),
            'is_from_admin' => $this->isFromAdmin(),
            'message_id' => $this->getMessageId(),
            'message_date' => $this->getMessageDate(),
        ];
    }

    /**
     * String representation for debugging
     */
    public function __toString(): string
    {
        return sprintf(
            'UpdateContext[%s:%s] User:%s Chat:%s %s',
            $this->getUpdateId(),
            $this->getType(),
            $this->getUserId(),
            $this->getChatId(),
            $this->isCommand() ? 'CMD:' . $this->getCommand() : 'TEXT'
        );
    }
}