<?php

namespace App\Filament\Resources\PaymentSourceResource\Pages;

use App\Filament\Resources\PaymentSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPaymentSource extends ViewRecord
{
    protected static string $resource = PaymentSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view_service_results')
                ->label('مشاهده نتایج سرویس')
                ->icon('heroicon-o-eye')
                ->url(fn (): string => route('filament.access.resources.service-results.index', [
                    'tableFilters[service][value]' => $this->record->id
                ]))
                ->openUrlInNewTab(),

            Actions\Action::make('view_transactions')
                ->label('مشاهده تراکنش‌ها')
                ->icon('heroicon-o-banknotes')
                ->url(fn (): string => route('filament.access.resources.wallet-transactions.index', [
                    'tableFilters[service_id][value]' => $this->record->id
                ]))
                ->openUrlInNewTab(),

            Actions\Action::make('view_gateway_transactions')
                ->label('مشاهده پرداخت‌های درگاه')
                ->icon('heroicon-o-credit-card')
                ->url(fn (): string => route('filament.access.resources.gateway-transactions.index', [
                    'tableFilters[service_id][value]' => $this->record->id
                ]))
                ->openUrlInNewTab(),
        ];
    }
} 