<?php

namespace App\Filament\Widgets;

use App\Models\GatewayTransaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class GatewayTransactionStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        $today = now()->startOfDay();
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();
        
        // Today's stats
        $todayTransactions = GatewayTransaction::whereDate('created_at', $today)->get();
        $todayRevenue = $todayTransactions->where('status', GatewayTransaction::STATUS_COMPLETED)->sum('total_amount');
        $todayProfit = $todayTransactions->where('status', GatewayTransaction::STATUS_COMPLETED)->sum('gateway_fee');
        
        // This month's stats
        $thisMonthTransactions = GatewayTransaction::where('created_at', '>=', $thisMonth)->get();
        $thisMonthRevenue = $thisMonthTransactions->where('status', GatewayTransaction::STATUS_COMPLETED)->sum('total_amount');
        $thisMonthProfit = $thisMonthTransactions->where('status', GatewayTransaction::STATUS_COMPLETED)->sum('gateway_fee');
        
        // Last month's stats for comparison
        $lastMonthTransactions = GatewayTransaction::whereBetween('created_at', [$lastMonth, $thisMonth])->get();
        $lastMonthRevenue = $lastMonthTransactions->where('status', GatewayTransaction::STATUS_COMPLETED)->sum('total_amount');
        $lastMonthProfit = $lastMonthTransactions->where('status', GatewayTransaction::STATUS_COMPLETED)->sum('gateway_fee');
        
        // Calculate growth percentages
        $revenueGrowth = $lastMonthRevenue > 0 ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;
        $profitGrowth = $lastMonthProfit > 0 ? (($thisMonthProfit - $lastMonthProfit) / $lastMonthProfit) * 100 : 0;
        
        // Pending transactions count
        $pendingCount = GatewayTransaction::whereIn('status', [
            GatewayTransaction::STATUS_PENDING,
            GatewayTransaction::STATUS_PROCESSING
        ])->count();
        
        // Success rate calculation
        $totalTransactions = GatewayTransaction::count();
        $successfulTransactions = GatewayTransaction::where('status', GatewayTransaction::STATUS_COMPLETED)->count();
        $successRate = $totalTransactions > 0 ? ($successfulTransactions / $totalTransactions) * 100 : 0;

        return [
            Stat::make('درآمد امروز', number_format($todayRevenue) . ' تومان')
                ->description('مجموع تراکنش‌های امروز')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
                
            Stat::make('سود امروز', number_format($todayProfit) . ' تومان')
                ->description('کارمزد دریافتی امروز')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
                
            Stat::make('درآمد ماه جاری', number_format($thisMonthRevenue) . ' تومان')
                ->description($revenueGrowth >= 0 ? 
                    sprintf('+%.1f%% نسبت به ماه قبل', $revenueGrowth) : 
                    sprintf('%.1f%% نسبت به ماه قبل', $revenueGrowth)
                )
                ->descriptionIcon($revenueGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueGrowth >= 0 ? 'success' : 'danger'),
                
            Stat::make('سود ماه جاری', number_format($thisMonthProfit) . ' تومان')
                ->description($profitGrowth >= 0 ? 
                    sprintf('+%.1f%% نسبت به ماه قبل', $profitGrowth) : 
                    sprintf('%.1f%% نسبت به ماه قبل', $profitGrowth)
                )
                ->descriptionIcon($profitGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($profitGrowth >= 0 ? 'success' : 'danger'),
                
            Stat::make('تراکنش‌های در انتظار', $pendingCount)
                ->description('نیاز به بررسی')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingCount > 0 ? 'warning' : 'success'),
                
            Stat::make('نرخ موفقیت', sprintf('%.1f%%', $successRate))
                ->description('از کل تراکنش‌ها')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($successRate >= 95 ? 'success' : ($successRate >= 85 ? 'warning' : 'danger')),
        ];
    }
} 