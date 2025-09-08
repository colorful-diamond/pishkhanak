<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Models\AiContent;
use App\Jobs\ProcessServiceContentWithMonitoringJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RegenerateServiceContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:regenerate-content 
                            {--all : Regenerate content for all services}
                            {--failed : Regenerate only failed content}
                            {--missing : Generate only for services without content}
                            {--service=* : Specific service IDs to regenerate}
                            {--category= : Regenerate services in specific category}
                            {--force : Force regeneration even if content exists}
                            {--sections=8 : Number of sections to generate}
                            {--subheadings=4 : Number of subheadings per section}
                            {--no-images : Skip image generation}
                            {--no-search : Disable web search}
                            {--dry-run : Show what would be regenerated without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate AI content for services using the new monitoring pipeline';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Service Content Regeneration Tool');
        $this->line('=====================================');
        
        $services = $this->getServicesToRegenerate();
        
        if ($services->isEmpty()) {
            $this->warn('No services found matching the criteria.');
            return 0;
        }
        
        $this->info("Found {$services->count()} services to process.");
        
        if ($this->option('dry-run')) {
            $this->table(
                ['ID', 'Title', 'Current Content', 'Status'],
                $services->map(function ($service) {
                    $aiContent = is_numeric($service->content) ? AiContent::find($service->content) : null;
                    return [
                        $service->id,
                        substr($service->title, 0, 50),
                        $aiContent ? "AI Content #{$aiContent->id}" : 'None',
                        $aiContent ? $aiContent->status : 'N/A'
                    ];
                })->toArray()
            );
            $this->info('Dry run complete. No changes made.');
            return 0;
        }
        
        if (!$this->confirm("Do you want to regenerate content for {$services->count()} services?")) {
            $this->info('Operation cancelled.');
            return 0;
        }
        
        $this->line('');
        $progressBar = $this->output->createProgressBar($services->count());
        $progressBar->start();
        
        $dispatched = 0;
        $failed = 0;
        
        foreach ($services as $service) {
            try {
                ProcessServiceContentWithMonitoringJob::dispatch($service, [
                    'force' => $this->option('force'),
                    'use_web_search' => !$this->option('no-search'),
                    'generate_images' => !$this->option('no-images'),
                    'sections_count' => (int) $this->option('sections'),
                    'subheadings_per_section' => (int) $this->option('subheadings'),
                ])->onQueue('ai-content');
                
                $dispatched++;
                $progressBar->advance();
                
                // Small delay to avoid overwhelming the queue
                usleep(100000); // 0.1 second
                
            } catch (\Exception $e) {
                $failed++;
                $this->error("\nFailed to dispatch job for service #{$service->id}: {$e->getMessage()}");
            }
        }
        
        $progressBar->finish();
        $this->line("\n");
        
        $this->info("✅ Successfully dispatched: {$dispatched} jobs");
        if ($failed > 0) {
            $this->error("❌ Failed to dispatch: {$failed} jobs");
        }
        
        $this->line('');
        $this->info('Jobs have been queued. Monitor progress with:');
        $this->line('  php artisan queue:work --queue=ai-content');
        $this->line('  php artisan queue:listen --queue=ai-content --tries=3');
        
        return $failed > 0 ? 1 : 0;
    }
    
    /**
     * Get services to regenerate based on options
     */
    protected function getServicesToRegenerate()
    {
        $query = Service::query();
        
        // Filter by specific service IDs
        if ($serviceIds = $this->option('service')) {
            $query->whereIn('id', $serviceIds);
        }
        
        // Filter by category
        elseif ($categoryId = $this->option('category')) {
            $query->where('category_id', $categoryId);
        }
        
        // Filter by status
        elseif ($this->option('failed')) {
            // Get services with failed AI content
            $failedAiContentIds = AiContent::where('status', 'failed')
                ->where('model_type', 'Service')
                ->pluck('id');
            
            // Safer approach: filter services where content matches failed AI content IDs
            $query->whereIn('content', $failedAiContentIds->map(function($id) {
                return (string) $id;
            }));
        }
        
        elseif ($this->option('missing')) {
            // Get services without AI content
            $query->where(function ($q) {
                $q->whereNull('content')
                  ->orWhere('content', '')
                  ->orWhereNotExists(function ($subQuery) {
                      $subQuery->select(DB::raw(1))
                               ->from('ai_contents')
                               ->whereColumn('ai_contents.id', '=', DB::raw('CAST(services.content AS VARCHAR)'));
                  });
            });
        }
        
        elseif (!$this->option('all') && !$this->option('force')) {
            // Default: services without content
            $query->where(function ($q) {
                $q->whereNull('content')
                  ->orWhere('content', '');
            });
        }
        
        // Always filter active services only
        $query->where('is_active', true);
        
        // Order by priority (you can adjust this)
        $query->orderBy('id');
        
        return $query->get();
    }
}