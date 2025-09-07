<?php

namespace App\Filament\Resources\TokenRefreshLogResource\Pages;

use App\Filament\Resources\TokenRefreshLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTokenRefreshLogs extends ListRecords
{
    protected static string $resource = TokenRefreshLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }


} 