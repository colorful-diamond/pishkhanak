<?php

namespace App\Filament\Resources\AutoResponseContextResource\Pages;

use App\Filament\Resources\AutoResponseContextResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAutoResponseContext extends ViewRecord
{
    protected static string $resource = AutoResponseContextResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
