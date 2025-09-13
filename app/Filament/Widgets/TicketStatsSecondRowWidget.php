<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use App\Models\TicketStatus;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TicketStatsSecondRowWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected static ?string $pollingInterval = '30s';
    
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Get status IDs
        $openStatusIds = TicketStatus::whereIn('slug', ['open', 'in-progress'])->pluck('id')->toArray();
        
        // Get unassigned tickets
        $unassignedTickets = Ticket::whereNull('assigned_to')
                                  ->whereIn('status_id', $openStatusIds)
                                  ->count();
        
        // Get overdue tickets (tickets older than 24 hours without response)
        $overdueTickets = Ticket::whereIn('status_id', $openStatusIds)
                                ->where('created_at', '<', now()->subDay())
                                ->whereNull('first_response_at')
                                ->count();
        
        // Get tickets created today
        $todayTickets = Ticket::whereDate('created_at', today())->count();
        
        // Calculate average response time
        $avgResponseTime = Ticket::whereNotNull('first_response_at')
            ->selectRaw('AVG(EXTRACT(EPOCH FROM (first_response_at - created_at))/3600) as avg_response')
            ->value('avg_response');
        
        return [
            Stat::make('تیکت‌های بدون مسئول', number_format($unassignedTickets))
                ->description('تیکت‌هایی که هنوز تخصیص نیافته‌اند')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color($unassignedTickets > 0 ? 'warning' : 'success'),
                
            Stat::make('تیکت‌های عقب‌افتاده', number_format($overdueTickets))
                ->description('تیکت‌های بیش از ۲۴ ساعت بدون پاسخ')
                ->descriptionIcon('heroicon-m-clock')
                ->color($overdueTickets > 0 ? 'danger' : 'warning'),
                
            Stat::make('تیکت‌های امروز', number_format($todayTickets))
                ->description('تیکت‌های ایجادشده امروز')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),
                
            Stat::make('میانگین زمان پاسخ', $avgResponseTime ? round($avgResponseTime, 1) . ' ساعت' : 'بدون داده')
                ->description('میانگین زمان اولین پاسخ به تیکت')
                ->descriptionIcon('heroicon-m-bolt')
                ->color($avgResponseTime && $avgResponseTime < 24 ? 'success' : ($avgResponseTime && $avgResponseTime < 48 ? 'warning' : 'danger')),
        ];
    }
}