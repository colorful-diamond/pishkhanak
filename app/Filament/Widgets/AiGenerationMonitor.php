<?php

namespace App\Filament\Widgets;

use App\Models\AiContent;
use App\Models\AiProcessingStatus;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class AiGenerationMonitor extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected int | string | array $columnSpan = 'full';
    
    protected function getStats(): array
    {
        $totalContent = AiContent::count();
        $servicesContent = AiContent::where('model_type', 'Service')->count();
        $blogsContent = AiContent::where('model_type', 'BlogContentPipeline')->count();
        
        $completedToday = AiContent::whereDate('completed_at', today())->count();
        $failedToday = AiContent::whereDate('failed_at', today())->count();
        $processingNow = AiContent::where('status', 'generating')->count();
        
        $avgQualityScore = DB::table('blog_content_pipelines')
            ->whereNotNull('quality_score')
            ->avg('quality_score');
        
        $activeJobs = AiProcessingStatus::where('status', 'processing')->count();
        
        return [
            Stat::make('Total AI Content', number_format($totalContent))
                ->description("Services: {$servicesContent} | Blogs: {$blogsContent}")
                ->icon('heroicon-o-document-text')
                ->chart([7, 12, 15, 18, 20, 22, $totalContent])
                ->color('primary'),
                
            Stat::make('Processing Now', $processingNow)
                ->description("Active jobs: {$activeJobs}")
                ->icon('heroicon-o-arrow-path')
                ->color($processingNow > 0 ? 'warning' : 'success'),
                
            Stat::make('Completed Today', $completedToday)
                ->description($failedToday > 0 ? "Failed: {$failedToday}" : 'All successful')
                ->icon('heroicon-o-check-circle')
                ->color($failedToday > 0 ? 'warning' : 'success'),
                
            Stat::make('Avg Quality Score', $avgQualityScore ? round($avgQualityScore * 100) . '%' : 'N/A')
                ->description('Blog content quality')
                ->icon('heroicon-o-star')
                ->color($avgQualityScore > 0.8 ? 'success' : ($avgQualityScore > 0.6 ? 'warning' : 'danger')),
        ];
    }
    
    public static function canView(): bool
    {
        return auth()->user()?->hasRole(['super_admin', 'admin']);
    }
}