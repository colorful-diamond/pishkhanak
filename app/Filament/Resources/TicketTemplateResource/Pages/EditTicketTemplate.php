<?php

namespace App\Filament\Resources\TicketTemplateResource\Pages;

use App\Filament\Resources\TicketTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicketTemplate extends EditRecord
{
    protected static string $resource = TicketTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
