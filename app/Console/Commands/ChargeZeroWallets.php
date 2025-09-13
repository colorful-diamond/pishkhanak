<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Bavix\Wallet\Models\Wallet;

class ChargeZeroWallets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:charge-zero 
                            {--amount=100000 : Amount to charge in Toman (default: 100,000 Toman)}
                            {--dry-run : Show what would be charged without actually charging}
                            {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Charge wallets that have zero balance or no wallet at all with a specified amount in Toman';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $amount = (int) $this->option('amount');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        // Validate amount
        if ($amount <= 0) {
            $this->error('Amount must be greater than 0');
            return Command::FAILURE;
        }

        // Find users with zero balance wallets
        $usersWithZeroBalance = User::whereHas('wallet', function ($query) {
                $query->where('balance', 0);
            })
            ->with(['wallet' => function ($query) {
                $query->where('balance', 0);
            }])
            ->get();

        // Find users without any wallet
        $usersWithoutWallet = User::whereDoesntHave('wallet')->get();

        // Combine both collections
        $allUsersToCharge = $usersWithZeroBalance->merge($usersWithoutWallet);

        if ($allUsersToCharge->isEmpty()) {
            $this->info('No users with zero balance or missing wallets found.');
            return Command::SUCCESS;
        }

        // Display summary
        $this->info("Found {$usersWithZeroBalance->count()} users with zero balance wallets");
        $this->info("Found {$usersWithoutWallet->count()} users without any wallet");
        $this->info("Total users to charge: {$allUsersToCharge->count()}");
        $this->info("Amount to charge: " . number_format($amount) . " Toman");
        $this->info("Total amount: " . number_format($amount * $allUsersToCharge->count()) . " Toman");

        // Show users list
        $this->table(
            ['User ID', 'Name', 'Mobile', 'Wallet Status', 'Current Balance', 'New Balance'],
            $allUsersToCharge->map(function ($user) use ($amount, $usersWithZeroBalance, $usersWithoutWallet) {
                // Check which collection this user belongs to
                $isInZeroBalance = $usersWithZeroBalance->contains('id', $user->id);
                $isInNoWallet = $usersWithoutWallet->contains('id', $user->id);
                
                if ($isInZeroBalance) {
                    $walletStatus = 'Has wallet (zero balance)';
                    $currentBalance = 0;
                } else {
                    $walletStatus = 'No wallet (will create)';
                    $currentBalance = 0;
                }
                
                return [
                    $user->id,
                    $user->name ?? 'N/A',
                    $user->mobile ?? 'N/A',
                    $walletStatus,
                    number_format($currentBalance),
                    number_format($amount),
                ];
            })->toArray()
        );

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No wallets were actually charged');
            return Command::SUCCESS;
        }

        // Confirmation
        if (!$force && !$this->confirm('Do you want to proceed with charging these wallets?')) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        // Perform charging
        $successCount = 0;
        $failureCount = 0;

        $this->info('Starting wallet charging...');
        $progressBar = $this->output->createProgressBar($allUsersToCharge->count());
        $progressBar->start();

        foreach ($allUsersToCharge as $user) {
            try {
                // Check if user has a wallet
                if ($user->wallet) {
                    // Double-check balance before charging for existing wallets
                    $user->wallet->refresh();
                    if ($user->wallet->balance > 0) {
                        $this->newLine();
                        $this->warn("Skipping user {$user->id} - balance is no longer zero ({$user->wallet->balance})");
                        $progressBar->advance();
                        continue;
                    }
                }

                // Charge the wallet using Bavix wallet
                $user->deposit($amount, [
                    'description' => 'شارژ کیف پول به علت خطای فنی در پایگاه داده',
                    'meta' => [
                        'command' => 'wallet:charge-zero',
                        'amount' => $amount,
                        'charged_at' => now()->toISOString(),
                        'admin_action' => true,
                        'reason' => 'technical_database_error',
                        'type' => 'compensatory_charge'
                    ]
                ]);

                $successCount++;

                // Log the action
                $walletStatus = $user->wallet ? 'zero_balance' : 'no_wallet_created';
                Log::info('Wallet charged', [
                    'user_id' => $user->id,
                    'wallet_id' => $user->wallet->id ?? 'newly_created',
                    'amount' => $amount,
                    'command' => 'wallet:charge-zero',
                    'previous_balance' => 0,
                    'new_balance' => $amount,
                    'wallet_status' => $walletStatus,
                ]);

            } catch (\Exception $e) {
                $failureCount++;
                $this->newLine();
                $this->error("Failed to charge wallet for user {$user->id}: " . $e->getMessage());
                
                Log::error('Failed to charge wallet', [
                    'user_id' => $user->id,
                    'wallet_id' => $user->wallet->id ?? null,
                    'amount' => $amount,
                    'error' => $e->getMessage(),
                    'command' => 'wallet:charge-zero',
                    'wallet_status' => $user->wallet ? 'had_wallet' : 'no_wallet',
                ]);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info("Charging completed:");
        $this->info("✅ Successfully charged: {$successCount} wallets");
        if ($failureCount > 0) {
            $this->error("❌ Failed to charge: {$failureCount} wallets");
        }
        $this->info("💰 Total amount charged: " . number_format($amount * $successCount) . " Toman");

        return $successCount > 0 ? Command::SUCCESS : Command::FAILURE;
    }
}
