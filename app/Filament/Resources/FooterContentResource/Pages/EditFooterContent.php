<?php

namespace App\Filament\Resources\FooterContentResource\Pages;

use App\Filament\Resources\FooterContentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFooterContent extends EditRecord
{
    protected static string $resource = FooterContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
