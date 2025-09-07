<?php

namespace App\Filament\Widgets;

use App\Models\GatewayTransaction;
use App\Models\PaymentGateway;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class PaymentStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $today = now()->startOfDay();
        $thisMonth = now()->startOfMonth();

        // Today's stats
        $todayTransactions = GatewayTransaction::whereDate('created_at', $today)->count();
        $todayAmount = GatewayTransaction::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->sum('total_amount');
        $todaySuccessRate = $todayTransactions > 0 
            ? round((GatewayTransaction::whereDate('created_at', $today)->where('status', 'completed')->count() / $todayTransactions) * 100, 1)
            : 0;

        // This month's stats
        $monthTransactions = GatewayTransaction::whereDate('created_at', '>=', $thisMonth)->count();
        $monthAmount = GatewayTransaction::whereDate('created_at', '>=', $thisMonth)
            ->where('status', 'completed')
            ->sum('total_amount');
        $monthSuccessRate = $monthTransactions > 0 
            ? round((GatewayTransaction::whereDate('created_at', '>=', $thisMonth)->where('status', 'completed')->count() / $monthTransactions) * 100, 1)
            : 0;

        // Active gateways
        $activeGateways = PaymentGateway::where('is_active', true)->count();

        return [
            Stat::make('تراکنش‌های امروز', $todayTransactions)
                ->description('تعداد تراکنش‌های ایجاد شده امروز')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),

            Stat::make('مبلغ امروز', number_format($todayAmount) . ' تومان')
                ->description('مجموع مبالغ تراکنش‌های موفق امروز')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('نرخ موفقیت امروز', $todaySuccessRate . '%')
                ->description('درصد تراکنش‌های موفق امروز')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($todaySuccessRate >= 90 ? 'success' : ($todaySuccessRate >= 70 ? 'warning' : 'danger')),

            Stat::make('تراکنش‌های ماه', $monthTransactions)
                ->description('تعداد تراکنش‌های ایجاد شده این ماه')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make('مبلغ ماه', number_format($monthAmount) . ' تومان')
                ->description('مجموع مبالغ تراکنش‌های موفق این ماه')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('درگاه‌های فعال', $activeGateways)
                ->description('تعداد درگاه‌های پرداخت فعال')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('warning'),
        ];
    }
} 