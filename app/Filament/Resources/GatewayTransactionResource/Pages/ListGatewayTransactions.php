<?php

namespace App\Filament\Resources\GatewayTransactionResource\Pages;

use App\Filament\Resources\GatewayTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGatewayTransactions extends ListRecords
{
    protected static string $resource = GatewayTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('ایجاد تراکنش جدید'),
        ];
    }
} 