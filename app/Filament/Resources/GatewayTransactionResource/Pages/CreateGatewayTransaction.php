<?php

namespace App\Filament\Resources\GatewayTransactionResource\Pages;

use App\Filament\Resources\GatewayTransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGatewayTransaction extends CreateRecord
{
    protected static string $resource = GatewayTransactionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 