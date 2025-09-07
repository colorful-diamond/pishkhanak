<?php

namespace App\Filament\Resources\AutoResponseContextResource\Pages;

use App\Filament\Resources\AutoResponseContextResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAutoResponseContexts extends ListRecords
{
    protected static string $resource = AutoResponseContextResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
