<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\TicketPriority;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TicketStatsFirstRowWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected static ?string $pollingInterval = '30s';
    
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Get status IDs
        $openStatusId = TicketStatus::where('slug', 'open')->first()?->id;
        $resolvedStatusIds = TicketStatus::whereIn('slug', ['resolved', 'closed'])->pluck('id')->toArray();
        
        // Get priority IDs
        $urgentPriorityIds = TicketPriority::where('level', '>=', 8)->pluck('id')->toArray();
        $openStatusIds = TicketStatus::whereIn('slug', ['open', 'in-progress'])->pluck('id')->toArray();
        
        // Get ticket counts
        $totalTickets = Ticket::count();
        $openTickets = $openStatusId ? Ticket::where('status_id', $openStatusId)->count() : 0;
        $closedTickets = Ticket::whereIn('status_id', $resolvedStatusIds)->count();
        $urgentTickets = Ticket::whereIn('priority_id', $urgentPriorityIds)
                              ->whereIn('status_id', $openStatusIds)
                              ->count();
        
        // Get current user's tickets
        $currentUser = auth()->user();
        $myTickets = $currentUser ? Ticket::where('user_id', $currentUser->id)->count() : 0;
        
        return [
            Stat::make('کل تیکت‌ها', number_format($totalTickets))
                ->description('مجموع تیکت‌های سیستم')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('primary'),
                
            Stat::make('تیکت‌های باز', number_format($openTickets))
                ->description('تیکت‌های در انتظار بررسی')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color($openTickets > 10 ? 'danger' : ($openTickets > 5 ? 'warning' : 'success')),
                
            Stat::make('تیکت‌های بسته', number_format($closedTickets))
                ->description('تیکت‌های حل‌شده و بسته')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('تیکت‌های فوری', number_format($urgentTickets))
                ->description('تیکت‌های با اولویت بالا')
                ->descriptionIcon('heroicon-m-fire')
                ->color($urgentTickets > 0 ? 'danger' : 'success'),
                
            Stat::make('تیکت‌های من', number_format($myTickets))
                ->description('تیکت‌های من در سیستم')
                ->descriptionIcon('heroicon-m-user')
                ->color('info'),
        ];
    }
}