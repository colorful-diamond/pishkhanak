<?php

namespace App\Filament\Resources\BlogPipelineResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\BlogContentPipeline;
use App\Models\BlogPublicationQueue;

class BlogPipelineStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalPosts = BlogContentPipeline::count();
        $imported = BlogContentPipeline::where('status', 'imported')->count();
        $processing = BlogContentPipeline::whereIn('status', ['queued', 'processing'])->count();
        $processed = BlogContentPipeline::where('status', 'processed')->count();
        $scheduled = BlogContentPipeline::where('status', 'scheduled')->count();
        $published = BlogContentPipeline::where('status', 'published')->count();
        $failed = BlogContentPipeline::where('status', 'failed')->count();
        
        $todayScheduled = BlogPublicationQueue::today()->pending()->count();
        $tomorrowScheduled = BlogPublicationQueue::tomorrow()->pending()->count();
        
        $avgQuality = BlogContentPipeline::whereNotNull('quality_score')->avg('quality_score');
        $highQuality = BlogContentPipeline::where('quality_score', '>=', 0.7)->count();
        
        $progressPercent = $totalPosts > 0 ? round(($published / 18506) * 100, 1) : 0;
        $remainingDays = $published > 0 ? ceil((18506 - $published) / 100) : 185;

        return [
            Stat::make('Total Imported', number_format($totalPosts))
                ->description("Out of 18,506 posts")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 12, 18, 25, 35, 45, $totalPosts])
                ->color('primary'),
                
            Stat::make('Ready to Process', number_format($imported))
                ->description($processing . ' currently processing')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            Stat::make('AI Processed', number_format($processed))
                ->description('Avg quality: ' . round($avgQuality * 100, 0) . '%')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('success'),
                
            Stat::make('Published', number_format($published))
                ->description($progressPercent . '% complete')
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart($this->getPublishingTrend())
                ->color('success'),
                
            Stat::make('Today\'s Schedule', $todayScheduled . ' / 100')
                ->description('Tomorrow: ' . $tomorrowScheduled)
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
                
            Stat::make('Days Remaining', $remainingDays)
                ->description('At 100 posts/day')
                ->descriptionIcon('heroicon-m-clock')
                ->color($remainingDays < 30 ? 'danger' : 'secondary'),
        ];
    }

    protected function getPublishingTrend(): array
    {
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = BlogContentPipeline::whereDate('published_at', $date)->count();
            $trend[] = $count;
        }
        return $trend;
    }

    public function getPollingInterval(): ?string
    {
        return '30s';
    }
}