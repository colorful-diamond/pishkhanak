<?php

namespace App\Filament\Resources\AutoResponseContextResource\Pages;

use App\Filament\Resources\AutoResponseContextResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAutoResponseContext extends EditRecord
{
    protected static string $resource = AutoResponseContextResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'PERSIAN_TEXT_c8748e19';
    }
}
