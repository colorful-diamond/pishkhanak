<?php

namespace App\Console\Commands;

use App\Models\User;
use Bavix\Wallet\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestWalletManagement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:test 
                           {--user-id= : User ID to test with}
                           {--amount=1000 : Amount to test with}
                           {--cleanup : Clean up test data after testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test wallet management functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Wallet Management System...');
        $this->newLine();

        // Get or create test user
        $userId = $this->option('user-id');
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found!");
                return 1;
            }
        } else {
            $user = User::first();
            if (!$user) {
                $this->error('No users found in database!');
                return 1;
            }
        }

        $amount = (int) $this->option('amount');

        $this->info("Testing with User: {$user->name} (ID: {$user->id})");
        $this->info("Test Amount: " . number_format($amount) . " Toman");
        $this->newLine();

        // Store initial balance
        $initialBalance = $user->balance;
        $this->info("Initial Balance: " . number_format($initialBalance) . " Toman");

        try {
            DB::beginTransaction();

            // Test 1: Deposit (Charge Wallet)
            $this->info('1️⃣ Testing Wallet Charge...');
            $depositTransaction = $user->deposit($amount, [
                'description' => 'Test admin charge via command',
                'reason_type' => 'admin_charge',
                'admin_user_id' => 1,
                'admin_user_name' => 'System Test',
                'type' => 'admin_charge',
                'performed_at' => now()->toISOString(),
            ]);

            $this->info("   ✅ Deposit successful - Transaction ID: {$depositTransaction->id}");
            $this->info("   💰 New Balance: " . number_format($user->fresh()->balance) . " Toman");

            // Test 2: Normal Withdrawal (Deduct from Wallet)
            $this->info('2️⃣ Testing Normal Wallet Deduction...');
            $withdrawAmount = min($amount / 2, $user->fresh()->balance);
            
            if ($withdrawAmount > 0) {
                $withdrawTransaction = $user->withdraw($withdrawAmount, [
                    'description' => 'Test admin deduction via command',
                    'reason_type' => 'admin_deduction',
                    'admin_user_id' => 1,
                    'admin_user_name' => 'System Test',
                    'type' => 'admin_deduction',
                    'performed_at' => now()->toISOString(),
                ]);

                $this->info("   ✅ Withdrawal successful - Transaction ID: {$withdrawTransaction->id}");
                $this->info("   💰 New Balance: " . number_format($user->fresh()->balance) . " Toman");
            } else {
                $this->warn("   ⚠️ Skipping withdrawal test - insufficient balance");
            }

            // Test 3: Force Withdrawal
            $this->info('3️⃣ Testing Force Deduction...');
            $forceAmount = 100;
            $forceTransaction = $user->forceWithdraw($forceAmount, [
                'description' => 'Test force deduction via command',
                'reason_type' => 'admin_deduction',
                'admin_user_id' => 1,
                'admin_user_name' => 'System Test',
                'type' => 'admin_deduction',
                'force_deduction' => true,
                'performed_at' => now()->toISOString(),
            ]);

            $this->info("   ✅ Force withdrawal successful - Transaction ID: {$forceTransaction->id}");
            $this->info("   💰 New Balance: " . number_format($user->fresh()->balance) . " Toman");

            // Test 4: Transaction History
            $this->info('4️⃣ Testing Transaction History...');
            $transactions = $user->transactions()
                ->where('created_at', '>=', now()->subMinute())
                ->orderBy('created_at', 'desc')
                ->get();

            $this->info("   📋 Found {$transactions->count()} recent transactions:");
            foreach ($transactions as $transaction) {
                $type = $transaction->type === 'deposit' ? '📈' : '📉';
                $confirmed = $transaction->confirmed ? '✅' : '⏳';
                $description = isset($transaction->meta['description']) ? $transaction->meta['description'] : 'No description';
                $this->info("      {$type} {$confirmed} " . number_format($transaction->amount) . " Toman - {$description}");
            }

            // Show final balance
            $finalBalance = $user->fresh()->balance;
            $this->newLine();
            $this->info("📊 FINAL RESULTS:");
            $this->info("   Initial Balance: " . number_format($initialBalance) . " Toman");
            $this->info("   Final Balance: " . number_format($finalBalance) . " Toman");
            $this->info("   Net Change: " . number_format($finalBalance - $initialBalance) . " Toman");

            // Cleanup option
            if ($this->option('cleanup')) {
                $this->info('🧹 Cleaning up test transactions...');
                
                // Delete test transactions
                $deletedCount = Transaction::where('payable_id', $user->id)
                    ->where('created_at', '>=', now()->subMinute())
                    ->whereJsonContains('meta->admin_user_name', 'System Test')
                    ->delete();

                // Restore original balance
                $user->wallet->update(['balance' => $initialBalance]);
                
                $this->info("   ✅ Deleted {$deletedCount} test transactions");
                $this->info("   ✅ Restored original balance: " . number_format($initialBalance) . " Toman");
            }

            if ($this->option('cleanup')) {
                DB::commit();
            } else {
                DB::rollBack();
                $this->warn('🔄 Changes rolled back (use --cleanup to persist changes)');
            }

            $this->newLine();
            $this->info('✅ All wallet management tests completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Test failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }

    /**
     * Test wallet statistics
     */
    private function testStatistics()
    {
        $this->info('📊 Testing Wallet Statistics...');
        
        $totalUsers = User::whereHas('wallet')->count();
        $totalBalance = \Bavix\Wallet\Models\Wallet::where('holder_type', User::class)->sum('balance') ?? 0;
        $avgBalance = $totalUsers > 0 ? ($totalBalance / $totalUsers) : 0;
        $recentTransactions = Transaction::where('created_at', '>=', now()->subDay())->count();
        $pendingTransactions = Transaction::where('confirmed', false)->count();

        $this->info("   👥 Users with wallets: " . number_format($totalUsers));
        $this->info("   💰 Total balance: " . number_format($totalBalance) . " Toman");
        $this->info("   📊 Average balance: " . number_format($avgBalance) . " Toman");
        $this->info("   🕐 Recent transactions (24h): " . number_format($recentTransactions));
        $this->info("   ⏳ Pending transactions: " . number_format($pendingTransactions));
    }
} 