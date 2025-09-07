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
            Stat::make('PERSIAN_TEXT_72008d08', number_format($unassignedTickets))
                ->description('PERSIAN_TEXT_424a810b')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color($unassignedTickets > 0 ? 'warning' : 'success'),
                
            Stat::make('PERSIAN_TEXT_cf713f31', number_format($overdueTickets))
                ->description('PERSIAN_TEXT_7dcb1990')
                ->descriptionIcon('heroicon-m-clock')
                ->color($overdueTickets > 0 ? 'danger' : 'warning'),
                
            Stat::make('PERSIAN_TEXT_aac7d1e0', number_format($todayTickets))
                ->description('PERSIAN_TEXT_e0618071')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),
                
            Stat::make('PERSIAN_TEXT_aa7e9106', $avgResponseTime ? round($avgResponseTime, 1) . 'PERSIAN_TEXT_5cdcc33b' : 'PERSIAN_TEXT_264f61d0')
                ->description('PERSIAN_TEXT_3d479511')
                ->descriptionIcon('heroicon-m-bolt')
                ->color($avgResponseTime && $avgResponseTime < 24 ? 'success' : ($avgResponseTime && $avgResponseTime < 48 ? 'warning' : 'danger')),
        ];
    }
}