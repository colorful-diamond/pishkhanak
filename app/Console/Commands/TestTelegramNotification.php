<?php

namespace App\Console\Commands;

use App\Models\GatewayTransaction;
use App\Models\User;
use App\Services\TelegramNotificationService;
use App\Jobs\SendTelegramNotificationJob;
use Illuminate\Console\Command;

class TestTelegramNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:test 
                            {--type=connection : Type of test (connection|notification|job)}
                            {--transaction= : Transaction ID for notification test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Telegram bot connection and notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');

        switch ($type) {
            case 'connection':
                $this->testConnection();
                break;
            
            case 'notification':
                $this->testNotification();
                break;
                
            case 'job':
                $this->testNotificationJob();
                break;
                
            default:
                $this->error('Invalid test type. Use: connection, notification, or job');
        }
    }

    /**
     * Test basic connection to Telegram
     */
    protected function testConnection()
    {
        $this->info('Testing Telegram bot connection...');
        
        try {
            $telegramService = new TelegramNotificationService();
            $result = $telegramService->sendTestNotification();
            
            if ($result) {
                $this->info('✅ Connection successful! Test message sent to channel.');
            } else {
                $this->error('❌ Connection failed! Check logs for details.');
            }
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
    }

    /**
     * Test sending a sample notification
     */
    protected function testNotification()
    {
        $this->info('Testing Telegram notification...');
        
        $transactionId = $this->option('transaction');
        
        if ($transactionId) {
            // Use existing transaction
            $transaction = GatewayTransaction::find($transactionId);
            
            if (!$transaction) {
                $this->error('Transaction not found with ID: ' . $transactionId);
                return;
            }
        } else {
            // Create a mock transaction for testing
            $this->info('Creating mock transaction for testing...');
            
            $transaction = new GatewayTransaction();
            $transaction->id = 999999;
            $transaction->uuid = 'TEST-' . uniqid();
            $transaction->gateway_id = 1;
            $transaction->user_id = User::first()?->id;
            $transaction->type = 'wallet_charge';
            $transaction->amount = 50000;
            $transaction->gateway_fee = 1000;
            $transaction->total_amount = 51000;
            $transaction->status = 'completed';
            $transaction->gateway_reference_id = 'TEST-REF-' . rand(100000, 999999);
            $transaction->created_at = now();
            $transaction->metadata = [
                'service_name' => 'تست ارسال پیام',
                'service_category' => 'آزمایشی',
            ];
            
            // Load relations
            $transaction->setRelation('user', User::first());
            $transaction->setRelation('gateway', (object)['name' => 'درگاه تست']);
        }
        
        try {
            $telegramService = new TelegramNotificationService();
            $result = $telegramService->sendNewOrderNotification($transaction);
            
            if ($result) {
                $this->info('✅ Notification sent successfully!');
            } else {
                $this->error('❌ Failed to send notification. Check logs for details.');
            }
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
        }
    }

    /**
     * Test notification via job queue
     */
    protected function testNotificationJob()
    {
        $this->info('Testing Telegram notification via job queue...');
        
        $transactionId = $this->option('transaction');
        
        if (!$transactionId) {
            $this->error('Please provide a transaction ID with --transaction=ID');
            return;
        }
        
        $transaction = GatewayTransaction::find($transactionId);
        
        if (!$transaction) {
            $this->error('Transaction not found with ID: ' . $transactionId);
            return;
        }
        
        try {
            SendTelegramNotificationJob::dispatch($transaction)->onQueue('notifications');
            $this->info('✅ Job dispatched successfully! Check your queue worker.');
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
    }
}