<?php

namespace App\Filament\Widgets;

use App\Models\GatewayTransaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class GatewayTransactionChartWidget extends ChartWidget
{
    protected static ?string $heading = 'روند تراکنش‌ها (30 روز اخیر)';

    protected static ?int $sort = 2;

    protected static ?string $pollingInterval = '30s';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $days = collect(range(29, 0))->map(function ($day) {
            return now()->subDays($day)->format('Y-m-d');
        });

        $completedStatus = GatewayTransaction::STATUS_COMPLETED;
        
        $transactions = GatewayTransaction::where('created_at', '>=', now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_count'),
                DB::raw("COUNT(CASE WHEN status = ? THEN 1 END) as completed_count"),
                DB::raw("SUM(CASE WHEN status = ? THEN total_amount ELSE 0 END) as revenue"),
                DB::raw("SUM(CASE WHEN status = ? THEN gateway_fee ELSE 0 END) as profit")
            )
            ->addBinding([$completedStatus, $completedStatus, $completedStatus], 'select')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $totalTransactions = [];
        $completedTransactions = [];
        $revenue = [];
        $profit = [];
        $labels = [];

        foreach ($days as $day) {
            $dayData = $transactions->get($day);
            
            $labels[] = now()->createFromFormat('Y-m-d', $day)->format('m/d');
            $totalTransactions[] = $dayData ? $dayData->total_count : 0;
            $completedTransactions[] = $dayData ? $dayData->completed_count : 0;
            $revenue[] = $dayData ? round($dayData->revenue / 1000) : 0; // Convert to thousands
            $profit[] = $dayData ? round($dayData->profit / 1000) : 0; // Convert to thousands
        }

        return [
            'datasets' => [
                [
                    'label' => 'کل تراکنش‌ها',
                    'data' => $totalTransactions,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.1,
                ],
                [
                    'label' => 'تراکنش‌های موفق',
                    'data' => $completedTransactions,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.1,
                ],
                [
                    'label' => 'درآمد (هزار تومان)',
                    'data' => $revenue,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'yAxisID' => 'y1',
                    'fill' => false,
                    'tension' => 0.1,
                ],
                [
                    'label' => 'سود (هزار تومان)',
                    'data' => $profit,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'yAxisID' => 'y1',
                    'fill' => false,
                    'tension' => 0.1,
                ],
            ],
            'labels' => $labels,
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
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 15,
                    ],
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'تعداد تراکنش',
                    ],
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'مبلغ (هزار تومان)',
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                        'color' => 'rgba(0, 0, 0, 0.1)',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'تاریخ',
                    ],
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
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