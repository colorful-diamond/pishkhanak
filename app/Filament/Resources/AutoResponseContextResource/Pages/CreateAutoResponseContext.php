<?php

namespace App\Filament\Resources\AutoResponseContextResource\Pages;

use App\Filament\Resources\AutoResponseContextResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAutoResponseContext extends CreateRecord
{
    protected static string $resource = AutoResponseContextResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'زمینه پاسخ خودکار با موفقیت ایجاد شد';
    }
}
