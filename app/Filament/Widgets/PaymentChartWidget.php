<?php

namespace App\Filament\Widgets;

use App\Models\GatewayTransaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class PaymentChartWidget extends ChartWidget
{
    protected static ?string $heading = 'روند پرداخت‌ها';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $days = collect();
        $completed = collect();
        $failed = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days->push($date->format('Y/m/d'));

            $completed->push(
                GatewayTransaction::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->count()
            );

            $failed->push(
                GatewayTransaction::whereDate('created_at', $date)
                    ->whereIn('status', ['failed', 'cancelled', 'expired'])
                    ->count()
            );
        }

        return [
            'datasets' => [
                [
                    'label' => 'تراکنش‌های موفق',
                    'data' => $completed->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.1,
                ],
                [
                    'label' => 'تراکنش‌های ناموفق',
                    'data' => $failed->toArray(),
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                    'tension' => 0.1,
                ],
            ],
            'labels' => $days->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'aspectRatio' => 2,
            'layout' => [
                'padding' => [
                    'top' => 20,
                    'bottom' => 20,
                    'left' => 10,
                    'right' => 10,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                    ],
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'تعداد تراکنش',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'تاریخ',
                    ],
                ],
            ],
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
        ];
    }


} 