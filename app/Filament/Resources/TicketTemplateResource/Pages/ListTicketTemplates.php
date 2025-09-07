<?php

namespace App\Filament\Resources\TicketTemplateResource\Pages;

use App\Filament\Resources\TicketTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTicketTemplates extends ListRecords
{
    protected static string $resource = TicketTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
