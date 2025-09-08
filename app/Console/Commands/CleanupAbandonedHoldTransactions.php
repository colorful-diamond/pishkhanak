<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Bavix\Wallet\Models\Transaction;
use App\Services\ConfirmationBasedPaymentService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanupAbandonedHoldTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:cleanup-abandoned-holds 
                           {--dry-run : Show what would be cleaned up without actually doing it}
                           {--max-age-hours=1 : Maximum age in hours for hold transactions (default: 1 hour)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up abandoned wallet hold transactions older than specified time';

    protected ConfirmationBasedPaymentService $paymentService;

    public function __construct(ConfirmationBasedPaymentService $paymentService)
    {
        parent::__construct();
        $this->paymentService = $paymentService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $maxAgeHours = (int) $this->option('max-age-hours');
        
        $this->info("🧹 Starting cleanup of abandoned hold transactions older than {$maxAgeHours} hour(s)");
        
        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - No transactions will actually be cancelled');
        }

        // Find abandoned hold transactions
        $cutoffTime = Carbon::now()->subHours($maxAgeHours);
        
        $abandonedTransactions = Transaction::where('confirmed', false)
            ->where('type', 'withdraw') // All wallet withdrawals
            ->where('created_at', '<', $cutoffTime)
            ->where('meta->type', 'service_payment_hold')
            ->where('meta->description', 'LIKE', '%نگهداری وجه برای سرویس%')
            ->get();

        if ($abandonedTransactions->isEmpty()) {
            $this->info('✅ No abandoned hold transactions found');
            return 0;
        }

        $this->info("🎯 Found {$abandonedTransactions->count()} abandoned hold transactions");

        // Show summary table
        $tableData = [];
        $totalAmount = 0;
        
        foreach ($abandonedTransactions as $transaction) {
            $meta = $transaction->meta ?? [];
            $serviceTitle = $meta['service_title'] ?? 'نامشخص';
            $description = $meta['description'] ?? $transaction->description ?? 'نامشخص';
            $amount = abs($transaction->amount);
            $totalAmount += $amount;
            
            $tableData[] = [
                'ID' => $transaction->id,
                'User ID' => $transaction->payable_id,
                'Service' => $serviceTitle,
                'Amount' => number_format($amount) . ' تومان',
                'Age' => $transaction->created_at->diffForHumans(),
                'Created' => $transaction->created_at->format('Y-m-d H:i:s'),
            ];
        }

        $this->table([
            'ID', 'User ID', 'Service', 'Amount', 'Age', 'Created'
        ], $tableData);

        $this->info("💰 Total amount to be released: " . number_format($totalAmount) . " تومان");

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN: These transactions would be cancelled, but no action was taken');
            return 0;
        }

        // Confirm cleanup
        if (!$this->confirm('Do you want to proceed with cleaning up these transactions?', false)) {
            $this->info('❌ Cleanup cancelled by user');
            return 0;
        }

        // Process cleanup
        $cleanedCount = 0;
        $errorCount = 0;

        $progressBar = $this->output->createProgressBar($abandonedTransactions->count());
        $progressBar->start();

        foreach ($abandonedTransactions as $transaction) {
            try {
                $meta = $transaction->meta ?? [];
                $serviceTitle = $meta['service_title'] ?? 'نامشخص';
                
                // Cancel the transaction using the existing service method
                $success = $this->paymentService->forceCancelTransaction(
                    $transaction->id, 
                    "Abandoned hold transaction cleanup - older than {$maxAgeHours} hour(s)"
                );

                if ($success) {
                    $cleanedCount++;
                    
                    Log::info('Abandoned hold transaction cleaned up', [
                        'transaction_id' => $transaction->id,
                        'user_id' => $transaction->payable_id,
                        'service_title' => $serviceTitle,
                        'amount' => abs($transaction->amount),
                        'age_hours' => $transaction->created_at->diffInHours(),
                        'cleanup_reason' => 'Automated cleanup - abandoned hold transaction'
                    ]);
                } else {
                    $errorCount++;
                    
                    Log::error('Failed to cleanup abandoned hold transaction', [
                        'transaction_id' => $transaction->id,
                        'user_id' => $transaction->payable_id,
                        'service_title' => $serviceTitle,
                        'amount' => abs($transaction->amount)
                    ]);
                }

            } catch (\Exception $e) {
                $errorCount++;
                
                Log::error('Exception during hold transaction cleanup', [
                    'transaction_id' => $transaction->id,
                    'user_id' => $transaction->payable_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Show results
        if ($cleanedCount > 0) {
            $this->info("✅ Successfully cleaned up {$cleanedCount} abandoned hold transactions");
        }
        
        if ($errorCount > 0) {
            $this->error("❌ Failed to clean up {$errorCount} transactions");
        }

        // Log summary
        Log::info('Abandoned hold transactions cleanup completed', [
            'total_found' => $abandonedTransactions->count(),
            'successfully_cleaned' => $cleanedCount,
            'errors' => $errorCount,
            'total_amount_released' => $totalAmount,
            'max_age_hours' => $maxAgeHours,
            'dry_run' => $isDryRun
        ]);

        $this->info("🎉 Cleanup completed! Released " . number_format($totalAmount) . " تومان back to user wallets");

        return $cleanedCount > 0 ? 0 : 1;
    }
} 