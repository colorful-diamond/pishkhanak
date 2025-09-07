<?php

namespace App\Filament\Resources\AiSettingResource\Pages;

use App\Filament\Resources\AiSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAiSetting extends CreateRecord
{
    protected static string $resource = AiSettingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
