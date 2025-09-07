<?php

namespace App\Console\Commands;

use App\Models\User;
use Bavix\Wallet\Models\Transaction;
use Illuminate\Console\Command;

class CheckWalletState extends Command
{
    protected $signature = 'debug:wallet-state {user_id=1}';
    protected $description = 'Check wallet state and transaction confirmation';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);

        if (!$user) {
            $this->error("User {$userId} not found");
            return 1;
        }

        $this->info("🔍 Wallet State for User {$userId}:");
        $this->line("📊 Current Balance: " . number_format($user->balance) . " Toman");
        $this->line("💰 Balance Float: " . $user->balanceFloat);
        
        // Check wallet balance directly
        $wallet = $user->wallet;
        $this->line("🏦 Wallet Balance: " . number_format($wallet->balance) . " Toman");
        
        $this->newLine();
        $this->info("📋 Recent Transactions:");
        
        $transactions = $user->transactions()->latest()->limit(5)->get();
        
        foreach ($transactions as $transaction) {
            $confirmed = $transaction->confirmed ? '✅' : '❌';
            $type = $transaction->type === 'deposit' ? '⬆️' : '⬇️';
            
            $this->line(sprintf(
                "  %s %s ID:%d | %s | %s Toman | %s | %s",
                $confirmed,
                $type,
                $transaction->id,
                $transaction->type,
                number_format($transaction->amount),
                $transaction->created_at->format('H:i:s'),
                $transaction->confirmed ? 'CONFIRMED' : 'PENDING'
            ));
        }
        
        $this->newLine();
        $this->info("🔢 Summary:");
        $this->line("Total Transactions: " . $user->transactions()->count());
        $this->line("Confirmed Transactions: " . $user->transactions()->where('confirmed', true)->count());
        $this->line("Pending Transactions: " . $user->transactions()->where('confirmed', false)->count());
        
        // Calculate balance manually
        $confirmedDeposits = $user->transactions()->where('type', 'deposit')->where('confirmed', true)->sum('amount');
        $confirmedWithdrawals = abs($user->transactions()->where('type', 'withdraw')->where('confirmed', true)->sum('amount'));
        $calculatedBalance = $confirmedDeposits - $confirmedWithdrawals;
        
        $this->line("Confirmed Deposits: " . number_format($confirmedDeposits) . " Toman");
        $this->line("Confirmed Withdrawals: " . number_format($confirmedWithdrawals) . " Toman");
        $this->line("Calculated Balance: " . number_format($calculatedBalance) . " Toman");
        
        if ($calculatedBalance != $user->balance) {
            $this->warn("⚠️  Balance mismatch detected!");
            $this->line("Expected: " . number_format($calculatedBalance));
            $this->line("Actual: " . number_format($user->balance));
        } else {
            $this->info("✅ Balance calculation is correct");
        }
        
        return 0;
    }
} 