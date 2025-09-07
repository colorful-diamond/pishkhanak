<?php

namespace App\Filament\Resources\PaymentSourceResource\Pages;

use App\Filament\Resources\PaymentSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ListPaymentSources extends ListRecords
{
    protected static string $resource = PaymentSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_csv')
                ->label('خروجی CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    // You can implement CSV export functionality here
                    $this->notify('success', 'CSV export functionality coming soon!');
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('همه سرویس‌ها'),
            
            'high_revenue' => Tab::make('درآمد بالا')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->having('total_revenue', '>', 1000000); // Greater than 1M IRT
                }),
                
            'popular' => Tab::make('پربازدید')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->having('results_count', '>', 100); // More than 100 uses
                }),
                
            'gateway_preferred' => Tab::make('ترجیح درگاه')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->having('gateway_payments_count', '>', DB::raw('wallet_payments_count'));
                }),
                
            'wallet_preferred' => Tab::make('ترجیح کیف‌پول')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->having('wallet_payments_count', '>', DB::raw('gateway_payments_count'));
                }),
        ];
    }
} 