<?php

namespace App\Filament\Resources\FooterLinkResource\Pages;

use App\Filament\Resources\FooterLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFooterLinks extends ListRecords
{
    protected static string $resource = FooterLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
