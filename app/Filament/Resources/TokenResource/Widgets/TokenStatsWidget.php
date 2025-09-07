<?php

namespace App\Filament\Resources\TokenResource\Widgets;

use App\Models\Token;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TokenStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        try {
            $totalTokens = Token::count();
            $activeTokens = Token::where('is_active', true)->count();
            $expiredTokens = Token::where('is_active', true)
                ->where('expires_at', '<', now())
                ->count();
            $needsRefreshTokens = Token::where('is_active', true)
                ->where('expires_at', '<', now()->addMinutes(5))
                ->where('expires_at', '>', now())
                ->count();
        } catch (\Exception $e) {
            // Return zeros if table doesn't exist (before migration)
            $totalTokens = $activeTokens = $expiredTokens = $needsRefreshTokens = 0;
        }

        return [
            Stat::make(__('admin.total_tokens'), $totalTokens)
                ->description(__('admin.all_tokens_in_system'))
                ->descriptionIcon('heroicon-m-key')
                ->color('primary'),

            Stat::make(__('admin.active_tokens'), $activeTokens)
                ->description(__('admin.currently_active_tokens'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make(__('admin.expired_tokens'), $expiredTokens)
                ->description(__('admin.tokens_that_have_expired'))
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make(__('admin.tokens_need_refresh'), $needsRefreshTokens)
                ->description(__('admin.tokens_expiring_soon'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
} 