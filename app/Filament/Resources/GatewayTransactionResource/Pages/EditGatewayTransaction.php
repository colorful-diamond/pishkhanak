<?php

namespace App\Filament\Resources\GatewayTransactionResource\Pages;

use App\Filament\Resources\GatewayTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGatewayTransaction extends EditRecord
{
    protected static string $resource = GatewayTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('حذف'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 