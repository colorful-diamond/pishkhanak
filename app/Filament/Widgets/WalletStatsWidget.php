<?php

namespace App\Filament\Widgets;

use Bavix\Wallet\Models\Wallet;
use Bavix\Wallet\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class WalletStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalBalance = Wallet::sum('balance');
        $totalWallets = Wallet::count();
        $totalTransactions = Transaction::count();
        $pendingTransactions = Transaction::where('confirmed', 0)->count();
        
        $todayDeposits = Transaction::where('type', 'deposit')
            ->where('confirmed', 1)
            ->whereDate('created_at', today())
            ->sum('amount');
            
        $todayWithdrawals = Transaction::where('type', 'withdraw')
            ->where('confirmed', 1)
            ->whereDate('created_at', today())
            ->sum('amount');

        $lastWeekDeposits = Transaction::where('type', 'deposit')
            ->where('confirmed', 1)
            ->whereBetween('created_at', [now()->subWeek(), now()])
            ->sum('amount');

        $previousWeekDeposits = Transaction::where('type', 'deposit')
            ->where('confirmed', 1)
            ->whereBetween('created_at', [now()->subWeeks(2), now()->subWeek()])
            ->sum('amount');

        $depositsGrowth = $previousWeekDeposits > 0 
            ? (($lastWeekDeposits - $previousWeekDeposits) / $previousWeekDeposits) * 100 
            : 0;

        return [
            Stat::make('مجموع موجودی کیف‌پول‌ها', number_format($totalBalance) . ' تومان')
                ->description('موجودی تمام کیف‌پول‌های کاربران')
                ->descriptionIcon('heroicon-m-wallet')
                ->color('success'),

            Stat::make('تعداد کیف‌پول‌ها', number_format($totalWallets))
                ->description('کل کیف‌پول‌های ایجاد شده')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('واریزی‌های امروز', number_format($todayDeposits) . ' تومان')
                ->description(sprintf('%.1f%% نسبت به هفته گذشته', $depositsGrowth))
                ->descriptionIcon($depositsGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($depositsGrowth >= 0 ? 'success' : 'danger'),

            Stat::make('برداشت‌های امروز', number_format(abs($todayWithdrawals)) . ' تومان')
                ->description('مجموع برداشت‌های امروز')
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->color('warning'),

            Stat::make('تعداد تراکنش‌ها', number_format($totalTransactions))
                ->description($pendingTransactions > 0 ? "{$pendingTransactions} در انتظار تایید" : 'همه تایید شده')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($pendingTransactions > 0 ? 'warning' : 'success'),

            Stat::make('نسبت موجودی فعال', $totalWallets > 0 ? number_format((Wallet::where('balance', '>', 0)->count() / $totalWallets) * 100, 1) . '%' : '0%')
                ->description('درصد کیف‌پول‌های دارای موجودی')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
} 