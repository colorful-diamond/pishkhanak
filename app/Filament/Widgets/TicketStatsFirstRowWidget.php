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
            Stat::make('PERSIAN_TEXT_95ec8314', number_format($totalTickets))
                ->description('PERSIAN_TEXT_b8e0cc2b')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('primary'),
                
            Stat::make('PERSIAN_TEXT_45b439eb', number_format($openTickets))
                ->description('PERSIAN_TEXT_dac63629')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color($openTickets > 10 ? 'danger' : ($openTickets > 5 ? 'warning' : 'success')),
                
            Stat::make('PERSIAN_TEXT_3cf0bf7f', number_format($closedTickets))
                ->description('PERSIAN_TEXT_fc051316')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('PERSIAN_TEXT_b21bd6a1', number_format($urgentTickets))
                ->description('PERSIAN_TEXT_70dbaa3f')
                ->descriptionIcon('heroicon-m-fire')
                ->color($urgentTickets > 0 ? 'danger' : 'success'),
                
            Stat::make('PERSIAN_TEXT_df184102', number_format($myTickets))
                ->description('PERSIAN_TEXT_bf9dd8dd')
                ->descriptionIcon('heroicon-m-user')
                ->color('info'),
        ];
    }
}