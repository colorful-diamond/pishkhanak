<?php

namespace App\Filament\Resources\SiteLinkResource\Pages;

use App\Filament\Resources\SiteLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSiteLink extends EditRecord
{
    protected static string $resource = SiteLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
