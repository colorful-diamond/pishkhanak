<?php

namespace App\Filament\Resources\FooterLinkResource\Pages;

use App\Filament\Resources\FooterLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFooterLink extends EditRecord
{
    protected static string $resource = FooterLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
