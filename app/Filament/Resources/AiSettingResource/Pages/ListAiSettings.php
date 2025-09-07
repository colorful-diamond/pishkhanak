<?php

namespace App\Filament\Resources\AiSettingResource\Pages;

use App\Filament\Resources\AiSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAiSettings extends ListRecords
{
    protected static string $resource = AiSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
