<?php

namespace App\Filament\Resources\BlogPipelineResource\Pages;

use App\Filament\Resources\BlogPipelineResource;
use Filament\Resources\Pages\Page;
use Filament\Actions;
use App\Models\BlogContentPipeline;
use App\Models\BlogProcessingQueue;
use App\Models\BlogPublicationQueue;
use App\Models\BlogPipelineSetting;
use Livewire\Attributes\On;
use Filament\Notifications\Notification;

class BlogPipelineDashboard extends Page
{
    protected static string $resource = BlogPipelineResource::class;

    protected static string $view = 'filament.resources.blog-pipeline.dashboard';
    
    protected static ?string $title = 'Pipeline Dashboard';
    
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    public $importProgress = [];
    public $processingStats = [];
    public $publishingStats = [];
    public $qualityMetrics = [];
    public $systemHealth = [];

    public function mount(): void
    {
        $this->loadDashboardData();
    }

    #[On('refresh-dashboard')]
    public function loadDashboardData(): void
    {
        // Import Progress
        $total = 18506;
        $imported = BlogContentPipeline::count();
        $this->importProgress = [
            'total' => $total,
            'imported' => $imported,
            'remaining' => $total - $imported,
            'percentage' => round(($imported / $total) * 100, 2),
            'last_import' => BlogContentPipeline::latest()->first()?->created_at?->diffForHumans() ?? 'Never',
        ];

        // Processing Stats
        $this->processingStats = [
            'queued' => BlogProcessingQueue::where('status', 'pending')->count(),
            'processing' => BlogProcessingQueue::where('status', 'processing')->count(),
            'completed_today' => BlogProcessingQueue::where('status', 'completed')
                ->whereDate('completed_at', today())->count(),
            'failed' => BlogProcessingQueue::where('status', 'failed')->count(),
            'avg_processing_time' => BlogContentPipeline::whereNotNull('processing_time_seconds')
                ->avg('processing_time_seconds'),
        ];

        // Publishing Stats
        $this->publishingStats = [
            'published_today' => BlogContentPipeline::whereDate('published_at', today())->count(),
            'scheduled_today' => BlogPublicationQueue::today()->pending()->count(),
            'scheduled_week' => BlogPublicationQueue::upcoming()
                ->where('publish_date', '<=', now()->addDays(7))->count(),
            'scheduled_month' => BlogPublicationQueue::upcoming()
                ->where('publish_date', '<=', now()->addDays(30))->count(),
            'daily_limit' => BlogPipelineSetting::getDailyPublishLimit(),
        ];

        // Quality Metrics
        $this->qualityMetrics = [
            'average_score' => BlogContentPipeline::whereNotNull('quality_score')
                ->avg('quality_score') * 100,
            'high_quality' => BlogContentPipeline::where('quality_score', '>=', 0.7)->count(),
            'medium_quality' => BlogContentPipeline::whereBetween('quality_score', [0.5, 0.7])->count(),
            'low_quality' => BlogContentPipeline::where('quality_score', '<', 0.5)
                ->whereNotNull('quality_score')->count(),
            'requiring_review' => BlogContentPipeline::where('requires_review', true)->count(),
        ];

        // System Health
        $failedJobs = \DB::table('failed_jobs')->count();
        $queueSize = BlogProcessingQueue::where('status', 'pending')->count();
        
        $this->systemHealth = [
            'status' => $failedJobs > 10 ? 'critical' : ($failedJobs > 0 ? 'warning' : 'healthy'),
            'failed_jobs' => $failedJobs,
            'queue_size' => $queueSize,
            'workers_active' => $this->checkWorkersActive(),
            'last_publish' => BlogContentPipeline::where('status', 'published')
                ->latest('published_at')->first()?->published_at?->diffForHumans() ?? 'Never',
        ];
    }

    protected function checkWorkersActive(): bool
    {
        // Check if any processing happened in last 5 minutes
        return BlogProcessingQueue::where('status', 'processing')
            ->where('started_at', '>=', now()->subMinutes(5))
            ->exists();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('refresh')
                ->label('Refresh')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    $this->loadDashboardData();
                    $this->dispatch('refresh-dashboard');
                }),
                
            Actions\Action::make('run_import')
                ->label('Run Import')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->requiresConfirmation()
                ->action(function () {
                    \Artisan::queue('blog:import-bson', ['--limit' => 500]);
                    
                    Notification::make()
                        ->title('Import Started')
                        ->body('Importing next 500 posts in background')
                        ->success()
                        ->send();
                }),
                
            Actions\Action::make('process_batch')
                ->label('Process Batch')
                ->icon('heroicon-o-sparkles')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    \Artisan::queue('blog:pipeline', ['action' => 'process', '--count' => 20]);
                    
                    Notification::make()
                        ->title('Processing Started')
                        ->body('Processing 20 posts with AI')
                        ->success()
                        ->send();
                }),
                
            Actions\Action::make('settings')
                ->label('Pipeline Settings')
                ->icon('heroicon-o-cog-6-tooth')
                ->modal('settings-modal')
                ->modalContent(fn () => view('filament.resources.blog-pipeline.settings')),
        ];
    }

    public function startImport(): void
    {
        \Artisan::queue('blog:import-bson');
        
        Notification::make()
            ->title('Import Started')
            ->body('Full import running in background')
            ->success()
            ->send();
            
        $this->loadDashboardData();
    }

    public function startProcessing(): void
    {
        \Artisan::queue('blog:pipeline', ['action' => 'process', '--count' => 50]);
        
        Notification::make()
            ->title('Processing Started')
            ->body('Processing 50 posts with AI')
            ->success()
            ->send();
            
        $this->loadDashboardData();
    }

    public function schedulePublishing(): void
    {
        \Artisan::queue('blog:pipeline', ['action' => 'schedule', '--count' => 100]);
        
        Notification::make()
            ->title('Scheduling Complete')
            ->body('Scheduled next 100 posts for publication')
            ->success()
            ->send();
            
        $this->loadDashboardData();
    }

    public function clearFailedJobs(): void
    {
        \Artisan::call('queue:flush');
        
        Notification::make()
            ->title('Failed Jobs Cleared')
            ->success()
            ->send();
            
        $this->loadDashboardData();
    }
}