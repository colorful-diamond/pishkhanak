<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\TicketPriority;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class TicketStatsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        // Get status IDs
        $openStatusId = TicketStatus::where('slug', 'open')->first()?->id;
        $inProgressStatusId = TicketStatus::where('slug', 'in-progress')->first()?->id;
        $waitingStatusIds = TicketStatus::where('slug', 'waiting-for-user')->pluck('id')->toArray();
        $resolvedStatusIds = TicketStatus::whereIn('slug', ['resolved', 'closed'])->pluck('id')->toArray();
        $openStatusIds = TicketStatus::whereIn('slug', ['open', 'in-progress'])->pluck('id')->toArray();
        
        // Get priority IDs
        $urgentPriorityIds = TicketPriority::where('level', '>=', 8)->pluck('id')->toArray();
        
        // Get ticket counts by status
        $totalTickets = Ticket::count();
        $openTickets = $openStatusId ? Ticket::where('status_id', $openStatusId)->count() : 0;
        $inProgressTickets = $inProgressStatusId ? Ticket::where('status_id', $inProgressStatusId)->count() : 0;
        $waitingForUserTickets = Ticket::whereIn('status_id', $waitingStatusIds)->count();
        $resolvedTickets = Ticket::whereIn('status_id', $resolvedStatusIds)->count();
        
        // Get urgent tickets
        $urgentTickets = Ticket::whereIn('priority_id', $urgentPriorityIds)
                              ->whereIn('status_id', $openStatusIds)
                              ->count();
        
        // Get unassigned tickets
        $unassignedTickets = Ticket::whereNull('assigned_to')
                                  ->whereIn('status_id', $openStatusIds)
                                  ->count();
        
        // Get tickets created in last 24 hours
        $recentTickets = Ticket::where('created_at', '>=', now()->subDay())->count();
        
        // Calculate average response time (PostgreSQL compatible)
        $avgResponseTime = Ticket::whereNotNull('first_response_at')
            ->selectRaw('AVG(EXTRACT(EPOCH FROM (first_response_at - created_at))/3600) as avg_response')
            ->value('avg_response');
        
        // Get ticket activity trend (compare with previous period)
        $previousPeriodTickets = Ticket::whereBetween('created_at', [
            now()->subDays(2),
            now()->subDay()
        ])->count();
        
        $ticketTrend = $recentTickets - $previousPeriodTickets;

        return [
            Stat::make('کل تیکت‌ها', number_format($totalTickets))
                ->description('تعداد کل تیکت‌های سیستم')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('primary')
                ->chart($this->getTicketChart()),
                
            Stat::make('تیکت‌های باز', number_format($openTickets))
                ->description('نیاز به بررسی اولیه')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color($openTickets > 10 ? 'danger' : ($openTickets > 5 ? 'warning' : 'success')),
                
            Stat::make('در حال بررسی', number_format($inProgressTickets))
                ->description('تیکت‌های در دست بررسی')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            Stat::make('فوری', number_format($urgentTickets))
                ->description('تیکت‌های با اولویت فوری')
                ->descriptionIcon('heroicon-m-fire')
                ->color($urgentTickets > 0 ? 'danger' : 'success'),
                
            Stat::make('بدون تخصیص', number_format($unassignedTickets))
                ->description('تیکت‌های بدون تخصیص')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color($unassignedTickets > 0 ? 'warning' : 'success'),
                
            Stat::make('تیکت‌های جدید', number_format($recentTickets))
                ->description('در ۲۴ ساعت گذشته')
                ->descriptionIcon($ticketTrend > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($ticketTrend > 0 ? 'success' : 'gray')
                ->descriptionColor($ticketTrend > 0 ? 'success' : 'danger'),
                
            Stat::make('میانگین پاسخ‌گویی', $avgResponseTime ? round($avgResponseTime, 1) . ' ساعت' : 'نامشخص')
                ->description('زمان حل مسئله')
                ->descriptionIcon('heroicon-m-clock')
                ->color($avgResponseTime && $avgResponseTime < 24 ? 'success' : ($avgResponseTime && $avgResponseTime < 48 ? 'warning' : 'danger')),
                
            Stat::make('انتظار پاسخ کاربر', number_format($waitingForUserTickets))
                ->description('منتظر پاسخ کاربران')
                ->descriptionIcon('heroicon-m-chat-bubble-left-ellipsis')
                ->color('info'),
        ];
    }

    /**
     * Get ticket creation chart data for the last 7 days
     */
    protected function getTicketChart(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $count = Ticket::whereDate('created_at', $date)->count();
            $data[] = $count;
        }
        return $data;
    }

    public function getDescription(): ?string
    {
        return 'آمار و وضعیت کلی سیستم تیکتینگ';
    }
} 