<?php

namespace App\Filament\Resources\GatewayTransactionResource\Pages;

use App\Filament\Resources\GatewayTransactionResource;
use App\Models\GatewayTransaction;
use Filament\Resources\Pages\Page;

class ViewGatewayTransactionLogs extends Page
{
    protected static string $resource = GatewayTransactionResource::class;

    protected static string $view = 'filament.resources.gateway-transaction-resource.pages.view-gateway-transaction-logs';

    public GatewayTransaction $record;

    public function mount(int | string $record): void
    {
        $this->record = GatewayTransaction::findOrFail($record);
    }

    public function getTitle(): string
    {
        return "لاگ‌های تراکنش: {$this->record->uuid}";
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('back')
                ->label('بازگشت')
                ->url($this->getResource()::getUrl('view', ['record' => $this->record]))
                ->icon('heroicon-m-arrow-left'),
        ];
    }
} 