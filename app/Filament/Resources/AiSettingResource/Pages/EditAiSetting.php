<?php

namespace App\Filament\Resources\AiSettingResource\Pages;

use App\Filament\Resources\AiSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAiSetting extends EditRecord
{
    protected static string $resource = AiSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
