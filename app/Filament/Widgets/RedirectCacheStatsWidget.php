<?php

namespace App\Filament\Widgets;

use App\Models\Redirect;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class RedirectCacheStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    
    protected function getStats(): array
    {
        $stats = Redirect::getCacheStats();
        $totalRedirects = Redirect::where('is_active', true)->count();
        $totalHits = Redirect::sum('hit_count');
        
        // Calculate cache efficiency
        $cacheEfficiency = $stats['cache_keys_count'] > 0 ? 
            round(($stats['cache_keys_count'] / $totalRedirects) * 100, 1) : 0;

        return [
            Stat::make('فعال ریدایرکت‌ها', $totalRedirects)
                ->description('تعداد کل ریدایرکت‌های فعال')
                ->descriptionIcon('heroicon-o-arrow-path')
                ->color('success'),

            Stat::make('کلیک‌های کل', number_format($totalHits))
                ->description('تعداد کل استفاده از ریدایرکت‌ها')
                ->descriptionIcon('heroicon-o-cursor-arrow-rays')
                ->color('info'),

            Stat::make('وضعیت کش', $stats['active_redirects_cached'] ? 'فعال' : 'غیرفعال')
                ->description($stats['cache_keys_count'] . ' کلید در کش')
                ->descriptionIcon('heroicon-o-cpu-chip')
                ->color($stats['active_redirects_cached'] ? 'success' : 'danger'),

            Stat::make('کارایی کش', $cacheEfficiency . '%')
                ->description('درصد ریدایرکت‌های کش شده')
                ->descriptionIcon('heroicon-o-bolt')
                ->color($cacheEfficiency > 80 ? 'success' : ($cacheEfficiency > 50 ? 'warning' : 'danger')),
        ];
    }
}