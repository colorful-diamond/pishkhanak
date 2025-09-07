<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Bavix\Wallet\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WalletManagementStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        // Get users with wallets
        $totalUsers = User::whereHas('wallet')->count();
        
        // Calculate total wallet balance across all users using Bavix wallet
        // Instead of User::sum('balance'), we need to sum the wallet balances
        $totalWalletBalance = \Bavix\Wallet\Models\Wallet::where('holder_type', User::class)->sum('balance') ?? 0;
        
        // Get wallet transactions in the last 24 hours
        $recentTransactions = Transaction::where('created_at', '>=', now()->subDay())->count();
        
        // Get admin transactions (charges/deductions) in the last 7 days
        $adminTransactionsWeek = Transaction::where('created_at', '>=', now()->subWeek())
            ->where(function ($query) {
                $query->whereJsonContains('meta->type', 'admin_charge')
                      ->orWhereJsonContains('meta->type', 'admin_deduction');
            })
            ->count();
            
        // Get pending transactions count
        $pendingTransactions = Transaction::where('confirmed', false)->count();
        
        // Calculate average wallet balance
        $avgWalletBalance = $totalUsers > 0 ? ($totalWalletBalance / $totalUsers) : 0;

        return [
            Stat::make('کل موجودی کیف پول‌ها', number_format($totalWalletBalance) . ' تومان')
                ->description('مجموع موجودی تمام کاربران')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart($this->getWalletBalanceChart()),
                
            Stat::make('کاربران دارای کیف پول', number_format($totalUsers))
                ->description('تعداد کاربران با کیف پول فعال')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
                
            Stat::make('میانگین موجودی', number_format($avgWalletBalance) . ' تومان')
                ->description('میانگین موجودی هر کاربر')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info'),
                
            Stat::make('تراکنش‌های اخیر', number_format($recentTransactions))
                ->description('تراکنش‌های 24 ساعت گذشته')
                ->descriptionIcon('heroicon-m-clock')
                ->color($recentTransactions > 0 ? 'success' : 'gray'),
                
            Stat::make('عملیات ادمین هفته', number_format($adminTransactionsWeek))
                ->description('شارژ/کسر توسط ادمین در 7 روز گذشته')
                ->descriptionIcon('heroicon-m-cog-6-tooth')
                ->color($adminTransactionsWeek > 0 ? 'warning' : 'gray'),
                
            Stat::make('تراکنش‌های معلق', number_format($pendingTransactions))
                ->description('تراکنش‌های در انتظار تایید')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($pendingTransactions > 0 ? 'danger' : 'success'),
        ];
    }

    /**
     * Get wallet balance trend chart for the last 7 days
     */
    private function getWalletBalanceChart(): array
    {
        $data = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            
            // Get total deposits and withdrawals for each day
            $deposits = Transaction::where('type', 'deposit')
                ->where('confirmed', true)
                ->whereDate('created_at', $date)
                ->sum('amount');
                
            $withdrawals = Transaction::where('type', 'withdraw')
                ->where('confirmed', true)
                ->whereDate('created_at', $date)
                ->sum('amount');
                
            $netChange = $deposits - $withdrawals;
            $data[] = $netChange / 1000; // Convert to thousands for better chart display
        }
        
        return $data;
    }

    /**
     * Get the widget title
     */
    public function getHeading(): string
    {
        return 'آمار مدیریت کیف پول';
    }

    /**
     * Check if widget should be displayed
     */
    public static function canView(): bool
    {
        return Auth::check();
    }
} 