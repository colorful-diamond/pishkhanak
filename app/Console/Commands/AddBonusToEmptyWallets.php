<?php

namespace App\Console\Commands;

use App\Models\User;
use Bavix\Wallet\Models\Wallet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddBonusToEmptyWallets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:add-bonus-empty 
                           {--amount=100000 : Amount to add to empty wallets}
                           {--dry-run : Show what would be done without making changes}
                           {--force : Force operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add bonus amount to all empty wallets (balance = 0)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $amount = (int) $this->option('amount');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('💰 Adding Bonus to Empty Wallets...');
        $this->newLine();

        // Find users with empty wallets (balance = 0)
        $usersWithEmptyWallets = User::whereHas('wallet', function($query) {
            $query->where('balance', 0);
        })->with('wallet')->get();

        // Find users without wallets
        $usersWithoutWallets = User::whereDoesntHave('wallet')->get();

        $this->info("📊 Current Status:");
        $this->info("   Users with empty wallets (balance = 0): {$usersWithEmptyWallets->count()}");
        $this->info("   Users without wallets: {$usersWithoutWallets->count()}");
        $this->info("   Total users to receive bonus: " . ($usersWithEmptyWallets->count() + $usersWithoutWallets->count()));
        $this->info("   Bonus amount per user: " . number_format($amount) . " Toman");
        $this->info("   Total bonus to distribute: " . number_format($amount * ($usersWithEmptyWallets->count() + $usersWithoutWallets->count())) . " Toman");
        $this->newLine();

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
            $this->showAffectedUsers($usersWithEmptyWallets, $usersWithoutWallets);
            return 0;
        }

        // Confirm action unless forced
        if (!$force) {
            if (!$this->confirm('Do you want to proceed with adding the bonus to all empty wallets?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        try {
            DB::beginTransaction();

            $processedCount = 0;
            $errors = [];

            // Process users with empty wallets
            $this->info('Processing users with empty wallets...');
            $this->output->progressStart($usersWithEmptyWallets->count());

            foreach ($usersWithEmptyWallets as $user) {
                try {
                    $user->deposit($amount, [
                        'description' => 'Empty wallet bonus distribution',
                        'reason_type' => 'admin_bonus',
                        'admin_user_id' => 1,
                        'admin_user_name' => 'System Admin',
                        'type' => 'bonus',
                        'bonus_type' => 'empty_wallet_bonus',
                        'performed_at' => now()->toISOString(),
                    ]);
                    
                    $processedCount++;
                    $this->output->progressAdvance();
                } catch (\Exception $e) {
                    $errors[] = "User {$user->id} ({$user->name}): " . $e->getMessage();
                    $this->output->progressAdvance();
                }
            }
            $this->output->progressFinish();

            // Process users without wallets
            if ($usersWithoutWallets->count() > 0) {
                $this->info('Processing users without wallets...');
                $this->output->progressStart($usersWithoutWallets->count());

                foreach ($usersWithoutWallets as $user) {
                    try {
                        // This will automatically create a wallet and add the deposit
                        $user->deposit($amount, [
                            'description' => 'New wallet bonus distribution',
                            'reason_type' => 'admin_bonus',
                            'admin_user_id' => 1,
                            'admin_user_name' => 'System Admin',
                            'type' => 'bonus',
                            'bonus_type' => 'new_wallet_bonus',
                            'performed_at' => now()->toISOString(),
                        ]);
                        
                        $processedCount++;
                        $this->output->progressAdvance();
                    } catch (\Exception $e) {
                        $errors[] = "User {$user->id} ({$user->name}): " . $e->getMessage();
                        $this->output->progressAdvance();
                    }
                }
                $this->output->progressFinish();
            }

            DB::commit();

            $this->newLine();
            $this->info('✅ Bonus distribution completed successfully!');
            $this->info("   Processed users: {$processedCount}");
            $this->info("   Total amount distributed: " . number_format($amount * $processedCount) . " Toman");

            if (!empty($errors)) {
                $this->newLine();
                $this->warn('⚠️ Some errors occurred:');
                foreach ($errors as $error) {
                    $this->warn("   - {$error}");
                }
            }

            // Show final statistics
            $this->showFinalStatistics();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Operation failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Show affected users in dry run mode
     */
    private function showAffectedUsers($usersWithEmptyWallets, $usersWithoutWallets)
    {
        if ($usersWithEmptyWallets->count() > 0) {
            $this->info('👥 Users with empty wallets that would receive bonus:');
            foreach ($usersWithEmptyWallets->take(10) as $user) {
                $this->info("   - {$user->name} (ID: {$user->id}, Email: {$user->email})");
            }
            if ($usersWithEmptyWallets->count() > 10) {
                $this->info("   ... and " . ($usersWithEmptyWallets->count() - 10) . " more users");
            }
            $this->newLine();
        }

        if ($usersWithoutWallets->count() > 0) {
            $this->info('👥 Users without wallets that would receive bonus:');
            foreach ($usersWithoutWallets->take(10) as $user) {
                $this->info("   - {$user->name} (ID: {$user->id}, Email: {$user->email})");
            }
            if ($usersWithoutWallets->count() > 10) {
                $this->info("   ... and " . ($usersWithoutWallets->count() - 10) . " more users");
            }
        }
    }

    /**
     * Show final statistics after operation
     */
    private function showFinalStatistics()
    {
        $this->newLine();
        $this->info('📊 Final Statistics:');
        
        $totalUsers = User::count();
        $usersWithWallets = User::whereHas('wallet')->count();
        $usersWithEmptyWallets = User::whereHas('wallet', function($query) {
            $query->where('balance', 0);
        })->count();
        $usersWithPositiveBalance = User::whereHas('wallet', function($query) {
            $query->where('balance', '>', 0);
        })->count();
        $totalBalance = Wallet::where('holder_type', User::class)->sum('balance') ?? 0;

        $this->info("   Total users: {$totalUsers}");
        $this->info("   Users with wallets: {$usersWithWallets}");
        $this->info("   Users with empty wallets: {$usersWithEmptyWallets}");
        $this->info("   Users with positive balance: {$usersWithPositiveBalance}");
        $this->info("   Total wallet balance: " . number_format($totalBalance) . " Toman");
    }
}