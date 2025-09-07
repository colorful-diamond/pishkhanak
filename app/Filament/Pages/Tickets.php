<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Ticket;

class Tickets extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static string $view = 'filament.pages.tickets';

    protected static ?string $navigationLabel = 'تیکت‌ها';

    protected static ?string $title = 'مدیریت تیکت‌ها';

    protected static ?string $navigationGroup = 'پشتیبانی';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        try {
            return (string) Ticket::whereHas('status', fn($q) => $q->where('slug', '!=', 'closed'))->count();
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        try {
            $openCount = Ticket::whereHas('status', fn($q) => $q->where('slug', '!=', 'closed'))->count();
            
            if ($openCount > 10) {
                return 'danger';
            } elseif ($openCount > 5) {
                return 'warning';
            }
            
            return 'success';
        } catch (\Exception $e) {
            return 'gray';
        }
    }
} 