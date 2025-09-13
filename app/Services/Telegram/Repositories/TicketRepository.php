<?php

namespace App\Services\Telegram\Repositories;

use App\Services\Telegram\Contracts\TicketRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * Ticket Repository Implementation
 * 
 * Database implementation for ticket management
 * Uses Laravel's query builder for database operations
 */
class TicketRepository implements TicketRepositoryInterface
{
    private const CACHE_TTL = 300; // 5 minutes

    /**
     * Create a new support ticket
     */
    public function createTicket(array $data): int
    {
        $ticketId = DB::table('telegram_tickets')->insertGetId([
            'user_id' => $data['user_id'],
            'user_name' => $data['user_name'],
            'subject' => $data['subject'],
            'status' => $data['status'] ?? 'open',
            'priority' => $data['priority'] ?? 'normal',
            'assigned_to' => $data['assigned_to'] ?? null,
            'created_at' => $data['created_at'] ?? now(),
            'updated_at' => now(),
        ]);

        // Clear user tickets cache
        Cache::forget("telegram_user_tickets:{$data['user_id']}");
        
        return $ticketId;
    }

    /**
     * Get user's tickets
     */
    public function getUserTickets(string $userId, int $limit = 50): array
    {
        $cacheKey = "telegram_user_tickets:{$userId}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId, $limit) {
            return DB::table('telegram_tickets')
                ->where('user_id', $userId)
                ->select([
                    'id',
                    'subject',
                    'status',
                    'priority',
                    'created_at',
                    'updated_at',
                ])
                ->selectRaw('(SELECT COUNT(*) FROM telegram_ticket_messages WHERE ticket_id = telegram_tickets.id) as messages_count')
                ->orderBy('updated_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($ticket) {
                    return (array) $ticket;
                })
                ->toArray();
        });
    }

    /**
     * Get specific user ticket
     */
    public function getUserTicket(int $ticketId, string $userId): ?array
    {
        $ticket = DB::table('telegram_tickets')
            ->where('id', $ticketId)
            ->where('user_id', $userId)
            ->first();

        return $ticket ? (array) $ticket : null;
    }

    /**
     * Get user ticket with messages
     */
    public function getUserTicketWithMessages(int $ticketId, string $userId): ?array
    {
        $ticket = $this->getUserTicket($ticketId, $userId);
        
        if (!$ticket) {
            return null;
        }

        $messages = DB::table('telegram_ticket_messages')
            ->where('ticket_id', $ticketId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return (array) $message;
            })
            ->toArray();

        $ticket['messages'] = $messages;
        
        return $ticket;
    }

    /**
     * Add message to ticket
     */
    public function addTicketMessage(int $ticketId, array $messageData): int
    {
        $messageId = DB::table('telegram_ticket_messages')->insertGetId([
            'ticket_id' => $ticketId,
            'user_id' => $messageData['user_id'],
            'message' => $messageData['message'],
            'is_admin' => $messageData['is_admin'] ?? false,
            'created_at' => $messageData['created_at'] ?? now(),
        ]);

        // Update ticket's updated_at timestamp
        DB::table('telegram_tickets')
            ->where('id', $ticketId)
            ->update(['updated_at' => now()]);

        // Clear caches
        $ticket = DB::table('telegram_tickets')->where('id', $ticketId)->first();
        if ($ticket) {
            Cache::forget("telegram_user_tickets:{$ticket->user_id}");
        }

        return $messageId;
    }

    /**
     * Update ticket status
     */
    public function updateTicketStatus(int $ticketId, string $status): bool
    {
        $updated = DB::table('telegram_tickets')
            ->where('id', $ticketId)
            ->update([
                'status' => $status,
                'updated_at' => now(),
            ]);

        if ($updated) {
            // Clear user cache
            $ticket = DB::table('telegram_tickets')->where('id', $ticketId)->first();
            if ($ticket) {
                Cache::forget("telegram_user_tickets:{$ticket->user_id}");
            }
        }

        return $updated > 0;
    }

    /**
     * Update ticket priority
     */
    public function updateTicketPriority(int $ticketId, string $priority): bool
    {
        $updated = DB::table('telegram_tickets')
            ->where('id', $ticketId)
            ->update([
                'priority' => $priority,
                'updated_at' => now(),
            ]);

        if ($updated) {
            // Clear user cache
            $ticket = DB::table('telegram_tickets')->where('id', $ticketId)->first();
            if ($ticket) {
                Cache::forget("telegram_user_tickets:{$ticket->user_id}");
            }
        }

        return $updated > 0;
    }

    /**
     * Get ticket statistics
     */
    public function getTicketStats(string $userId = null): array
    {
        $query = DB::table('telegram_tickets');
        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $stats = $query->selectRaw('
            status,
            COUNT(*) as count,
            AVG(CASE WHEN status = "closed" THEN 
                TIMESTAMPDIFF(HOUR, created_at, updated_at) 
                ELSE NULL END) as avg_resolution_hours
        ')
        ->groupBy('status')
        ->get()
        ->keyBy('status')
        ->map(function ($stat) {
            return [
                'count' => (int) $stat->count,
                'avg_resolution_hours' => $stat->avg_resolution_hours ? round($stat->avg_resolution_hours, 2) : null,
            ];
        })
        ->toArray();

        // Add total count
        $total = array_sum(array_column($stats, 'count'));
        $stats['total'] = ['count' => $total, 'avg_resolution_hours' => null];

        return $stats;
    }

    /**
     * Search tickets
     */
    public function searchTickets(array $filters, int $limit = 50): array
    {
        $query = DB::table('telegram_tickets');

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('subject', 'LIKE', "%{$filters['search']}%")
                  ->orWhere('user_name', 'LIKE', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($ticket) {
                return (array) $ticket;
            })
            ->toArray();
    }

    /**
     * Get tickets by status
     */
    public function getTicketsByStatus(string $status, int $limit = 50): array
    {
        return DB::table('telegram_tickets')
            ->where('status', $status)
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($ticket) {
                return (array) $ticket;
            })
            ->toArray();
    }

    /**
     * Get admin tickets (all tickets for admin panel)
     */
    public function getAdminTickets(array $filters = [], int $limit = 50): array
    {
        $query = DB::table('telegram_tickets');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        return $query->select([
                'id',
                'user_id',
                'user_name',
                'subject',
                'status',
                'priority',
                'assigned_to',
                'created_at',
                'updated_at',
            ])
            ->selectRaw('(SELECT COUNT(*) FROM telegram_ticket_messages WHERE ticket_id = telegram_tickets.id) as messages_count')
            ->selectRaw('(SELECT created_at FROM telegram_ticket_messages WHERE ticket_id = telegram_tickets.id ORDER BY created_at DESC LIMIT 1) as last_message_at')
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($ticket) {
                return (array) $ticket;
            })
            ->toArray();
    }

    /**
     * Assign ticket to admin
     */
    public function assignTicket(int $ticketId, string $adminUserId): bool
    {
        $updated = DB::table('telegram_tickets')
            ->where('id', $ticketId)
            ->update([
                'assigned_to' => $adminUserId,
                'updated_at' => now(),
            ]);

        if ($updated) {
            // Clear user cache
            $ticket = DB::table('telegram_tickets')->where('id', $ticketId)->first();
            if ($ticket) {
                Cache::forget("telegram_user_tickets:{$ticket->user_id}");
            }
        }

        return $updated > 0;
    }

    /**
     * Close expired tickets
     */
    public function closeExpiredTickets(int $daysOld = 30): int
    {
        $cutoffDate = now()->subDays($daysOld);
        
        $updated = DB::table('telegram_tickets')
            ->whereIn('status', ['open', 'waiting_user'])
            ->where('updated_at', '<', $cutoffDate)
            ->update([
                'status' => 'closed',
                'updated_at' => now(),
            ]);

        // Clear all user caches (simple approach)
        if ($updated > 0) {
            Cache::flush(); // In production, you'd want more targeted cache clearing
        }

        return $updated;
    }

    /**
     * Get ticket by ID (admin access)
     */
    public function getTicketById(int $ticketId): ?array
    {
        $ticket = DB::table('telegram_tickets')
            ->where('id', $ticketId)
            ->first();

        return $ticket ? (array) $ticket : null;
    }

    /**
     * Get ticket messages
     */
    public function getTicketMessages(int $ticketId, int $limit = 50): array
    {
        return DB::table('telegram_ticket_messages')
            ->where('ticket_id', $ticketId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($message) {
                return (array) $message;
            })
            ->reverse() // Show oldest first
            ->values()
            ->toArray();
    }
}