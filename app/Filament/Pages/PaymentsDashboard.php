<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\PaymentChartWidget;
use App\Filament\Widgets\GatewayTransactionChartWidget;
use App\Filament\Widgets\PaymentStatsWidget;
use App\Filament\Widgets\GatewayTransactionStatsWidget;
use App\Filament\Widgets\PaymentSourcesOverviewWidget;

class PaymentsDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static string $view = 'filament.pages.payments-dashboard';

    protected static ?string $navigationLabel = 'داشبورد پرداخت‌ها';

    protected static ?string $title = 'مدیریت پرداخت‌ها و تراکنش‌ها';

    protected static ?string $navigationGroup = 'داشبوردها';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        try {
            // Get today's failed transactions count
            return (string) \App\Models\GatewayTransaction::whereDate('created_at', today())
                ->whereIn('status', ['failed', 'cancelled', 'expired'])
                ->count();
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        try {
            $failedCount = \App\Models\GatewayTransaction::whereDate('created_at', today())
                ->whereIn('status', ['failed', 'cancelled', 'expired'])
                ->count();
            
            if ($failedCount > 10) {
                return 'danger';
            } elseif ($failedCount > 5) {
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
            PaymentStatsWidget::class,
            GatewayTransactionStatsWidget::class,
            PaymentSourcesOverviewWidget::class,
        ];
    }

    protected function getWidgets(): array
    {
        return [
            PaymentChartWidget::class,
            GatewayTransactionChartWidget::class,
        ];
    }

    public function getWidgetData(): array
    {
        return [
            'payments_today' => \App\Models\GatewayTransaction::whereDate('created_at', today())->count(),
            'revenue_today' => \App\Models\GatewayTransaction::whereDate('created_at', today())
                ->where('status', 'completed')
                ->sum('total_amount'),
            'success_rate' => $this->getSuccessRate(),
        ];
    }

    private function getSuccessRate(): float
    {
        $total = \App\Models\GatewayTransaction::whereDate('created_at', today())->count();
        $successful = \App\Models\GatewayTransaction::whereDate('created_at', today())
            ->where('status', 'completed')
            ->count();

        return $total > 0 ? round(($successful / $total) * 100, 1) : 0;
    }
} 