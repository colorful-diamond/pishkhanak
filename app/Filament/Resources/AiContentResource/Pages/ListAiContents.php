<?php

namespace App\Filament\Resources\AiContentResource\Pages;

use App\Filament\Resources\AiContentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAiContents extends ListRecords
{
    protected static string $resource = AiContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}