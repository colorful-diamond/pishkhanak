<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\WalletStatsWidget;
use App\Filament\Widgets\WalletManagementStatsWidget;

class WalletsDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    protected static string $view = 'filament.pages.wallets-dashboard';

    protected static ?string $navigationLabel = 'داشبورد کیف‌پول‌ها';

    protected static ?string $title = 'مدیریت کیف‌پول‌ها و موجودی';

    protected static ?string $navigationGroup = 'داشبوردها';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        try {
            // Get pending transactions count
            return (string) \Bavix\Wallet\Models\Transaction::where('confirmed', false)->count();
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        try {
            $pendingCount = \Bavix\Wallet\Models\Transaction::where('confirmed', false)->count();
            
            if ($pendingCount > 20) {
                return 'danger';
            } elseif ($pendingCount > 10) {
                return 'warning';
            }
            
            return 'success';
        } catch (\Exception $e) {
            return 'gray';
        }
    }

    protected function getHeaderWidgets(): array
    {
        return [
            WalletStatsWidget::class,
            WalletManagementStatsWidget::class,
        ];
    }

    public function getWidgetData(): array
    {
        return [
            'total_balance' => \Bavix\Wallet\Models\Wallet::where('holder_type', \App\Models\User::class)->sum('balance'),
            'active_wallets' => \Bavix\Wallet\Models\Wallet::where('balance', '>', 0)->count(),
            'transactions_today' => \Bavix\Wallet\Models\Transaction::whereDate('created_at', today())->count(),
            'pending_transactions' => \Bavix\Wallet\Models\Transaction::where('confirmed', false)->count(),
        ];
    }
} 