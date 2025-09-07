<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\TicketPriority;
use App\Models\TicketCategory;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class TelegramBotDebugger
{
    protected $errors = [];
    protected $warnings = [];
    protected $info = [];
    protected $bot;
    
    public function __construct()
    {
        $this->bot = new TelegramTicketBot();
    }
    
    /**
     * Run complete diagnostic
     */
    public function runCompleteDiagnostic()
    {
        $this->info[] = "Starting Telegram Bot Complete Diagnostic";
        $this->info[] = "==========================================";
        
        // 1. Check database structure
        $this->checkDatabaseStructure();
        
        // 2. Check model relationships
        $this->checkModelRelationships();
        
        // 3. Check bot configuration
        $this->checkBotConfiguration();
        
        // 4. Test all commands
        $this->testAllCommands();
        
        // 5. Test callback queries
        $this->testCallbackQueries();
        
        // 6. Test state management
        $this->testStateManagement();
        
        // 7. Generate report
        return $this->generateReport();
    }
    
    /**
     * Check database structure
     */
    protected function checkDatabaseStructure()
    {
        $this->info[] = "\n=== DATABASE STRUCTURE CHECK ===";
        
        $requiredTables = [
            'tickets' => ['id', 'ticket_number', 'user_id', 'subject', 'status', 'priority', 'category'],
            'ticket_statuses' => ['id', 'name', 'slug', 'color'],
            'ticket_priorities' => ['id', 'name', 'slug', 'color', 'order'],
            'ticket_categories' => ['id', 'name', 'slug', 'description'],
            'ticket_messages' => ['id', 'ticket_id', 'user_id', 'message'],
            'ticket_attachments' => ['id', 'ticket_id', 'file_name', 'file_path'],
            'users' => ['id', 'name', 'email', 'telegram_chat_id']
        ];
        
        foreach ($requiredTables as $table => $columns) {
            if (Schema::hasTable($table)) {
                $this->info[] = "✅ Table '$table' exists";
                
                $existingColumns = Schema::getColumnListing($table);
                foreach ($columns as $column) {
                    if (!in_array($column, $existingColumns)) {
                        $this->warnings[] = "⚠️ Table '$table' missing column '$column'";
                    }
                }
            } else {
                $this->errors[] = "❌ Table '$table' does not exist";
            }
        }
        
        // Check for data in critical tables
        if (Schema::hasTable('ticket_statuses')) {
            $statusCount = DB::table('ticket_statuses')->count();
            if ($statusCount == 0) {
                $this->warnings[] = "⚠️ No ticket statuses defined - will create defaults";
                $this->createDefaultStatuses();
            } else {
                $this->info[] = "✅ Found $statusCount ticket statuses";
            }
        }
        
        if (Schema::hasTable('ticket_priorities')) {
            $priorityCount = DB::table('ticket_priorities')->count();
            if ($priorityCount == 0) {
                $this->warnings[] = "⚠️ No ticket priorities defined - will create defaults";
                $this->createDefaultPriorities();
            } else {
                $this->info[] = "✅ Found $priorityCount ticket priorities";
            }
        }
        
        if (Schema::hasTable('ticket_categories')) {
            $categoryCount = DB::table('ticket_categories')->count();
            if ($categoryCount == 0) {
                $this->warnings[] = "⚠️ No ticket categories defined - will create defaults";
                $this->createDefaultCategories();
            } else {
                $this->info[] = "✅ Found $categoryCount ticket categories"54f31009"✅ Created default ticket statuses"PERSIAN_TEXT_a78da6b1"✅ Created default ticket priorities"15dfd2c5"✅ Created default ticket categories";
    }
    
    /**
     * Check model relationships
     */
    protected function checkModelRelationships()
    {
        $this->info[] = "\n=== MODEL RELATIONSHIPS CHECK ===";
        
        try {
            // Test Ticket model
            $ticket = Ticket::first();
            if ($ticket) {
                // Check if we can access user
                if ($ticket->user) {
                    $this->info[] = "✅ Ticket->user relationship works";
                } else {
                    $this->warnings[] = "⚠️ Ticket->user relationship returns null";
                }
                
                // Check messages
                try {
                    $messages = $ticket->messages;
                    $this->info[] = "✅ Ticket->messages relationship works";
                } catch (\Exception $e) {
                    $this->errors[] = "❌ Ticket->messages failed: " . $e->getMessage();
                }
                
                // Check attachments
                try {
                    $attachments = $ticket->attachments;
                    $this->info[] = "✅ Ticket->attachments relationship works";
                } catch (\Exception $e) {
                    $this->errors[] = "❌ Ticket->attachments failed: " . $e->getMessage();
                }
            } else {
                $this->warnings[] = "⚠️ No tickets found for relationship testing";
            }
            
        } catch (\Exception $e) {
            $this->errors[] = "❌ Model relationship check failed: " . $e->getMessage();
        }
    }
    
    /**
     * Check bot configuration
     */
    protected function checkBotConfiguration()
    {
        $this->info[] = "\n=== BOT CONFIGURATION CHECK ===";
        
        // Check bot token
        $botToken = config('services.telegram.bot_token');
        if ($botToken) {
            $this->info[] = "✅ Bot token configured";
        } else {
            $this->errors[] = "❌ Bot token not configured";
        }
        
        // Check admin chat IDs
        $adminIds = config('services.telegram.admin_chat_ids');
        if ($adminIds) {
            $adminCount = count(explode(',', $adminIds));
            $this->info[] = "✅ Admin chat IDs configured ($adminCount admins)";
        } else {
            $this->errors[] = "❌ No admin chat IDs configured";
        }
        
        // Check proxy configuration
        if (config('services.telegram.external_proxy.enabled')) {
            $this->info[] = "✅ External proxy enabled";
            $proxyUrl = config('services.telegram.external_proxy.url');
            if ($proxyUrl) {
                $this->info[] = "✅ Proxy URL: $proxyUrl";
            } else {
                $this->errors[] = "❌ Proxy enabled but URL not configured";
            }
        }
        
        // Test bot connection
        try {
            $botInfo = $this->testBotConnection();
            if ($botInfo) {
                $this->info[] = "✅ Bot connection successful: @" . ($botInfo['username'] ?? 'unknown');
            }
        } catch (\Exception $e) {
            $this->errors[] = "❌ Bot connection failed: " . $e->getMessage();
        }
    }
    
    /**
     * Test bot connection
     */
    protected function testBotConnection()
    {
        $service = new TelegramNotificationService();
        $result = $service->sendRequest('getMe');
        
        if ($result && isset($result['ok']) && $result['ok']) {
            return $result['result'];
        }
        
        throw new \Exception("Failed to get bot info");
    }
    
    /**
     * Test all commands
     */
    protected function testAllCommands()
    {
        $this->info[] = "\n=== COMMAND TESTS ===";
        
        $testUserId = 808124087; // Your chat ID
        $commands = [
            '/start' => 'handleStart',
            '/help' => 'handleHelp',
            '/tickets' => 'handleListTickets',
            '/stats' => 'handleStatistics',
            '/search test' => 'handleSearch',
        ];
        
        foreach ($commands as $command => $handler) {
            try {
                $update = $this->createFakeUpdate($testUserId, $command);
                
                // Use reflection to test protected methods
                $reflection = new \ReflectionClass($this->bot);
                $method = $reflection->getMethod('handleMessage');
                $method->setAccessible(true);
                
                $method->invoke($this->bot, $update['message']);
                $this->info[] = "✅ Command '$command' processed successfully";
                
            } catch (\Exception $e) {
                $this->errors[] = "❌ Command '$command' failed: " . $e->getMessage();
            }
        }
    }
    
    /**
     * Test callback queries
     */
    protected function testCallbackQueries()
    {
        $this->info[] = "\n=== CALLBACK QUERY TESTS ===";
        
        $testUserId = 808124087;
        $callbacks = [
            'tickets_open_1' => 'List open tickets page 1',
            'tickets_closed_1' => 'List closed tickets page 1',
            'stats_today' => 'Today statistics',
            'stats_all' => 'All time statistics',
            'help' => 'Help menu',
        ];
        
        foreach ($callbacks as $data => $description) {
            try {
                $update = $this->createFakeCallbackQuery($testUserId, $data);
                
                $reflection = new \ReflectionClass($this->bot);
                $method = $reflection->getMethod('handleCallbackQuery');
                $method->setAccessible(true);
                
                $method->invoke($this->bot, $update['callback_query']);
                $this->info[] = "✅ Callback '$data' ($description) processed";
                
            } catch (\Exception $e) {
                $this->warnings[] = "⚠️ Callback '$data' warning: " . $e->getMessage();
            }
        }
    }
    
    /**
     * Test state management
     */
    protected function testStateManagement()
    {
        $this->info[] = "\n=== STATE MANAGEMENT TESTS ===";
        
        $testUserId = 808124087;
        
        try {
            // Test setting state
            $reflection = new \ReflectionClass($this->bot);
            $setStateMethod = $reflection->getMethod('setUserState');
            $setStateMethod->setAccessible(true);
            
            $getStateMethod = $reflection->getMethod('getUserState');
            $getStateMethod->setAccessible(true);
            
            // Set a state
            $setStateMethod->invoke($this->bot, $testUserId, 'waiting_reply', ['ticket_id' => 123]);
            
            // Get the state
            $state = $getStateMethod->invoke($this->bot, $testUserId);
            
            if ($state['state'] === 'waiting_reply' && $state['data']['ticket_id'] === 123) {
                $this->info[] = "✅ State management working correctly";
            } else {
                $this->errors[] = "❌ State management not working properly";
            }
            
            // Clear state
            $clearStateMethod = $reflection->getMethod('clearUserState');
            $clearStateMethod->setAccessible(true);
            $clearStateMethod->invoke($this->bot, $testUserId);
            
            $this->info[] = "✅ State cleared successfully";
            
        } catch (\Exception $e) {
            $this->errors[] = "❌ State management test failed: " . $e->getMessage();
        }
    }
    
    /**
     * Create fake update for testing
     */
    protected function createFakeUpdate($userId, $text)
    {
        return [
            'update_id' => rand(100000, 999999),
            'message' => [
                'message_id' => rand(100, 999),
                'from' => [
                    'id' => $userId,
                    'first_name' => 'Test',
                    'username' => 'testuser'
                ],
                'chat' => [
                    'id' => $userId,
                    'type' => 'private'
                ],
                'text' => $text,
                'date' => time()
            ]
        ];
    }
    
    /**
     * Create fake callback query for testing
     */
    protected function createFakeCallbackQuery($userId, $data)
    {
        return [
            'update_id' => rand(100000, 999999),
            'callback_query' => [
                'id' => (string)rand(1000000000, 9999999999),
                'from' => [
                    'id' => $userId,
                    'first_name' => 'Test',
                    'username' => 'testuser'
                ],
                'data' => $data,
                'chat_instance' => (string)rand(1000000000, 9999999999)
            ]
        ];
    }
    
    /**
     * Generate diagnostic report
     */
    protected function generateReport()
    {
        $report = [
            'timestamp' => now()->toDateTimeString(),
            'summary' => [
                'total_errors' => count($this->errors),
                'total_warnings' => count($this->warnings),
                'total_info' => count($this->info),
                'status' => count($this->errors) === 0 ? 'PASS' : 'FAIL'
            ],
            'errors' => $this->errors,
            'warnings' => $this->warnings,
            'info' => $this->info,
            'recommendations' => $this->generateRecommendations()
        ];
        
        // Save report to file
        $reportPath = storage_path('app/telegram_bot_diagnostic_' . date('Y-m-d_H-i-s') . '.json');
        file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT));
        
        return $report;
    }
    
    /**
     * Generate recommendations based on findings
     */
    protected function generateRecommendations()
    {
        $recommendations = [];
        
        if (count($this->errors) > 0) {
            $recommendations[] = "Critical errors found - immediate attention required";
        }
        
        if (in_array('❌ No ticket statuses defined', $this->errors)) {
            $recommendations[] = "Run migrations to create ticket status/priority/category tables";
        }
        
        if (count($this->warnings) > 0) {
            $recommendations[] = "Review warnings and consider fixes for optimal performance";
        }
        
        return $recommendations;
    }
    
    /**
     * Fix all issues automatically
     */
    public function autoFix()
    {
        $fixes = [];
        
        // Fix 1: Create missing tables
        if (!Schema::hasTable('ticket_statuses')) {
            $this->createStatusTable();
            $fixes[] = "Created ticket_statuses table";
        }
        
        if (!Schema::hasTable('ticket_priorities')) {
            $this->createPriorityTable();
            $fixes[] = "Created ticket_priorities table";
        }
        
        if (!Schema::hasTable('ticket_categories')) {
            $this->createCategoryTable();
            $fixes[] = "Created ticket_categories table";
        }
        
        // Fix 2: Add missing columns
        $this->addMissingColumns();
        
        // Fix 3: Update existing tickets
        $this->updateExistingTickets();
        
        return $fixes;
    }
    
    /**
     * Create status table
     */
    protected function createStatusTable()
    {
        Schema::create('ticket_statuses', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color')->default('gray');
            $table->integer('order')->default(0);
            $table->timestamps();
        });
        
        $this->createDefaultStatuses();
    }
    
    /**
     * Create priority table
     */
    protected function createPriorityTable()
    {
        Schema::create('ticket_priorities', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color')->default('gray');
            $table->integer('order')->default(0);
            $table->timestamps();
        });
        
        $this->createDefaultPriorities();
    }
    
    /**
     * Create category table
     */
    protected function createCategoryTable()
    {
        Schema::create('ticket_categories', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });
        
        $this->createDefaultCategories();
    }
    
    /**
     * Add missing columns to tickets table
     */
    protected function addMissingColumns()
    {
        if (!Schema::hasColumn('tickets', 'status_id')) {
            Schema::table('tickets', function ($table) {
                $table->unsignedBigInteger('status_id')->nullable()->after('status');
                $table->foreign('status_id')->references('id')->on('ticket_statuses');
            });
        }
        
        if (!Schema::hasColumn('tickets', 'priority_id')) {
            Schema::table('tickets', function ($table) {
                $table->unsignedBigInteger('priority_id')->nullable()->after('priority');
                $table->foreign('priority_id')->references('id')->on('ticket_priorities');
            });
        }
        
        if (!Schema::hasColumn('tickets', 'category_id')) {
            Schema::table('tickets', function ($table) {
                $table->unsignedBigInteger('category_id')->nullable()->after('category');
                $table->foreign('category_id')->references('id')->on('ticket_categories');
            });
        }
        
        if (!Schema::hasColumn('tickets', 'closed_by')) {
            Schema::table('tickets', function ($table) {
                $table->unsignedBigInteger('closed_by')->nullable();
                $table->foreign('closed_by')->references('id')->on('users');
            });
        }
    }
    
    /**
     * Update existing tickets with proper IDs
     */
    protected function updateExistingTickets()
    {
        $tickets = Ticket::whereNull('status_id')->orWhereNull('priority_id')->orWhereNull('category_id')->get();
        
        foreach ($tickets as $ticket) {
            // Update status_id
            if (!$ticket->status_id && $ticket->status) {
                $status = DB::table('ticket_statuses')->where('slug', $ticket->status)->first();
                if (!$status) {
                    $status = DB::table('ticket_statuses')->where('slug', 'open')->first();
                }
                if ($status) {
                    $ticket->status_id = $status->id;
                }
            }
            
            // Update priority_id
            if (!$ticket->priority_id && $ticket->priority) {
                $priority = DB::table('ticket_priorities')->where('slug', $ticket->priority)->first();
                if (!$priority) {
                    $priority = DB::table('ticket_priorities')->where('slug', 'normal')->first();
                }
                if ($priority) {
                    $ticket->priority_id = $priority->id;
                }
            }
            
            // Update category_id
            if (!$ticket->category_id && $ticket->category) {
                $category = DB::table('ticket_categories')->where('slug', $ticket->category)->first();
                if (!$category) {
                    $category = DB::table('ticket_categories')->where('slug', 'general')->first();
                }
                if ($category) {
                    $ticket->category_id = $category->id;
                }
            }
            
            $ticket->save();
        }
    }
}