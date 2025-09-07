<?php

namespace App\Filament\Resources\FooterContentResource\Pages;

use App\Filament\Resources\FooterContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFooterContents extends ListRecords
{
    protected static string $resource = FooterContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
