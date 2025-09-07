<?php

namespace App\Filament\Resources\AutoResponseResource\Pages;

use App\Filament\Resources\AutoResponseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAutoResponse extends EditRecord
{
    protected static string $resource = AutoResponseResource::class;

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
        return 'PERSIAN_TEXT_40d0a6e5';
    }
}
