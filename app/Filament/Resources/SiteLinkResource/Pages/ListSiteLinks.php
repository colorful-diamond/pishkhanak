<?php

namespace App\Filament\Resources\SiteLinkResource\Pages;

use App\Filament\Resources\SiteLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSiteLinks extends ListRecords
{
    protected static string $resource = SiteLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
