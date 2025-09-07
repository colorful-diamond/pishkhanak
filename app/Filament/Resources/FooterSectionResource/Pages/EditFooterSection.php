<?php

namespace App\Filament\Resources\FooterSectionResource\Pages;

use App\Filament\Resources\FooterSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFooterSection extends EditRecord
{
    protected static string $resource = FooterSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
