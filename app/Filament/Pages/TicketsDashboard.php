<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\TicketStatsWidget;
use App\Models\Ticket;
use App\Models\TicketStatus;

class TicketsDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static string $view = 'filament.pages.tickets-dashboard';

    protected static ?string $navigationLabel = 'داشبورد تیکت‌ها';

    protected static ?string $title = 'مدیریت تیکت‌ها و پشتیبانی';

    protected static ?string $navigationGroup = 'داشبوردها';

    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
    {
        try {
            return (string) Ticket::whereHas('ticketStatus', fn($q) => $q->where('slug', '!=', 'closed'))->count();
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        try {
            $openCount = Ticket::whereHas('ticketStatus', fn($q) => $q->where('slug', '!=', 'closed'))->count();
            
            if ($openCount > 15) {
                return 'danger';
            } elseif ($openCount > 8) {
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
            TicketStatsWidget::class,
        ];
    }

    public function getWidgetData(): array
    {
        $openStatusIds = TicketStatus::whereIn('slug', ['open', 'in-progress'])->pluck('id')->toArray();
        $urgentTickets = Ticket::whereIn('status_id', $openStatusIds)
            ->whereHas('ticketPriority', fn($q) => $q->where('level', '>=', 8))
            ->count();

        return [
            'total_tickets' => Ticket::count(),
            'open_tickets' => Ticket::whereIn('status_id', $openStatusIds)->count(),
            'urgent_tickets' => $urgentTickets,
            'avg_response_time' => $this->getAverageResponseTime(),
            'tickets_today' => Ticket::whereDate('created_at', today())->count(),
        ];
    }

    private function getAverageResponseTime(): ?float
    {
        try {
            // Calculate average response time for resolved tickets in hours
            $resolvedTickets = Ticket::whereHas('ticketStatus', fn($q) => $q->whereIn('slug', ['resolved', 'closed']))
                ->whereBetween('created_at', [now()->subDays(30), now()])
                ->get();

            if ($resolvedTickets->isEmpty()) {
                return null;
            }

            $totalHours = 0;
            $count = 0;

            foreach ($resolvedTickets as $ticket) {
                if ($ticket->updated_at && $ticket->created_at) {
                    $totalHours += $ticket->created_at->diffInHours($ticket->updated_at);
                    $count++;
                }
            }

            return $count > 0 ? round($totalHours / $count, 1) : null;
        } catch (\Exception $e) {
            return null;
        }
    }
} 