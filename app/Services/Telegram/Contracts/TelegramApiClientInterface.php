<?php

namespace App\Services\Telegram\Contracts;

use App\Services\Telegram\Core\TelegramResponse;

/**
 * Telegram API Client Interface
 * 
 * Defines the contract for communicating with Telegram Bot API
 * Enables dependency injection and testability
 */
interface TelegramApiClientInterface
{
    /**
     * Send a text message to a chat
     */
    public function sendMessage(string $chatId, string $text, array $options = []): TelegramResponse;

    /**
     * Edit an existing message
     */
    public function editMessage(string $chatId, int $messageId, string $text, array $options = []): TelegramResponse;

    /**
     * Delete a message
     */
    public function deleteMessage(string $chatId, int $messageId): TelegramResponse;

    /**
     * Send a photo with caption
     */
    public function sendPhoto(string $chatId, string $photo, string $caption = '', array $options = []): TelegramResponse;

    /**
     * Send a document
     */
    public function sendDocument(string $chatId, string $document, string $caption = '', array $options = []): TelegramResponse;

    /**
     * Answer callback query (for inline keyboards)
     */
    public function answerCallbackQuery(string $callbackQueryId, string $text = '', bool $showAlert = false): TelegramResponse;

    /**
     * Get bot information
     */
    public function getMe(): TelegramResponse;

    /**
     * Set webhook URL
     */
    public function setWebhook(string $url, array $options = []): TelegramResponse;

    /**
     * Remove webhook
     */
    public function deleteWebhook(): TelegramResponse;

    /**
     * Get current webhook info
     */
    public function getWebhookInfo(): TelegramResponse;

    /**
     * Send chat action (typing, upload_photo, etc.)
     */
    public function sendChatAction(string $chatId, string $action): TelegramResponse;

    /**
     * Get chat information
     */
    public function getChat(string $chatId): TelegramResponse;

    /**
     * Get chat member information
     */
    public function getChatMember(string $chatId, string $userId): TelegramResponse;

    /**
     * Ban chat member
     */
    public function banChatMember(string $chatId, string $userId, array $options = []): TelegramResponse;

    /**
     * Unban chat member
     */
    public function unbanChatMember(string $chatId, string $userId, array $options = []): TelegramResponse;

    /**
     * Forward a message
     */
    public function forwardMessage(string $chatId, string $fromChatId, int $messageId, array $options = []): TelegramResponse;

    /**
     * Send location
     */
    public function sendLocation(string $chatId, float $latitude, float $longitude, array $options = []): TelegramResponse;

    /**
     * Send venue
     */
    public function sendVenue(string $chatId, float $latitude, float $longitude, string $title, string $address, array $options = []): TelegramResponse;

    /**
     * Send contact
     */
    public function sendContact(string $chatId, string $phoneNumber, string $firstName, array $options = []): TelegramResponse;

    /**
     * Send poll
     */
    public function sendPoll(string $chatId, string $question, array $options, array $pollOptions = []): TelegramResponse;

    /**
     * Stop poll
     */
    public function stopPoll(string $chatId, int $messageId, array $options = []): TelegramResponse;

    /**
     * Answer inline query
     */
    public function answerInlineQuery(string $inlineQueryId, array $results, array $options = []): TelegramResponse;
}