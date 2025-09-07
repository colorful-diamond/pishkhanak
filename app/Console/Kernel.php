<?php

namespace App\Console;

use App\Console\Commands\ClearOtps;
use App\Console\Commands\ImportBanks;
use App\Console\Commands\RefreshTokensCommand;
use App\Console\Commands\CleanupExpiredSmsTokensCommand;
use App\Console\Commands\CleanupAbandonedHoldTransactions;
use App\Jobs\RefreshApiTokensJob;
use App\Jobs\AutomaticTokenRefreshJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ClearOtps::class,
        ImportBanks::class,
        CleanupExpiredSmsTokensCommand::class,
        CleanupAbandonedHoldTransactions::class
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Clean up expired SMS tokens every hour
        $schedule->command('finnotech:cleanup-expired-sms-tokens --force')
                 ->hourly()
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->appendOutputTo(storage_path('logs/sms-token-cleanup.log'));

        // Clean up abandoned wallet hold transactions every hour
        $schedule->command('wallet:cleanup-abandoned-holds --max-age-hours=1')
                 ->hourly()
                 ->name('cleanup-abandoned-holds')
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->appendOutputTo(storage_path('logs/abandoned-holds-cleanup.log'))
                 ->onFailure(function () {
                     \Illuminate\Support\Facades\Log::error('Scheduled abandoned hold transactions cleanup failed');
                 })
                 ->onSuccess(function () {
                     \Illuminate\Support\Facades\Log::info('Scheduled abandoned hold transactions cleanup completed successfully');
                 });

        // Clean up expired SMS tokens daily with more verbose logging
        $schedule->command('finnotech:cleanup-expired-sms-tokens --force')
                 ->daily()
                 ->at('02:00')
                 ->emailOutputOnFailure('admin@pishkhanak.com');

        // Automatic token refresh every 12 hours with advanced logging
        $schedule->job(new AutomaticTokenRefreshJob())
                 ->everyTwelveHours()
                 ->name('automatic-token-refresh')
                 ->withoutOverlapping(60) // Prevent overlap for up to 60 minutes
                 ->runInBackground()
                 ->onFailure(function () {
                     \Illuminate\Support\Facades\Log::error('Automatic token refresh job failed permanently');
                 })
                 ->onSuccess(function () {
                     \Illuminate\Support\Facades\Log::info('Automatic token refresh job completed successfully');
                 });

        // Backup refresh job (legacy) - runs twice daily
        $schedule->job(new RefreshApiTokensJob())
                 ->twiceDaily(8, 20) // 8 AM and 8 PM
                 ->name('backup-token-refresh')
                 ->withoutOverlapping()
                 ->when(function () {
                     // Only run if automatic refresh failed in last 12 hours
                     return \App\Models\TokenRefreshLog::where('trigger_type', 'automatic')
                         ->where('created_at', '>=', now()->subHours(12))
                         ->where('status', 'failed')
                         ->exists();
                 })
                 ->onFailure(function () {
                     \Illuminate\Support\Facades\Log::error('Backup token refresh job failed');
                 });

        // Additional cleanup job to run once daily
        $schedule->call(function () {
            \App\Models\Token::clearAllCache();
            \Illuminate\Support\Facades\Log::info('Token cache cleared by scheduled job');
        })->daily()->at('01:00')->name('clear-token-cache');

        // Clean up expired OTPs daily
        $schedule->command('otp:clear --force')
                 ->daily()
                 ->at('02:00')
                 ->name('cleanup-expired-otps')
                 ->onFailure(function () {
                     \Illuminate\Support\Facades\Log::error('Scheduled OTP cleanup failed');
                 });

        // Clean up expired service requests weekly
        $schedule->command('services:cleanup-requests --days=7')
                 ->weekly()
                 ->sundays()
                 ->at('03:00')
                 ->name('cleanup-expired-service-requests')
                 ->onFailure(function () {
                     \Illuminate\Support\Facades\Log::error('Scheduled service request cleanup failed');
                 });

        // Clean up expired Finnotech SMS auth tokens daily
        $schedule->command('finnotech:cleanup-sms-tokens --force')
                 ->daily()
                 ->at('03:30')
                 ->name('cleanup-expired-sms-tokens')
                 ->withoutOverlapping()
                 ->onFailure(function () {
                     \Illuminate\Support\Facades\Log::error('Scheduled Finnotech SMS token cleanup failed');
                 })
                 ->onSuccess(function () {
                     \Illuminate\Support\Facades\Log::info('Scheduled Finnotech SMS token cleanup completed successfully');
                 });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
