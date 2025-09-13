<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\TicketPriority;
use App\Models\TicketCategory;
use App\Models\TicketMessage;
use App\Models\TicketAttachment;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Fixed version of TelegramTicketBot with proper error handling
 */
class TelegramTicketBot
{
    protected $botToken;
    protected $telegramService;
    protected $adminChatIds = [];
    protected $commandHandlers = [];
    
    // User states
    const STATE_IDLE = 'idle';
    const STATE_WAITING_REPLY = 'waiting_reply';
    const STATE_WAITING_SEARCH = 'waiting_search';
    const STATE_WAITING_CLOSE_REASON = 'waiting_close_reason';
    const STATE_WAITING_STATUS = 'waiting_status';
    const STATE_WAITING_PRIORITY = 'waiting_priority';
    const STATE_WAITING_ASSIGN = 'waiting_assign';
    const STATE_WAITING_CATEGORY = 'waiting_category';
    
    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->telegramService = new TelegramNotificationService();
        
        // Load admin chat IDs from config
        $adminIds = config('services.telegram.admin_chat_ids', '');
        $this->adminChatIds = array_filter(array_map('trim', explode(',', $adminIds)));
        
        // Register command handlers
        $this->registerCommands();
    }
    
    /**
     * Register all bot commands
     */
    protected function registerCommands()
    {
        $this->commandHandlers = [
            '/start' => 'handleStart',
            '/help' => 'handleHelp',
            '/tickets' => 'handleListTickets',
            '/ticket' => 'handleViewTicket',
            '/search' => 'handleSearch',
            '/reply' => 'handleReply',
            '/close' => 'handleCloseTicket',
            '/reopen' => 'handleReopenTicket',
            '/assign' => 'handleAssignTicket',
            '/priority' => 'handleSetPriority',
            '/category' => 'handleSetCategory',
            '/stats' => 'handleStatistics',
            '/export' => 'handleExport',
            '/notifications' => 'handleNotificationSettings',
        ];
    }
    
    /**
     * Process incoming webhook update with error handling
     */
    public function processUpdate(array $update)
    {
        try {
            // Handle callback queries (inline keyboard buttons)
            if (isset($update['callback_query'])) {
                return $this->handleCallbackQuery($update['callback_query']);
            }
            
            // Handle regular messages
            if (isset($update['message'])) {
                return $this->handleMessage($update['message']);
            }
            
            // Handle channel posts
            if (isset($update['channel_post'])) {
                return $this->handleChannelPost($update['channel_post']);
            }
            
        } catch (\Exception $e) {
            Log::error('Telegram bot error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'update' => $update
            ]);
            
            // Try to send error message to user
            $chatId = $this->extractChatId($update);
            if ($chatId) {
                $this->sendMessage($chatId, "متاسفانه خطایی رخ داده است");
            }
        }
    }
    
    /**
     * Extract chat ID from update
     */
    protected function extractChatId($update)
    {
        if (isset($update['message']['chat']['id'])) {
            return $update['message']['chat']['id'];
        }
        if (isset($update['callback_query']['message']['chat']['id'])) {
            return $update['callback_query']['message']['chat']['id'];
        }
        return null;
    }
    
    /**
     * Handle incoming message with proper error handling
     */
    protected function handleMessage($message)
    {
        try {
            $chatId = $message['chat']['id'];
            $userId = $message['from']['id'];
            $text = $message['text'] ?? '';
            
            // Check if user is authorized
            if (!$this->isAuthorized($userId)) {
                return $this->sendMessage($chatId, "847b0fd7");
            }
            
            // Check user state first
            $userState = $this->getUserState($userId);
            if ($userState['state'] !== self::STATE_IDLE) {
                return $this->handleStatefulMessage($chatId, $userId, $text, $userState);
            }
            
            // Handle file uploads
            if (isset($message['document']) || isset($message['photo'])) {
                return $this->handleFileUpload($chatId, $userId, $message);
            }
            
            // Parse command
            if (strpos($text, '/') === 0) {
                $parts = explode(' ', $text);
                $command = $parts[0];
                $args = array_slice($parts, 1);
                
                if (isset($this->commandHandlers[$command])) {
                    $handler = $this->commandHandlers[$command];
                    return $this->$handler($chatId, $userId, $args);
                } else {
                    return $this->sendMessage($chatId, "دستور شناخته شده نیست");
                }
            }
            
            // If not a command, treat as search
            return $this->handleSearch($chatId, $userId, [$text]);
            
        } catch (\Exception $e) {
            Log::error('Error handling message', [
                'error' => $e->getMessage(),
                'message' => $message
            ]);
            
            return $this->sendMessage($message['chat']['id'] ?? 0, "1f0931c1");
        }
    }
    
    /**
     * Check if user is authorized
     */
    protected function isAuthorized($userId)
    {
        // Check if user is in admin list
        if (in_array($userId, $this->adminChatIds)) {
            return true;
        }
        
        // Check if user exists in database with proper role
        try {
            $user = User::where('telegram_chat_id', $userId)->first();
            if ($user && method_exists($user, 'hasRole')) {
                return $user->hasRole(['admin', 'support']);
            } elseif ($user) {
                // If hasRole method doesn't exist, check if user is admin another way
                return $user->is_admin ?? false;
            }
        } catch (\Exception $e) {
            Log::warning('Error checking user authorization', ['error' => $e->getMessage()]);
        }
        
        return false;
    }
    
    /**
     * Handle /start command
     */
    protected function handleStart($chatId, $userId, $args)
    {
        $message = "PERSIAN_TEXT_aca4951e";
        $message .= "c721cb92";
        $message .= "PERSIAN_TEXT_17f2fec1";
        $message .= "5059dbd6";
        $message .= "PERSIAN_TEXT_cf0b718a";
        $message .= "5b75caab";
        $message .= "PERSIAN_TEXT_1adcd1ee";
        $message .= "de3be892";
        
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'PERSIAN_TEXT_20cefc22', 'callback_data' => 'tickets_open_1'],
                    ['text' => 'PERSIAN_TEXT_73399ed7', 'callback_data' => 'stats_today']
                ],
                [
                    ['text' => 'PERSIAN_TEXT_776716d3', 'callback_data' => 'search_prompt'],
                    ['text' => 'PERSIAN_TEXT_43bb5e2a', 'callback_data' => 'help']
                ]
            ]
        ];
        
        return $this->sendMessage($chatId, $message, $keyboard, 'Markdown');
    }
    
    /**
     * Handle /tickets command - List tickets with proper error handling
     */
    protected function handleListTickets($chatId, $userId, $args)
    {
        try {
            $status = $args[0] ?? 'open';
            $page = $args[1] ?? 1;
            
            $query = Ticket::with(['user', 'assignedTo']);
            
            // Apply status filter safely
            if ($status !== 'all') {
                // Check if we have status_id or just status field
                if (DB::getSchemaBuilder()->hasColumn('tickets', 'status_id')) {
                    $statusModel = DB::table('ticket_statuses')->where('slug', $status)->first();
                    if ($statusModel) {
                        $query->where('status_id', $statusModel->id);
                    } else {
                        $query->where('status', $status);
                    }
                } else {
                    $query->where('status', $status);
                }
            }
            
            // Pagination
            $perPage = 5;
            $total = $query->count();
            $tickets = $query->orderBy('created_at', 'desc')
                           ->skip(($page - 1) * $perPage)
                           ->take($perPage)
                           ->get();
            
            if ($tickets->isEmpty()) {
                return $this->sendMessage($chatId, "PERSIAN_TEXT_66ba5c07");
            }
            
            // Build message
            $statusText = $this->getStatusText($status);
            $message = "278f802c";
            $message .= "PERSIAN_TEXT_661ef264" . ceil($total / $perPage) . "\n\n";
            
            foreach ($tickets as $ticket) {
                $message .= $this->formatTicketSummary($ticket) . "\n";
            }
            
            // Build pagination keyboard
            $keyboard = $this->buildTicketListKeyboard($tickets, $status, $page, ceil($total / $perPage));
            
            return $this->sendMessage($chatId, $message, $keyboard, 'Markdown');
            
        } catch (\Exception $e) {
            Log::error('Error listing tickets', ['error' => $e->getMessage()]);
            return $this->sendMessage($chatId, "a493db05");
        }
    }
    
    /**
     * Format ticket summary safely
     */
    protected function formatTicketSummary($ticket)
    {
        try {
            $priorityEmoji = $this->getPriorityEmoji($ticket->priority ?? 'normal');
            $statusEmoji = $this->getStatusEmoji($ticket->status ?? 'open');
            
            $message = "🎫 *#{$ticket->ticket_number}*\n";
            $message .= "📝 " . mb_substr($ticket->subject, 0, 50) . "\n";
            $message .= "👤 "cb56282c"\n";
            $message .= "🏷️ " . $this->getCategoryName($ticket) . "\n";
            $message .= "{$statusEmoji} " . $this->getStatusName($ticket) . "\n";
            $message .= "📅 " . $this->formatDate($ticket->created_at) . "\n";
            
            return $message;
        } catch (\Exception $e) {
            Log::warning('Error formatting ticket', ['error' => $e->getMessage()]);
            return "854b5e3d";
        }
    }
    
    /**
     * Get category name safely
     */
    protected function getCategoryName($ticket)
    {
        // First try category_id with relationship
        if (isset($ticket->category_id) && DB::getSchemaBuilder()->hasTable('ticket_categories')) {
            $category = DB::table('ticket_categories')->find($ticket->category_id);
            if ($category) {
                return $category->name;
            }
        }
        
        // Fall back to category field
        $categories = [
            'general' => 'PERSIAN_TEXT_a1e5d878',
            'technical' => 'PERSIAN_TEXT_4348a334',
            'billing' => 'PERSIAN_TEXT_5587f36d',
            'suggestion' => 'PERSIAN_TEXT_cc625c1a',
            'complaint' => 'PERSIAN_TEXT_65aa5c17'
        ];
        
        return $categories[$ticket->category ?? 'general'] ?? 'PERSIAN_TEXT_a1e5d878';
    }
    
    /**
     * Get status name safely
     */
    protected function getStatusName($ticket)
    {
        // First try status_id with relationship
        if (isset($ticket->status_id) && DB::getSchemaBuilder()->hasTable('ticket_statuses')) {
            $status = DB::table('ticket_statuses')->find($ticket->status_id);
            if ($status) {
                return $status->name;
            }
        }
        
        // Fall back to status field
        $statuses = [
            'open' => 'PERSIAN_TEXT_2e91d38f',
            'pending' => 'PERSIAN_TEXT_dc55d0b5',
            'answered' => 'PERSIAN_TEXT_3f4b3594',
            'resolved' => 'PERSIAN_TEXT_1bbccabe',
            'closed' => 'PERSIAN_TEXT_1eeb4225'
        ];
        
        return $statuses[$ticket->status ?? 'open'] ?? 'PERSIAN_TEXT_2e91d38f';
    }
    
    /**
     * Get priority name safely
     */
    protected function getPriorityName($ticket)
    {
        // First try priority_id with relationship
        if (isset($ticket->priority_id) && DB::getSchemaBuilder()->hasTable('ticket_priorities')) {
            $priority = DB::table('ticket_priorities')->find($ticket->priority_id);
            if ($priority) {
                return $priority->name;
            }
        }
        
        // Fall back to priority field
        $priorities = [
            'low' => 'PERSIAN_TEXT_38b171ab',
            'normal' => 'PERSIAN_TEXT_14184253',
            'high' => 'PERSIAN_TEXT_6986ecfe',
            'urgent' => 'PERSIAN_TEXT_b21bd6a1'
        ];
        
        return $priorities[$ticket->priority ?? 'normal'] ?? 'PERSIAN_TEXT_14184253';
    }
    
    /**
     * Format date safely
     */
    protected function formatDate($date)
    {
        try {
            if (class_exists('\Verta')) {
                return \Verta::instance($date)->format('Y/m/d H:i');
            }
            return date('Y/m/d H:i', strtotime($date));
        } catch (\Exception $e) {
            return 'PERSIAN_TEXT_264f61d0';
        }
    }
    
    /**
     * Handle callback queries
     */
    protected function handleCallbackQuery($callbackQuery)
    {
        try {
            $callbackId = $callbackQuery['id'];
            $chatId = $callbackQuery['message']['chat']['id'] ?? $callbackQuery['from']['id'];
            $userId = $callbackQuery['from']['id'];
            $data = $callbackQuery['data'];
            $messageId = $callbackQuery['message']['message_id'] ?? null;
            
            // Check authorization first
            if (!$this->isAuthorized($userId)) {
                $this->answerCallbackQuery($callbackId, "PERSIAN_TEXT_6a25113c", true);
                return $this->sendMessage($chatId, "6a25113c");
            }
            
            // Parse callback data
            $parts = explode('_', $data);
            $action = $parts[0];
            
            // Prepare response text based on action
            $responseText = '';
            switch ($action) {
                case 'tickets':
                    $status = $parts[1] ?? 'open';
                    $statusText = $this->getStatusText($status);
                    $responseText = "PERSIAN_TEXT_5ed5e22d";
                    break;
                    
                case 'view':
                    $responseText = "54ce928f";
                    break;
                    
                case 'stats':
                    $period = $parts[1] ?? 'today';
                    $periodText = ['today' => 'PERSIAN_TEXT_aac7d1e0', 'week' => 'PERSIAN_TEXT_76bff395', 'month' => 'PERSIAN_TEXT_1c9e87a6', 'all' => 'PERSIAN_TEXT_c51219f5'][$period] ?? $period;
                    $responseText = "PERSIAN_TEXT_bc076ec5";
                    break;
                    
                case 'search':
                    $responseText = "e0c60a9b";
                    break;
                    
                case 'help':
                    $responseText = "PERSIAN_TEXT_f5b93894";
                    break;
                    
                case 'reply':
                    $responseText = "12e49cea";
                    break;
                    
                case 'close':
                    $responseText = "PERSIAN_TEXT_2bf4b08e";
                    break;
                    
                case 'reopen':
                    $responseText = "a3d48ee2";
                    break;
                    
                case 'messages':
                    $responseText = "PERSIAN_TEXT_e92bbb11";
                    break;
                    
                case 'export':
                    $responseText = "4f227da4";
                    break;
                    
                default:
                    $responseText = "PERSIAN_TEXT_0c089469";
            }
            
            // Answer callback with appropriate text
            $this->answerCallbackQuery($callbackId, $responseText);
            
            // Handle the action
            switch ($action) {
                case 'tickets':
                    $status = $parts[1] ?? 'open';
                    $page = $parts[2] ?? 1;
                    return $this->handleListTickets($chatId, $userId, [$status, $page]);
                    
                case 'view':
                    $ticketId = $parts[1] ?? 0;
                    return $this->handleViewTicket($chatId, $userId, [$ticketId]);
                    
                case 'stats':
                    $period = $parts[1] ?? 'today';
                    return $this->handleStatistics($chatId, $userId, [$period]);
                    
                case 'search':
                    if (isset($parts[1]) && $parts[1] === 'prompt') {
                        return $this->promptSearch($chatId, $userId);
                    }
                    break;
                    
                case 'help':
                    return $this->handleHelp($chatId, $userId, []);
                    
                case 'reply':
                    $ticketId = $parts[1] ?? 0;
                    $this->setUserState($userId, self::STATE_WAITING_REPLY, ['ticket_id' => $ticketId]);
                    return $this->sendMessage($chatId, "f967eebf");
                    
                case 'close':
                    $ticketId = $parts[1] ?? 0;
                    return $this->handleCloseTicket($chatId, $userId, [$ticketId]);
                    
                case 'reopen':
                    $ticketId = $parts[1] ?? 0;
                    return $this->handleReopenTicket($chatId, $userId, [$ticketId]);
                    
                case 'messages':
                    $ticketId = $parts[1] ?? 0;
                    return $this->showTicketMessages($chatId, $ticketId);
                    
                case 'export':
                    $period = $parts[1] ?? 'all';
                    return $this->handleExport($chatId, $userId, [$period]);
                    
                case 'cancel':
                    if (isset($parts[1]) && $parts[1] === 'reply') {
                        $this->clearUserState($userId);
                        $this->answerCallbackQuery($callbackId, "PERSIAN_TEXT_be85b8d8");
                        return $this->sendMessage($chatId, "78211a41");
                    }
                    break;
                    
                case 'assign':
                    $ticketId = $parts[1] ?? 0;
                    $assignTo = $parts[2] ?? null;
                    if ($assignTo === 'me') {
                        return $this->assignTicketToMe($chatId, $userId, $ticketId);
                    }
                    return $this->handleAssignTicket($chatId, $userId, [$ticketId, $assignTo]);
                    
                case 'priority':
                    $ticketId = $parts[1] ?? 0;
                    return $this->showPriorityOptions($chatId, $ticketId);
                    
                case 'category':
                    $ticketId = $parts[1] ?? 0;
                    return $this->showCategoryOptions($chatId, $ticketId);
                    
                case 'resolve':
                    $ticketId = $parts[1] ?? 0;
                    return $this->resolveTicket($chatId, $userId, $ticketId);
                    
                default:
                    $this->answerCallbackQuery($callbackId, "PERSIAN_TEXT_2f38ed6b", true);
                    return $this->sendMessage($chatId, "2f38ed6b");
            }
            
        } catch (\Exception $e) {
            Log::error('Error handling callback query', [
                'error' => $e->getMessage(),
                'callback' => $callbackQuery
            ]);
            
            return $this->sendMessage($chatId ?? 0, "PERSIAN_TEXT_8e830861");
        }
    }
    
    /**
     * Answer callback query
     */
    protected function answerCallbackQuery($callbackId, $text = null, $showAlert = false)
    {
        try {
            return $this->sendTelegramRequest('answerCallbackQuery', [
                'callback_query_id' => $callbackId,
                'text' => $text,
                'show_alert' => $showAlert
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to answer callback query', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Handle statistics command
     */
    protected function handleStatistics($chatId, $userId, $args)
    {
        try {
            $period = $args[0] ?? 'today';
            
            $query = Ticket::query();
            
            // Apply period filter
            switch ($period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    $periodText = 'PERSIAN_TEXT_aac7d1e0';
                    break;
                case 'week':
                    $query->where('created_at', '>=', now()->subWeek());
                    $periodText = 'PERSIAN_TEXT_b2e84f4c';
                    break;
                case 'month':
                    $query->where('created_at', '>=', now()->subMonth());
                    $periodText = 'PERSIAN_TEXT_b0d8c8cc';
                    break;
                default:
                    $periodText = 'PERSIAN_TEXT_c51219f5';
            }
            
            // Get statistics
            $total = $query->count();
            $open = (clone $query)->where('status', 'open')->count();
            $closed = (clone $query)->where('status', 'closed')->count();
            $resolved = (clone $query)->where('status', 'resolved')->count();
            $pending = (clone $query)->where('status', 'pending')->count();
            
            // Average response time (PostgreSQL compatible)
            $avgResponseTime = null;
            try {
                $dbDriver = DB::connection()->getDriverName();
                if ($dbDriver === 'pgsql') {
                    // PostgreSQL syntax
                    $avgResponseTime = (clone $query)->whereNotNull('first_response_at')
                        ->selectRaw("AVG(EXTRACT(EPOCH FROM (first_response_at - created_at))/60) as avg_time")
                        ->value('avg_time');
                } else {
                    // MySQL syntax
                    $avgResponseTime = (clone $query)->whereNotNull('first_response_at')
                        ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, first_response_at)) as avg_time')
                        ->value('avg_time');
                }
            } catch (\Exception $e) {
                Log::warning('Could not calculate average response time', ['error' => $e->getMessage()]);
                $avgResponseTime = null;
            }
            
            $message = "edb82134";
            $message .= "PERSIAN_TEXT_0c62f890";
            $message .= "250cdc93";
            $message .= "PERSIAN_TEXT_a379f6fe";
            $message .= "b29210ae";
            $message .= "PERSIAN_TEXT_8c8d30b0";
            
            if ($avgResponseTime) {
                $hours = floor($avgResponseTime / 60);
                $minutes = $avgResponseTime % 60;
                $message .= "8822692e";
            }
            
            // Top categories
            $topCategories = (clone $query)
                ->select('category')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('category')
                ->orderBy('count', 'desc'PERSIAN_TEXT_14ce376b'state' => self::STATE_IDLE,
            'data' => []
        ]);
    }
    
    protected function setUserState($userId, $state, $data = [])
    {
        Cache::put("telegram_bot_state_{$userId}", [
            'state' => $state,
            'data' => $data
        ], 3600);
    }
    
    protected function clearUserState($userId)
    {
        Cache::forget("telegram_bot_state_{$userId}");
    }
    
    protected function handleStatefulMessage($chatId, $userId, $text, $userState)
    {
        // Handle based on current state
        switch ($userState['state']) {
            case self::STATE_WAITING_REPLY:
                return $this->processReply($chatId, $userId, $text, $userState['data']);
                
            case self::STATE_WAITING_SEARCH:
                return $this->processSearch($chatId, $userId, $text);
                
            default:
                $this->clearUserState($userId);
                return $this->sendMessage($chatId, "f75e1116");
        }
    }
    
    protected function handleViewTicket($chatId, $userId, $args)
    {
        $ticketId = $args[0] ?? null;
        
        if (!$ticketId) {
            return $this->sendMessage($chatId, "PERSIAN_TEXT_0b3efd73");
        }
        
        try {
            $ticket = Ticket::with(['user', 'assignedTo', 'messages', 'attachments'])
                          ->where('ticket_number', $ticketId)
                          ->orWhere('id', $ticketId)
                          ->first();
            
            if (!$ticket) {
                return $this->sendMessage($chatId, "a8908e0f");
            }
            
            $message = $this->formatTicketDetails($ticket);
            $keyboard = $this->buildTicketActionsKeyboard($ticket);
            
            return $this->sendMessage($chatId, $message, $keyboard, 'Markdown');
            
        } catch (\Exception $e) {
            Log::error('Error viewing ticket', ['error' => $e->getMessage()]);
            return $this->sendMessage($chatId, "PERSIAN_TEXT_a8b92220");
        }
    }
    
    protected function formatTicketDetails($ticket)
    {
        $message = "c2fdf8dc";
        $message .= "PERSIAN_TEXT_d5533675";
        $message .= "37f87463" . ($ticket->user->name ?? 'PERSIAN_TEXT_1789f5ad') . "\n";
        $message .= "83a276f8" . ($ticket->user->email ?? '-') . "\n\n";
        
        $message .= "efaa83f9" . $this->getCategoryName($ticket) . "\n";
        $message .= $this->getPriorityEmoji($ticket->priority ?? 'normal') . "b2a09fcb" . $this->getPriorityName($ticket) . "\n";
        $message .= $this->getStatusEmoji($ticket->status ?? 'open') . "47143e6f" . $this->getStatusName($ticket) . "\n\n";
        
        $message .= "c0807a14" . $this->formatDate($ticket->created_at) . "\n";
        
        if ($ticket->assignedTo) {
            $message .= "6770c38f" . $ticket->assignedTo->name . "\n";
        }
        
        $message .= "13504abf" . mb_substr($ticket->description, 0, 500);
        
        return $message;
    }
    
    protected function buildTicketActionsKeyboard($ticket)
    {
        $keyboard = ['inline_keyboard' => []];
        
        // Reply button
        $keyboard['inline_keyboard'][] = [
            ['text' => 'PERSIAN_TEXT_76509c01', 'callback_data' => "reply_{$ticket->id}"PERSIAN_TEXT_116b3ed9"close_{$ticket->id}"5d21ff65"reopen_{$ticket->id}"PERSIAN_TEXT_6e59b185"messages_{$ticket->id}"]
        ];
        
        return $keyboard;
    }
    
    protected function buildTicketListKeyboard($tickets, $status, $page, $totalPages)
    {
        $keyboard = ['inline_keyboard' => []];
        
        // Ticket action buttons
        foreach ($tickets as $ticket) {
            $keyboard['inline_keyboard'][] = [
                ['text' => "9d21baa8", 'callback_data'PERSIAN_TEXT_2e36b049'user'])
                ->where(function($q) use ($query) {
                    $q->where('ticket_number', 'like', "%{$query}%")
                      ->orWhere('subject', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhereHas('user', function($q) use ($query) {
                          $q->where('name', 'like', "%{$query}%")
                            ->orWhere('email', 'like', "%{$query}%");
                      });
                })
                ->limit(10)
                ->orderBy('created_at', 'desc')
                ->get();
            
            if ($tickets->isEmpty()) {
                return $this->sendMessage($chatId, "3673d635");
            }
            
            $message = "PERSIAN_TEXT_192a77d2";
            $message .= "8e172394";
            
            foreach ($tickets as $ticket) {
                $message .= $this->formatTicketSummary($ticket) . "\n";
            }
            
            $keyboard = ['inline_keyboard' => []];
            foreach ($tickets as $ticket) {
                $keyboard['inline_keyboard'][] = [
                    ['text' => "9d21baa8", 'callback_data' => "view_{$ticket->id}"]
                ];
            }
            
            return $this->sendMessage($chatId, $message, $keyboard, 'Markdown');
            
        } catch (\Exception $e) {
            Log::error('Search error', ['error' => $e->getMessage()]);
            return $this->sendMessage($chatId, "146eff14");
        }
    }
    
    protected function processSearch($chatId, $userId, $text)
    {
        $this->clearUserState($userId);
        return $this->performSearch($chatId, $userId, $text);
    }
    
    protected function promptSearch($chatId, $userId)
    {
        $this->setUserState($userId, self::STATE_WAITING_SEARCH);
        return $this->sendMessage($chatId, "PERSIAN_TEXT_61f7977e");
    }
    
    protected function handleHelp($chatId, $userId, $args)
    {
        $message = "8d5b11de";
        
        $message .= "PERSIAN_TEXT_31dcdffe";
        $message .= "6a8f0593";
        $message .= "PERSIAN_TEXT_f4351ca8";
        $message .= "939edd20";
        $message .= "PERSIAN_TEXT_c6886b15";
        $message .= "5ef12531";
        $message .= "PERSIAN_TEXT_dd113841";
        
        $message .= "5125ba27";
        $message .= "PERSIAN_TEXT_1bd42510";
        $message .= "6035a253";
        $message .= "PERSIAN_TEXT_129992d3";
        $message .= "b6e97211";
        $message .= "PERSIAN_TEXT_c9573346";
        $message .= "a025fb88";
        
        $message .= "PERSIAN_TEXT_a569502b";
        $message .= "264acb47";
        $message .= "PERSIAN_TEXT_860b8fe0";
        $message .= "9c671489";
        
        return $this->sendMessage($chatId, $message, null, 'Markdown');
    }
    
    protected function handleReply($chatId, $userId, $args)
    {
        try {
            // Get ticket ID from args
            $ticketId = $args[0] ?? null;
            $replyText = implode(' ', array_slice($args, 1));
            
            if (!$ticketId) {
                return $this->sendMessage($chatId, "PERSIAN_TEXT_3487412c");
            }
            
            // If reply text is provided directly, process it
            if (!empty($replyText)) {
                return $this->processReply($chatId, $userId, $replyText, ['ticket_id' => $ticketId]);
            }
            
            // Otherwise, set state to wait for reply
            $ticket = Ticket::where('ticket_number', $ticketId)
                          ->orWhere('id', $ticketId)
                          ->first();
            
            if (!$ticket) {
                return $this->sendMessage($chatId, "a8908e0f");
            }
            
            // Set user state to waiting for reply
            $this->setUserState($userId, self::STATE_WAITING_REPLY, ['ticket_id' => $ticket->id]);
            
            $message = "PERSIAN_TEXT_f48ea152";
            $message .= "d5533675";
            $message .= "PERSIAN_TEXT_37f87463" . ($ticket->user->name ?? 'PERSIAN_TEXT_1789f5ad') . "\n\n";
            $message .= "8e46aaa4";
            
            $keyboard = [
                'inline_keyboard' => [
                    [['text' => 'PERSIAN_TEXT_d7d5cc1e', 'callback_data' => 'cancel_reply']]
                ]
            ];
            
            return $this->sendMessage($chatId, $message, $keyboard, 'Markdown');
            
        } catch (\Exception $e) {
            Log::error('Error in handleReply', ['error' => $e->getMessage()]);
            return $this->sendMessage($chatId, "PERSIAN_TEXT_cdd9727d");
        }
    }
    
    protected function handleCloseTicket($chatId, $userId, $args)
    {
        // Implementation for close ticket
        return $this->sendMessage($chatId, "74b55ea0");
    }
    
    protected function handleReopenTicket($chatId, $userId, $args)
    {
        // Implementation for reopen ticket
        return $this->sendMessage($chatId, "PERSIAN_TEXT_74b55ea0");
    }
    
    protected function handleAssignTicket($chatId, $userId, $args)
    {
        // Implementation for assign ticket
        return $this->sendMessage($chatId, "74b55ea0");
    }
    
    protected function handleSetPriority($chatId, $userId, $args)
    {
        // Implementation for set priority
        return $this->sendMessage($chatId, "PERSIAN_TEXT_74b55ea0");
    }
    
    protected function handleSetCategory($chatId, $userId, $args)
    {
        // Implementation for set category
        return $this->sendMessage($chatId, "74b55ea0");
    }
    
    protected function handleExport($chatId, $userId, $args)
    {
        // Implementation for export
        return $this->sendMessage($chatId, "PERSIAN_TEXT_74b55ea0");
    }
    
    protected function handleNotificationSettings($chatId, $userId, $args)
    {
        // Implementation for notification settings
        return $this->sendMessage($chatId, "74b55ea0");
    }
    
    protected function handleFileUpload($chatId, $userId, $message)
    {
        // Implementation for file upload
        return $this->sendMessage($chatId, "PERSIAN_TEXT_1434d0ba");
    }
    
    protected function handleChannelPost($channelPost)
    {
        // Implementation for channel posts
        Log::info('Channel post received', $channelPost);
    }
    
    protected function processReply($chatId, $userId, $text, $data)
    {
        try {
            // Clear user state first
            $this->clearUserState($userId);
            
            // Get ticket ID from data
            $ticketId = $data['ticket_id'] ?? null;
            
            if (!$ticketId) {
                return $this->sendMessage($chatId, "8b253e2d");
            }
            
            // Find the ticket
            $ticket = Ticket::find($ticketId);
            
            if (!$ticket) {
                return $this->sendMessage($chatId, "PERSIAN_TEXT_9bfa85be");
            }
            
            // Get the user who is replying
            $replyUser = User::where('telegram_chat_id', $userId)->first();
            
            if (!$replyUser) {
                // If user not found by telegram_chat_id, try to find admin
                if (in_array($userId, $this->adminChatIds)) {
                    // Create or find admin user by email (unique identifier)
                    $adminEmail = 'admin_telegram_' . $userId . '@system.local';
                    
                    // First try to find existing admin user
                    $replyUser = User::where('email', $adminEmail)->first();
                    
                    if (!$replyUser) {
                        // Create new admin user if not exists
                        $replyUser = User::create([
                            'email' => $adminEmail,
                            'name' => 'PERSIAN_TEXT_49a5689b',
                            'password' => bcrypt(Str::random(32)),
                            'telegram_chat_id' => $userId,
                            'is_admin' => true
                        ]);
                        
                        Log::info('Created new admin user for Telegram', [
                            'user_id' => $replyUser->id,
                            'telegram_chat_id' => $userId,
                            'email' => $adminEmail
                        ]);
                    }
                    
                    // Update telegram_chat_id if needed
                    if ($replyUser->telegram_chat_id != $userId) {
                        $replyUser->telegram_chat_id = $userId;
                        $replyUser->save();
                    }
                } else {
                    return $this->sendMessage($chatId, "181f3601");
                }
            }
            
            // Ensure we have a valid user ID
            if (!$replyUser || !$replyUser->id) {
                Log::error('Failed to get valid user for ticket reply', [
                    'telegram_chat_id' => $userId,
                    'is_admin' => in_array($userId, $this->adminChatIds)
                ]);
                return $this->sendMessage($chatId, "PERSIAN_TEXT_608ab1b8");
            }
            
            // Log before creating message for debugging
            Log::info('Creating ticket message', [
                'ticket_id' => $ticket->id,
                'user_id' => $replyUser->id,
                'telegram_chat_id' => $userId,
                'user_email' => $replyUser->email,
                'message_preview' => substr($text, 0, 50)
            ]);
            
            // Create the reply/message
            $message = TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => $replyUser->id,
                'message' => $text,
                'is_internal' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Update ticket status if it was closed
            if ($ticket->status === 'closed') {
                $ticket->status = 'open';
                $ticket->status_id = DB::table('ticket_statuses')->where('slug', 'open')->value('id');
                $ticket->save();
            }
            
            // Update first response time if this is the first admin response
            if (!$ticket->first_response_at && $replyUser->is_admin) {
                $ticket->first_response_at = now();
                $ticket->save();
            }
            
            // Send confirmation to admin
            $confirmMessage = "6cf6ae9f";
            $confirmMessage .= "PERSIAN_TEXT_e217bc6f";
            $confirmMessage .= "d5533675";
            $confirmMessage .= "PERSIAN_TEXT_4603bc93";
            $confirmMessage .= "2b4414aa" . \Verta::now()->format('Y/m/d H:i:s');
            
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'PERSIAN_TEXT_81bf76f7', 'callback_data' => "view_{$ticket->id}"PERSIAN_TEXT_a1ff8161"messages_{$ticket->id}"0f22f5c9"close_{$ticket->id}"PERSIAN_TEXT_5e927982"tickets_open_1"]
                    ]
                ]
            ];
            
            $this->sendMessage($chatId, $confirmMessage, $keyboard, 'Markdown');
            
            // Notify the ticket owner if they have Telegram
            if ($ticket->user && $ticket->user->telegram_chat_id && $ticket->user->telegram_chat_id != $userId) {
                $ownerMessage = "d8f908a4";
                $ownerMessage .= "PERSIAN_TEXT_0075842c";
                $ownerMessage .= "d5533675";
                $ownerMessage .= "PERSIAN_TEXT_55f5067c";
                $ownerMessage .= "d4cde5b4";
                $ownerMessage .= "PERSIAN_TEXT_bfe8ebc1" . \Verta::now()->format('Y/m/d H:i:s');
                
                $this->sendMessage($ticket->user->telegram_chat_id, $ownerMessage, null, 'Markdown');
            }
            
            // Log the activity
            Log::info('Ticket reply added via Telegram', [
                'ticket_id' => $ticket->id,
                'user_id' => $replyUser->id,
                'telegram_user_id' => $userId
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Error processing ticket reply', [
                'error' => $e->getMessage(),
                'ticket_id' => $data['ticket_id'] ?? null,
                'user_id' => $userId
            ]);
            
            $this->clearUserState($userId);
            return $this->sendMessage($chatId, "d08ab2a4" . $e->getMessage());
        }
    }
    
    protected function showTicketMessages($chatId, $ticketId)
    {
        try {
            $ticket = Ticket::with(['messages.user'])->find($ticketId);
            
            if (!$ticket) {
                return $this->sendMessage($chatId, "PERSIAN_TEXT_98fe8a54");
            }
            
            $message = "aed5ffb7";
            
            // Show original ticket message
            $message .= "PERSIAN_TEXT_1a738a21";
            $message .= $ticket->description . "\n";
            $message .= "_" . $this->formatDate($ticket->created_at) . "_\n\n";
            
            // Show replies
            if ($ticket->messages && $ticket->messages->count() > 0) {
                $message .= "402b4244";
                foreach ($ticket->messages as $msg) {
                    $userName = $msg->user->name ?? 'PERSIAN_TEXT_1789f5ad';
                    $message .= "👤 *{$userName}:*\n";
                    $message .= $msg->message . "\n";
                    $message .= "_" . $this->formatDate($msg->created_at) . "_\n\n";
                }
            } else {
                $message .= "a673779a";
            }
            
            $keyboard = [
                'inline_keyboard' => [
                    [['text' => 'PERSIAN_TEXT_76509c01', 'callback_data' => "reply_{$ticketId}"PERSIAN_TEXT_10fc3a95"view_{$ticketId}"]]
                ]
            ];
            
            return $this->sendMessage($chatId, $message, $keyboard, 'Markdown');
            
        } catch (\Exception $e) {
            Log::error('Error showing messages', ['error' => $e->getMessage()]);
            return $this->sendMessage($chatId, "0c606654");
        }
    }
    
    /**
     * Send notification for new ticket to admins
     */
    public function notifyNewTicket($ticket)
    {
        try {
            // Get all admin chat IDs
            $adminChatIds = $this->adminChatIds;
            
            // Also get admins from database
            $adminUsers = User::where('telegram_notifications_enabled', true)
                            ->whereNotNull('telegram_chat_id')
                            ->where(function($q) {
                                $q->where('is_admin', true);
                                if (method_exists(User::class, 'whereHasRole')) {
                                    $q->orWhereHasRole(['admin', 'support']);
                                }
                            })
                            ->pluck('telegram_chat_id')
                            ->toArray();
            
            $allAdminIds = array_unique(array_merge($adminChatIds, $adminUsers));
            
            foreach ($allAdminIds as $adminChatId) {
                if (empty($adminChatId)) continue;
                
                $message = "PERSIAN_TEXT_50810e79";
                $message .= "ab0aeb28";
                $message .= "PERSIAN_TEXT_37f87463" . ($ticket->user->name ?? 'PERSIAN_TEXT_1789f5ad') . "\n";
                $message .= "83a276f8" . ($ticket->user->email ?? '-') . "\n";
                $message .= "d5533675";
                $message .= "PERSIAN_TEXT_777d08a7" . $this->getCategoryName($ticket) . "\n";
                $message .= $this->getPriorityEmoji($ticket->priority ?? 'normal') . "b2a09fcb" . $this->getPriorityName($ticket) . "\n";
                $message .= "bfe8ebc1" . $this->formatDate($ticket->created_at) . "\n\n";
                $message .= "f8119f03" . mb_substr($ticket->description, 0, 300);
                
                if (mb_strlen($ticket->description) > 300) {
                    $message .= "PERSIAN_TEXT_04c3c0d3";
                }
                
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'PERSIAN_TEXT_a8df3df4', 'callback_data' => "view_{$ticket->id}"68fd0751"reply_{$ticket->id}"PERSIAN_TEXT_2b48f1a2"assign_{$ticket->id}_me"e99ab5f5"close_{$ticket->id}"PERSIAN_TEXT_f7fcc31d"priority_{$ticket->id}"36e4bc1d"category_{$ticket->id}"]
                        ]
                    ]
                ];
                
                $this->sendMessage($adminChatId, $message, $keyboard, 'Markdown');
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to notify new ticket', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    protected function assignTicketToMe($chatId, $userId, $ticketId)
    {
        try {
            $ticket = Ticket::find($ticketId);
            if (!$ticket) {
                return $this->sendMessage($chatId, "PERSIAN_TEXT_98fe8a54");
            }
            
            // Find admin user
            $adminUser = User::where('telegram_chat_id', $userId)->first();
            
            if (!$adminUser) {
                // Create admin user if not exists
                $adminUser = User::firstOrCreate(
                    ['email' => 'admin_telegram_' . $userId . '@system.local'],
                    [
                        'name' => 'Admin (Telegram)',
                        'password' => bcrypt(Str::random(32)),
                        'telegram_chat_id' => $userId,
                        'is_admin' => true
                    ]
                );
            }
            
            $ticket->assigned_to = $adminUser->id;
            $ticket->save();
            
            $message = "e80d9a2a";
            $message .= "PERSIAN_TEXT_d52dbc65";
            $message .= "3099860c";
            $message .= "PERSIAN_TEXT_e73fff0c";
            
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'PERSIAN_TEXT_5aaba3de', 'callback_data' => "view_{$ticket->id}"7a461837"reply_{$ticket->id}"]
                    ]
                ]
            ];
            
            return $this->sendMessage($chatId, $message, $keyboard, 'Markdown');
            
        } catch (\Exception $e) {
            Log::error('Error assigning ticket', ['error' => $e->getMessage()]);
            return $this->sendMessage($chatId, "PERSIAN_TEXT_7842600c");
        }
    }
    
    protected function showPriorityOptions($chatId, $ticketId)
    {
        $ticket = Ticket::find($ticketId);
        if (!$ticket) {
            return $this->sendMessage($chatId, "98fe8a54");
        }
        
        $message = "PERSIAN_TEXT_6f970956";
        $message .= "f28890b5" . $this->getPriorityName($ticket);
        
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'PERSIAN_TEXT_407cc435', 'callback_data' => "setpriority_{$ticketId}_low"PERSIAN_TEXT_2f4c2877"setpriority_{$ticketId}_normal"15b17332"setpriority_{$ticketId}_high"PERSIAN_TEXT_7a8ef9d7"setpriority_{$ticketId}_urgent"7415a6a6"view_{$ticketId}"]
                ]
            ]
        ];
        
        return $this->sendMessage($chatId, $message, $keyboard, 'Markdown');
    }
    
    protected function showCategoryOptions($chatId, $ticketId)
    {
        $ticket = Ticket::find($ticketId);
        if (!$ticket) {
            return $this->sendMessage($chatId, "PERSIAN_TEXT_98fe8a54");
        }
        
        $message = "55f83abc";
        $message .= "PERSIAN_TEXT_071946e1" . $this->getCategoryName($ticket);
        
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'PERSIAN_TEXT_52045493', 'callback_data' => "setcategory_{$ticketId}_general"7cb0c61c"setcategory_{$ticketId}_technical"PERSIAN_TEXT_2357310e"setcategory_{$ticketId}_billing"d31bc55b"setcategory_{$ticketId}_suggestion"PERSIAN_TEXT_4de41e4a"setcategory_{$ticketId}_complaint"7415a6a6"view_{$ticketId}"]
                ]
            ]
        ];
        
        return $this->sendMessage($chatId, $message, $keyboard, 'Markdown');
    }
    
    protected function resolveTicket($chatId, $userId, $ticketId)
    {
        try {
            $ticket = Ticket::find($ticketId);
            if (!$ticket) {
                return $this->sendMessage($chatId, "PERSIAN_TEXT_98fe8a54");
            }
            
            $ticket->status = 'resolved';
            $ticket->status_id = DB::table('ticket_statuses')->where('slug', 'resolved')->value('id');
            $ticket->resolved_at = now();
            $ticket->save();
            
            $message = "9c37af1f";
            $message .= "PERSIAN_TEXT_d52dbc65";
            $message .= "3099860c";
            $message .= "PERSIAN_TEXT_5b510d6b";
            $message .= "f98fd3c5" . \Verta::now()->format('Y/m/d H:i:s');
            
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'PERSIAN_TEXT_5aaba3de', 'callback_data' => "view_{$ticket->id}"PERSIAN_TEXT_a2573392"close_{$ticket->id}"]
                    ]
                ]
            ];
            
            return $this->sendMessage($chatId, $message, $keyboard, 'Markdown');
            
        } catch (\Exception $e) {
            Log::error('Error resolving ticket', ['error' => $e->getMessage()]);
            return $this->sendMessage($chatId, "ef99bf00");
        }
    }
    
    /**
     * Send notification for ticket reply to admins
     */
    public function notifyTicketReply($ticket, $reply)
    {
        try {
            // Get assigned admin if exists
            $notifyUsers = [];
            
            if ($ticket->assigned_to) {
                $assignedUser = User::find($ticket->assigned_to);
                if ($assignedUser && $assignedUser->telegram_chat_id) {
                    $notifyUsers[] = $assignedUser->telegram_chat_id;
                }
            }
            
            // Also notify all admins for important tickets
            if (in_array($ticket->priority, ['high', 'urgent'])) {
                $notifyUsers = array_merge($notifyUsers, $this->adminChatIds);
            }
            
            $notifyUsers = array_unique($notifyUsers);
            
            foreach ($notifyUsers as $chatId) {
                if (empty($chatId)) continue;
                
                $message = "PERSIAN_TEXT_0b4a6145";
                $message .= "ab0aeb28";
                $message .= "PERSIAN_TEXT_91a12018" . ($reply->user->name ?? 'PERSIAN_TEXT_883da9f0') . "\n";
                $message .= "b1847eb9";
                $message .= "PERSIAN_TEXT_48bc15b8" . $this->formatDate($reply->created_at) . "\n\n";
                $message .= "3850a9b2" . mb_substr($reply->message, 0, 300);
                
                if (mb_strlen($reply->message) > 300) {
                    $message .= "PERSIAN_TEXT_04c3c0d3";
                }
                
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'PERSIAN_TEXT_5aaba3de', 'callback_data' => "view_{$ticket->id}"93c9fc24"reply_{$ticket->id}"PERSIAN_TEXT_3b0b17d5"resolve_{$ticket->id}"e99ab5f5"close_{$ticket->id}"]
                        ]
                    ]
                ];
                
                $this->sendMessage($chatId, $message, $keyboard, 'Markdown');
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to notify ticket reply', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}