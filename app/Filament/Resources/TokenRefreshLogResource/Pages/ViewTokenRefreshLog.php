<?php

namespace App\Filament\Resources\TokenRefreshLogResource\Pages;

use App\Filament\Resources\TokenRefreshLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTokenRefreshLog extends ViewRecord
{
    protected static string $resource = TokenRefreshLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
} 