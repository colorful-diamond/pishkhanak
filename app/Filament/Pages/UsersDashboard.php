<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;

class UsersDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static string $view = 'filament.pages.users-dashboard';

    protected static ?string $navigationLabel = 'داشبورد کاربران';

    protected static ?string $title = 'تحلیل و مدیریت کاربران';

    protected static ?string $navigationGroup = 'داشبوردها';

    protected static ?int $navigationSort = 5;

    public static function getNavigationBadge(): ?string
    {
        try {
            // Get new users today
            return (string) User::whereDate('created_at', today())->count();
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        try {
            $newUsers = User::whereDate('created_at', today())->count();
            
            if ($newUsers > 50) {
                return 'success';
            } elseif ($newUsers > 20) {
                return 'warning';
            }
            
            return 'gray';
        } catch (\Exception $e) {
            return 'gray';
        }
    }

    public function getWidgetData(): array
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('email_verified_at', '!=', null)->count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'new_users_week' => User::whereBetween('created_at', [now()->subWeek(), now()])->count(),
            'new_users_month' => User::whereBetween('created_at', [now()->subMonth(), now()])->count(),
            'users_with_transactions' => User::whereHas('transactions')->count(),
            'users_with_wallets' => User::whereHas('wallet')->count(),
            'growth_rate' => $this->getUserGrowthRate(),
        ];
    }

    private function getUserGrowthRate(): float
    {
        $thisMonth = User::whereBetween('created_at', [now()->startOfMonth(), now()])->count();
        $lastMonth = User::whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->startOfMonth()])->count();

        if ($lastMonth == 0) {
            return $thisMonth > 0 ? 100 : 0;
        }

        return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1);
    }
} 