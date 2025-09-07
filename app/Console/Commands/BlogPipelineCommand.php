<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BlogContentPipeline;
use App\Models\BlogProcessingQueue;
use App\Models\BlogPublicationQueue;
use App\Models\BlogPipelineSetting;
use App\Jobs\ProcessBlogContentJob;
use App\Jobs\PublishScheduledBlogPostsJob;

class BlogPipelineCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:pipeline 
                            {action : Action to perform (status|process|publish|schedule|settings)}
                            {--count= : Number of posts to process}
                            {--priority=normal : Priority for processing (low|normal|high|urgent)}
                            {--date= : Date for scheduling or publishing}
                            {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage blog content pipeline';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        
        switch ($action) {
            case 'status':
                $this->showStatus();
                break;
                
            case 'process':
                $this->processContent();
                break;
                
            case 'publish':
                $this->publishContent();
                break;
                
            case 'schedule':
                $this->scheduleContent();
                break;
                
            case 'settings':
                $this->showSettings();
                break;
                
            default:
                $this->error("Unknown action: {$action}");
                $this->info('Available actions: status, process, publish, schedule, settings');
                return 1;
        }
        
        return 0;
    }

    /**
     * Show pipeline status
     */
    protected function showStatus()
    {
        $this->info('Blog Content Pipeline Status');
        $this->line('═══════════════════════════════════════════');
        
        // Overall statistics
        $stats = [
            ['Status', 'Count'],
            ['────────────────', '──────'],
            ['Imported', BlogContentPipeline::imported()->count()],
            ['Queued', BlogContentPipeline::queued()->count()],
            ['Processing', BlogContentPipeline::processing()->count()],
            ['Processed', BlogContentPipeline::processed()->count()],
            ['Reviewed', BlogContentPipeline::where('status', 'reviewed')->count()],
            ['Scheduled', BlogContentPipeline::scheduled()->count()],
            ['Published', BlogContentPipeline::published()->count()],
            ['Failed', BlogContentPipeline::failed()->count()],
            ['Total', BlogContentPipeline::count()],
        ];
        
        $this->table(['Status', 'Count'], array_slice($stats, 2));
        
        $this->newLine();
        $this->info('Processing Queue');
        $this->line('─────────────────');
        
        $processingStats = [
            ['Pending', BlogProcessingQueue::pending()->count()],
            ['Processing', BlogProcessingQueue::processing()->count()],
            ['Completed', BlogProcessingQueue::where('status', 'completed')->count()],
            ['Failed', BlogProcessingQueue::where('status', 'failed')->count()],
        ];
        
        $this->table(['Status', 'Count'], $processingStats);
        
        $this->newLine();
        $this->info('Publication Schedule');
        $this->line('────────────────────');
        
        $publicationStats = [
            ['Today', BlogPublicationQueue::today()->pending()->count()],
            ['Tomorrow', BlogPublicationQueue::tomorrow()->pending()->count()],
            ['Next 7 days', BlogPublicationQueue::upcoming()
                ->where('publish_date', '<=', now()->addDays(7))
                ->count()],
            ['Next 30 days', BlogPublicationQueue::upcoming()
                ->where('publish_date', '<=', now()->addDays(30))
                ->count()],
        ];
        
        $this->table(['Period', 'Scheduled'], $publicationStats);
        
        // Quality metrics
        $this->newLine();
        $this->info('Quality Metrics');
        $this->line('───────────────');
        
        $avgQuality = BlogContentPipeline::processed()
            ->whereNotNull('quality_score')
            ->avg('quality_score');
        
        $highQuality = BlogContentPipeline::processed()
            ->highQuality()
            ->count();
        
        $requiresReview = BlogContentPipeline::processed()
            ->requiringReview()
            ->count();
        
        $this->line("Average Quality Score: " . number_format($avgQuality ?? 0, 2));
        $this->line("High Quality Posts: {$highQuality}");
        $this->line("Requires Review: {$requiresReview}");
    }

    /**
     * Process content through AI
     */
    protected function processContent()
    {
        $count = $this->option('count') ?? 10;
        $priority = $this->option('priority');
        $dryRun = $this->option('dry-run');
        
        $this->info("Processing {$count} posts with {$priority} priority");
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No jobs will be dispatched');
        }
        
        // Get posts ready for processing
        $posts = BlogContentPipeline::imported()
            ->orderBy('created_at')
            ->limit($count)
            ->get();
        
        if ($posts->isEmpty()) {
            $this->warn('No posts available for processing');
            return;
        }
        
        $bar = $this->output->createProgressBar($posts->count());
        $bar->start();
        
        foreach ($posts as $pipeline) {
            if (!$dryRun) {
                // Create processing queue entry
                $queue = BlogProcessingQueue::create([
                    'pipeline_id' => $pipeline->id,
                    'priority' => $priority,
                    'status' => BlogProcessingQueue::STATUS_PENDING,
                    'queued_at' => now(),
                    'processing_config' => BlogPipelineSetting::getAiProcessingConfig(),
                ]);
                
                // Update pipeline status
                $pipeline->update(['status' => BlogContentPipeline::STATUS_QUEUED]);
                
                // Dispatch the job
                ProcessBlogContentJob::dispatch($pipeline, $queue);
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("Queued {$posts->count()} posts for processing");
    }

    /**
     * Publish scheduled content
     */
    protected function publishContent()
    {
        $date = $this->option('date') ? \Carbon\Carbon::parse($this->option('date')) : now();
        $dryRun = $this->option('dry-run');
        
        $this->info("Publishing posts for: " . $date->toDateString());
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No posts will be published');
            
            $scheduled = BlogPublicationQueue::forDate($date)
                ->pending()
                ->with('pipeline')
                ->get();
            
            if ($scheduled->isEmpty()) {
                $this->warn('No posts scheduled for this date');
                return;
            }
            
            $this->table(
                ['ID', 'Title', 'Quality Score', 'Order'],
                $scheduled->map(function ($item) {
                    return [
                        $item->pipeline->id,
                        substr($item->pipeline->ai_title ?? $item->pipeline->title, 0, 50),
                        $item->pipeline->quality_score,
                        $item->publish_order,
                    ];
                })
            );
            
            return;
        }
        
        // Dispatch the publishing job
        PublishScheduledBlogPostsJob::dispatch($date);
        
        $this->info('Publishing job dispatched');
    }

    /**
     * Schedule content for publication
     */
    protected function scheduleContent()
    {
        $count = $this->option('count') ?? 100;
        $startDate = $this->option('date') 
            ? \Carbon\Carbon::parse($this->option('date')) 
            : now()->startOfDay();
        
        $dryRun = $this->option('dry-run');
        
        $this->info("Scheduling {$count} posts starting from: " . $startDate->toDateString());
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No posts will be scheduled');
        }
        
        // Get processed posts ready for scheduling
        $posts = BlogContentPipeline::readyForPublication()
            ->orderBy('quality_score', 'desc')
            ->orderBy('processing_completed_at')
            ->limit($count)
            ->get();
        
        if ($posts->isEmpty()) {
            $this->warn('No posts ready for publication');
            return;
        }
        
        $dailyLimit = BlogPipelineSetting::getDailyPublishLimit();
        $currentDate = $startDate->copy();
        $scheduled = 0;
        
        $bar = $this->output->createProgressBar($posts->count());
        $bar->start();
        
        foreach ($posts as $pipeline) {
            if (!$dryRun) {
                // Check how many posts are already scheduled for this date
                $existingCount = BlogPublicationQueue::forDate($currentDate)->count();
                
                if ($existingCount >= $dailyLimit) {
                    $currentDate->addDay();
                    $existingCount = 0;
                }
                
                // Create publication queue entry
                BlogPublicationQueue::create([
                    'pipeline_id' => $pipeline->id,
                    'publish_date' => $currentDate,
                    'publish_order' => $existingCount + 1,
                    'status' => BlogPublicationQueue::STATUS_PENDING,
                ]);
                
                // Update pipeline status
                $pipeline->markAsScheduled($currentDate);
                
                $scheduled++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("Scheduled {$scheduled} posts for publication");
        
        if (!$dryRun && $scheduled > 0) {
            $lastDate = $currentDate->toDateString();
            $this->info("Posts scheduled from {$startDate->toDateString()} to {$lastDate}");
        }
    }

    /**
     * Show pipeline settings
     */
    protected function showSettings()
    {
        $this->info('Blog Pipeline Settings');
        $this->line('══════════════════════');
        
        $settings = [
            ['Setting', 'Value'],
            ['───────────────────────', '─────────────────'],
            ['Daily Publish Limit', BlogPipelineSetting::getDailyPublishLimit()],
            ['Publishing Enabled', BlogPipelineSetting::isPublishingEnabled() ? 'Yes' : 'No'],
            ['Min Quality Score', BlogPipelineSetting::getMinQualityScore()],
        ];
        
        $this->table(['Setting', 'Value'], array_slice($settings, 2));
        
        $this->newLine();
        $this->info('AI Processing Config');
        $config = BlogPipelineSetting::getAiProcessingConfig();
        foreach ($config as $key => $value) {
            $this->line("{$key}: {$value}");
        }
        
        $this->newLine();
        $this->info('Publishing Schedule');
        $schedule = BlogPipelineSetting::getPublishingSchedule();
        foreach ($schedule as $key => $value) {
            if (is_array($value)) {
                $this->line("{$key}: " . implode(', ', $value));
            } else {
                $this->line("{$key}: {$value}");
            }
        }
        
        $this->newLine();
        $this->info('Quality Thresholds');
        $thresholds = BlogPipelineSetting::getQualityThresholds();
        foreach ($thresholds as $key => $value) {
            $this->line("{$key}: {$value}");
        }
    }
}