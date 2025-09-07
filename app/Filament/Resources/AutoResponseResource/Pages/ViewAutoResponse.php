<?php

namespace App\Filament\Resources\AutoResponseResource\Pages;

use App\Filament\Resources\AutoResponseResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAutoResponse extends ViewRecord
{
    protected static string $resource = AutoResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
