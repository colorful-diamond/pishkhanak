<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;
use App\Models\Ticket;
use App\Models\GatewayTransaction;
use Bavix\Wallet\Models\Transaction;
use Bavix\Wallet\Models\Wallet;

class OverviewDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static string $view = 'filament.pages.overview-dashboard';

    protected static ?string $navigationLabel = 'نمای کلی';

    protected static ?string $title = 'نمای کلی سیستم';

    protected static ?string $navigationGroup = 'داشبوردها';

    protected static ?int $navigationSort = 1;

    public function mount(): void
    {
        // Redirect to overview-dashboard route if we're accessing the main dashboard
        if (request()->route()->getName() === 'filament.access.pages.dashboard') {
            $this->redirect(route('filament.access.pages.overview-dashboard'));
        }
    }

    public function getViewData(): array
    {
        return [
            'summary_stats' => $this->getSummaryStats(),
            'quick_actions' => $this->getQuickActions(),
            'system_health' => $this->getSystemHealth(),
            'recent_activities' => $this->getRecentActivities(),
        ];
    }

    private function getSummaryStats(): array
    {
        try {
            return [
                'users' => [
                    'total' => User::count(),
                    'new_today' => User::whereDate('created_at', today())->count(),
                    'active' => User::where('email_verified_at', '!=', null)->count(),
                ],
                'payments' => [
                    'total_today' => GatewayTransaction::whereDate('created_at', today())->count(),
                    'revenue_today' => GatewayTransaction::whereDate('created_at', today())
                        ->where('status', 'completed')
                        ->sum('total_amount'),
                    'success_rate' => $this->getPaymentSuccessRate(),
                ],
                'tickets' => [
                    'open' => Ticket::whereHas('status', fn($q) => $q->where('slug', '!=', 'closed'))->count(),
                    'new_today' => Ticket::whereDate('created_at', today())->count(),
                    'urgent' => Ticket::whereHas('priority', fn($q) => $q->where('level', '>=', 8))
                        ->whereHas('status', fn($q) => $q->where('slug', '!=', 'closed'))
                        ->count(),
                ],
                'wallets' => [
                    'total_balance' => Wallet::where('holder_type', User::class)->sum('balance'),
                    'active_wallets' => Wallet::where('balance', '>', 0)->count(),
                    'pending_transactions' => Transaction::where('confirmed', false)->count(),
                ],
            ];
        } catch (\Exception $e) {
            return [
                'users' => ['total' => 0, 'new_today' => 0, 'active' => 0],
                'payments' => ['total_today' => 0, 'revenue_today' => 0, 'success_rate' => 0],
                'tickets' => ['open' => 0, 'new_today' => 0, 'urgent' => 0],
                'wallets' => ['total_balance' => 0, 'active_wallets' => 0, 'pending_transactions' => 0],
            ];
        }
    }

    private function getPaymentSuccessRate(): float
    {
        $total = GatewayTransaction::whereDate('created_at', today())->count();
        $successful = GatewayTransaction::whereDate('created_at', today())
            ->where('status', 'completed')
            ->count();

        return $total > 0 ? round(($successful / $total) * 100, 1) : 0;
    }

    private function getQuickActions(): array
    {
        return [
            [
                'title' => 'مدیریت پرداخت‌ها',
                'description' => 'نمایش تراکنش‌ها و آمار پرداخت',
                'url' => PaymentsDashboard::getUrl(),
                'icon' => 'heroicon-o-credit-card',
                'color' => 'success',
            ],
            [
                'title' => 'کیف‌پول‌ها',
                'description' => 'مدیریت موجودی و تراکنش‌های کیف‌پول',
                'url' => WalletsDashboard::getUrl(),
                'icon' => 'heroicon-o-wallet',
                'color' => 'warning',
            ],
            [
                'title' => 'تیکت‌ها و پشتیبانی',
                'description' => 'پاسخ به درخواست‌های کاربران',
                'url' => TicketsDashboard::getUrl(),
                'icon' => 'heroicon-o-ticket',
                'color' => 'danger',
            ],
            [
                'title' => 'تحلیل کاربران',
                'description' => 'آمار و گزارش‌های کاربری',
                'url' => UsersDashboard::getUrl(),
                'icon' => 'heroicon-o-users',
                'color' => 'primary',
            ],
        ];
    }

    private function getSystemHealth(): array
    {
        $health = [];

        // Payment gateway health
        $recentFailures = GatewayTransaction::whereDate('created_at', today())
            ->whereIn('status', ['failed', 'cancelled'])
            ->count();
        $health['payments'] = $recentFailures < 5 ? 'good' : ($recentFailures < 15 ? 'warning' : 'critical');

        // Ticket response health
        $openTickets = Ticket::whereHas('status', fn($q) => $q->where('slug', '!=', 'closed'))->count();
        $health['support'] = $openTickets < 10 ? 'good' : ($openTickets < 25 ? 'warning' : 'critical');

        // Wallet health
        $pendingTransactions = Transaction::where('confirmed', false)->count();
        $health['wallets'] = $pendingTransactions < 5 ? 'good' : ($pendingTransactions < 15 ? 'warning' : 'critical');

        return $health;
    }

    private function getRecentActivities(): array
    {
        $activities = [];

        try {
            // Recent users
            $recentUsers = User::latest()->take(3)->get();
            foreach ($recentUsers as $user) {
                $activities[] = [
                    'type' => 'user',
                    'message' => "کاربر جدید: {$user->name}",
                    'time' => $user->created_at,
                    'icon' => 'heroicon-o-user-plus',
                ];
            }

            // Recent tickets
            $recentTickets = Ticket::latest()->take(3)->get();
            foreach ($recentTickets as $ticket) {
                $activities[] = [
                    'type' => 'ticket',
                    'message' => "تیکت جدید: {$ticket->subject}",
                    'time' => $ticket->created_at,
                    'icon' => 'heroicon-o-ticket',
                ];
            }

            // Recent transactions
            $recentTransactions = GatewayTransaction::latest()->take(3)->get();
            foreach ($recentTransactions as $transaction) {
                $activities[] = [
                    'type' => 'payment',
                    'message' => "تراکنش: " . number_format($transaction->total_amount) . " تومان",
                    'time' => $transaction->created_at,
                    'icon' => 'heroicon-o-banknotes',
                ];
            }

            // Sort by time
            usort($activities, function ($a, $b) {
                return $b['time'] <=> $a['time'];
            });

            return array_slice($activities, 0, 8);
        } catch (\Exception $e) {
            return [];
        }
    }
} 