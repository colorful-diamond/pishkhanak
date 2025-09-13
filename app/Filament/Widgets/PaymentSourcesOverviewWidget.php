<?php

namespace App\Filament\Widgets;

use App\Models\Service;
use App\Models\ServiceResult;
use App\Models\GatewayTransaction;
use Bavix\Wallet\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class PaymentSourcesOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Get services with payment activity
        $servicesWithPayments = Service::whereHas('results')->count();
        
        // Get total service revenue (estimated)
        $totalServiceRevenue = ServiceResult::join('services', 'service_results.service_id', '=', 'services.id')
            ->where('service_results.status', 'success')
            ->sum('services.price');
            
        // Get gateway payments for services
        $gatewayServiceRevenue = GatewayTransaction::where('status', 'completed')
            ->whereRaw("metadata->>'service_id' IS NOT NULL")
            ->sum('amount');
            
        // Get wallet payments for services
        $walletServiceRevenue = Transaction::where('confirmed', true)
            ->where('type', 'withdraw')
            ->whereRaw("meta->>'service_id' IS NOT NULL")
            ->sum('amount');
            
        // Get most popular service (combining both service_requests and service_results)
        $mostPopularService = Service::select([
                'services.*', 
                DB::raw('(
                    (SELECT COUNT(*) FROM service_requests WHERE service_requests.service_id = services.id) +
                    (SELECT COUNT(*) FROM service_results WHERE service_results.service_id = services.id)
                ) as usage_count')
            ])
            ->orderByDesc('usage_count')
            ->first();
            
        // Get payment method distribution
        $gatewayPaymentsCount = GatewayTransaction::where('status', 'completed')
            ->whereRaw("metadata->>'service_id' IS NOT NULL")
            ->count();
            
        $walletPaymentsCount = Transaction::where('confirmed', true)
            ->where('type', 'withdraw')
            ->whereRaw("meta->>'service_id' IS NOT NULL")
            ->count();

        return [
            Stat::make('سرویس‌های فعال', $servicesWithPayments)
                ->description('سرویس‌هایی که پرداخت داشته‌اند')
                ->descriptionIcon('heroicon-m-cog')
                ->color('primary'),

            Stat::make('کل درآمد سرویس‌ها', number_format($totalServiceRevenue) . ' تومان')
                ->description('مجموع درآمد از همه سرویس‌ها')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('درآمد درگاه پرداخت', number_format($gatewayServiceRevenue) . ' تومان')
                ->description('درآمد از طریق درگاه‌های پرداخت')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('info'),

            Stat::make('درآمد کیف‌پول', number_format(abs($walletServiceRevenue)) . ' تومان')
                ->description('درآمد از طریق کیف‌پول کاربران')
                ->descriptionIcon('heroicon-m-wallet')
                ->color('warning'),

            Stat::make('محبوب‌ترین سرویس', $mostPopularService ? $mostPopularService->title : 'ندارد')
                ->description($mostPopularService ? "استفاده: {$mostPopularService->usage_count} بار" : 'هیچ سرویسی استفاده نشده')
                ->descriptionIcon('heroicon-m-star')
                ->color('primary'),

            Stat::make('ترجیح پرداخت', $gatewayPaymentsCount > $walletPaymentsCount ? 'درگاه پرداخت' : 'کیف‌پول')
                ->description("درگاه: {$gatewayPaymentsCount} | کیف‌پول: {$walletPaymentsCount}")
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($gatewayPaymentsCount > $walletPaymentsCount ? 'success' : 'warning'),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }

    public function getDisplayName(): string
    {
        return 'نمای کلی منابع پرداخت';
    }
} 