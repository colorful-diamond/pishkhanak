<?php

namespace App\Services\Telegram\Contracts;

/**
 * Ticket Repository Contract
 * 
 * Defines data access methods for ticket management
 * Enables dependency injection and testing
 */
interface TicketRepositoryInterface
{
    /**
     * Create a new support ticket
     */
    public function createTicket(array $data): int;

    /**
     * Get user's tickets
     */
    public function getUserTickets(string $userId, int $limit = 50): array;

    /**
     * Get specific user ticket
     */
    public function getUserTicket(int $ticketId, string $userId): ?array;

    /**
     * Get user ticket with messages
     */
    public function getUserTicketWithMessages(int $ticketId, string $userId): ?array;

    /**
     * Add message to ticket
     */
    public function addTicketMessage(int $ticketId, array $messageData): int;

    /**
     * Update ticket status
     */
    public function updateTicketStatus(int $ticketId, string $status): bool;

    /**
     * Update ticket priority
     */
    public function updateTicketPriority(int $ticketId, string $priority): bool;

    /**
     * Get ticket statistics
     */
    public function getTicketStats(string $userId = null): array;

    /**
     * Search tickets
     */
    public function searchTickets(array $filters, int $limit = 50): array;

    /**
     * Get tickets by status
     */
    public function getTicketsByStatus(string $status, int $limit = 50): array;

    /**
     * Get admin tickets (all tickets for admin panel)
     */
    public function getAdminTickets(array $filters = [], int $limit = 50): array;

    /**
     * Assign ticket to admin
     */
    public function assignTicket(int $ticketId, string $adminUserId): bool;

    /**
     * Close expired tickets
     */
    public function closeExpiredTickets(int $daysOld = 30): int;

    /**
     * Get ticket by ID (admin access)
     */
    public function getTicketById(int $ticketId): ?array;

    /**
     * Get ticket messages
     */
    public function getTicketMessages(int $ticketId, int $limit = 50): array;
}