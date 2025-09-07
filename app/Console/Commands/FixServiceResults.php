<?php

namespace App\Console\Commands;

use App\Models\ServiceResult;
use App\Models\User;
use Illuminate\Console\Command;

class FixServiceResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:fix-results {--user-id= : Show results for specific user} {--show-latest : Show latest results} {--cleanup : Clean up orphaned records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix and diagnose service result issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('show-latest')) {
            $this->showLatestResults();
        }

        if ($this->option('user-id')) {
            $this->showUserResults($this->option('user-id'));
        }

        if ($this->option('cleanup')) {
            $this->cleanupOrphanedRecords();
        }

        if (!$this->option('show-latest') && !$this->option('user-id') && !$this->option('cleanup')) {
            $this->info('Service Results Diagnostic Tool');
            $this->info('Available options:');
            $this->info('  --show-latest     Show latest service results');
            $this->info('  --user-id=1       Show results for specific user');
            $this->info('  --cleanup         Clean up orphaned records');
        }
    }

    private function showLatestResults()
    {
        $this->info('📊 Latest Service Results:');
        $results = ServiceResult::with('service', 'user')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        foreach ($results as $result) {
            $this->line(sprintf(
                'ID: %d | Hash: %s | User: %d | Service: %s | Status: %s | Date: %s',
                $result->id,
                $result->result_hash,
                $result->user_id,
                $result->service->title ?? 'Unknown',
                $result->status,
                $result->processed_at->format('Y-m-d H:i:s')
            ));
            
            $url = route('services.result', ['id' => $result->result_hash]);
            $this->line("  URL: {$url}");
            $this->line('');
        }
    }

    private function showUserResults($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return;
        }

        $this->info("👤 Results for User: {$user->name} (ID: {$userId})");
        
        $results = ServiceResult::with('service')
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->get();

        if ($results->isEmpty()) {
            $this->warn('No results found for this user.');
            return;
        }

        foreach ($results as $result) {
            $this->line(sprintf(
                'ID: %d | Hash: %s | Service: %s | Status: %s | Date: %s',
                $result->id,
                $result->result_hash,
                $result->service->title ?? 'Unknown',
                $result->status,
                $result->processed_at->format('Y-m-d H:i:s')
            ));
            
            $url = route('services.result', ['id' => $result->result_hash]);
            $this->line("  URL: {$url}");
            
            if ($result->isExpired()) {
                $this->line('  ⚠️  This result is expired');
            } else {
                $this->line('  ✅ This result is valid');
            }
            $this->line('');
        }
    }

    private function cleanupOrphanedRecords()
    {
        $this->info('🧹 Cleaning up orphaned records...');
        
        // Find expired results
        $expiredCount = ServiceResult::whereRaw('processed_at < NOW() - INTERVAL 30 DAY')->count();
        
        if ($expiredCount > 0) {
            if ($this->confirm("Found {$expiredCount} expired results. Delete them?")) {
                ServiceResult::whereRaw('processed_at < NOW() - INTERVAL 30 DAY')->delete();
                $this->info("Deleted {$expiredCount} expired results.");
            }
        } else {
            $this->info('No expired results found.');
        }

        // Show summary
        $totalResults = ServiceResult::count();
        $this->info("Total remaining results: {$totalResults}");
    }
} 