<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BlogContentPipeline;
use Illuminate\Support\Facades\DB;

class BlogImportStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:import-status 
                            {--show-ids : Show list of imported MongoDB IDs}
                            {--check-duplicates : Check for duplicate imports}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the status of BSON import progress';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📊 Blog Import Status Report');
        $this->line('════════════════════════════════════════════');
        
        // Total count
        $totalImported = BlogContentPipeline::count();
        $expectedTotal = 18506;
        $remaining = $expectedTotal - $totalImported;
        $percentComplete = $totalImported > 0 ? round(($totalImported / $expectedTotal) * 100, 2) : 0;
        
        // Status breakdown
        $statusCounts = BlogContentPipeline::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        
        // Display main statistics
        $this->newLine();
        $this->info('Import Progress:');
        $this->line("Total Imported: {$totalImported} / {$expectedTotal}");
        $this->line("Remaining: {$remaining}");
        $this->line("Progress: {$percentComplete}%");
        
        // Progress bar
        $this->newLine();
        $progressBar = $this->output->createProgressBar($expectedTotal);
        $progressBar->setProgress($totalImported);
        $progressBar->finish();
        $this->newLine(2);
        
        // Status breakdown
        if (!empty($statusCounts)) {
            $this->info('Status Breakdown:');
            $this->table(
                ['Status', 'Count', 'Percentage'],
                collect($statusCounts)->map(function($count, $status) use ($totalImported) {
                    $percentage = $totalImported > 0 ? round(($count / $totalImported) * 100, 2) : 0;
                    return [
                        ucfirst($status),
                        number_format($count),
                        "{$percentage}%"
                    ];
                })->toArray()
            );
        }
        
        // Check for duplicates if requested
        if ($this->option('check-duplicates')) {
            $this->checkDuplicates();
        }
        
        // Show sample of imported IDs if requested
        if ($this->option('show-ids')) {
            $this->showImportedIds();
        }
        
        // Recent imports
        $this->showRecentImports();
        
        // Recommendations
        $this->showRecommendations($totalImported, $remaining);
        
        return 0;
    }
    
    /**
     * Check for duplicate imports
     */
    protected function checkDuplicates()
    {
        $this->newLine();
        $this->info('Checking for duplicates...');
        
        $duplicates = BlogContentPipeline::select('original_id', DB::raw('COUNT(*) as count'))
            ->groupBy('original_id')
            ->having('count', '>', 1)
            ->get();
        
        if ($duplicates->isEmpty()) {
            $this->line('✅ No duplicate imports found');
        } else {
            $this->warn("⚠️ Found {$duplicates->count()} duplicate MongoDB IDs");
            $this->table(
                ['MongoDB ID', 'Duplicate Count'],
                $duplicates->take(10)->map(function($item) {
                    return [$item->original_id, $item->count];
                })->toArray()
            );
            
            if ($duplicates->count() > 10) {
                $this->line("... and " . ($duplicates->count() - 10) . " more duplicates");
            }
        }
    }
    
    /**
     * Show sample of imported IDs
     */
    protected function showImportedIds()
    {
        $this->newLine();
        $this->info('Sample of Imported MongoDB IDs:');
        
        $firstIds = BlogContentPipeline::orderBy('id', 'asc')
            ->limit(5)
            ->pluck('original_id', 'id');
        
        $lastIds = BlogContentPipeline::orderBy('id', 'desc')
            ->limit(5)
            ->pluck('original_id', 'id');
        
        $this->line('First 5 imports:');
        foreach ($firstIds as $id => $mongoId) {
            $this->line("  ID {$id}: {$mongoId}");
        }
        
        $this->line('Last 5 imports:');
        foreach ($lastIds as $id => $mongoId) {
            $this->line("  ID {$id}: {$mongoId}");
        }
    }
    
    /**
     * Show recent imports
     */
    protected function showRecentImports()
    {
        $this->newLine();
        $this->info('Recent Import Activity:');
        
        $recentImports = BlogContentPipeline::select(
                DB::raw('DATE(created_at) as import_date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('import_date')
            ->orderBy('import_date', 'desc')
            ->get();
        
        if ($recentImports->isEmpty()) {
            $this->line('No imports in the last 7 days');
        } else {
            $this->table(
                ['Date', 'Posts Imported'],
                $recentImports->map(function($item) {
                    return [
                        $item->import_date,
                        number_format($item->count)
                    ];
                })->toArray()
            );
        }
        
        // Last import time
        $lastImport = BlogContentPipeline::orderBy('created_at', 'desc')->first();
        if ($lastImport) {
            $this->line("Last import: " . $lastImport->created_at->diffForHumans());
        }
    }
    
    /**
     * Show recommendations based on current status
     */
    protected function showRecommendations($totalImported, $remaining)
    {
        $this->newLine();
        $this->info('📝 Recommendations:');
        
        if ($totalImported === 0) {
            $this->line('• Start import with: php artisan blog:import-bson');
            $this->line('• Test with small batch first: php artisan blog:import-bson --limit=10');
        } elseif ($remaining > 0) {
            $this->line("• Resume import with: php artisan blog:import-bson");
            $this->line("  (Will automatically skip {$totalImported} already imported posts)");
            
            if ($remaining > 1000) {
                $batchSize = min(1000, $remaining);
                $this->line("• Or import in batches: php artisan blog:import-bson --limit={$batchSize}");
            }
        } else {
            $this->line('✅ All posts have been imported!');
            
            $processed = BlogContentPipeline::where('status', '!=', 'imported')->count();
            if ($processed === 0) {
                $this->line('• Start processing: php artisan blog:pipeline process');
            } else {
                $this->line('• Continue processing: php artisan blog:pipeline process');
            }
            
            $scheduled = BlogContentPipeline::where('status', 'scheduled')->count();
            if ($scheduled === 0) {
                $this->line('• Schedule for publication: php artisan blog:pipeline schedule');
            }
        }
        
        // Check for stalled imports
        $processing = BlogContentPipeline::where('status', 'processing')->count();
        if ($processing > 0) {
            $this->newLine();
            $this->warn("⚠️ {$processing} posts are stuck in 'processing' status");
            $this->line('  Consider resetting them or checking the processing queue');
        }
    }
}