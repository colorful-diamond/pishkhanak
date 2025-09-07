<?php

namespace App\Console\Commands;

use App\Models\Otp;
use App\Services\SmsService;
use Illuminate\Console\Command;

class ClearOtps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:clear {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired OTP records';

    /**
     * Execute the console command.
     */
    public function handle(SmsService $smsService): int
    {
        $this->info('🧹 Starting OTP cleanup...');

        // Get counts before cleanup
        $totalBefore = Otp::count();
        $expiredBefore = Otp::expired()->count();
        $activeBefore = Otp::active()->count();

        $this->table(
            ['Type', 'Count'],
            [
                ['Total OTPs', $totalBefore],
                ['Expired OTPs', $expiredBefore],
                ['Active OTPs', $activeBefore],
            ]
        );

        if ($expiredBefore === 0) {
            $this->info('✅ No expired OTPs found. Nothing to clean up.');
            return Command::SUCCESS;
        }

        // Confirm cleanup unless force flag is used
        if (!$this->option('force')) {
            if (!$this->confirm("Do you want to delete {$expiredBefore} expired OTP(s)?")) {
                $this->info('❌ Cleanup cancelled.');
                return Command::SUCCESS;
            }
        }

        // Perform cleanup
        $deletedCount = $smsService->cleanup();

        $this->info("✅ Cleanup completed!");
        $this->info("📊 Deleted {$deletedCount} expired OTP records.");

        // Show stats after cleanup
        $totalAfter = Otp::count();
        $activeAfter = Otp::active()->count();

        $this->table(
            ['Metric', 'Before', 'After', 'Difference'],
            [
                ['Total OTPs', $totalBefore, $totalAfter, $totalBefore - $totalAfter],
                ['Active OTPs', $activeBefore, $activeAfter, $activeBefore - $activeAfter],
            ]
        );

        // Show SMS statistics
        if ($this->option('verbose')) {
            $this->info('📈 SMS Statistics:');
            $stats = $smsService->getStatistics();
            
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Total SMS Today', $stats['total_today']],
                    ['Login SMS Today', $stats['login_today']],
                    ['Register SMS Today', $stats['register_today']],
                    ['Password Reset SMS Today', $stats['password_reset_today']],
                    ['Verified Today', $stats['verified_today']],
                ]
            );
        }

        return Command::SUCCESS;
    }
}
