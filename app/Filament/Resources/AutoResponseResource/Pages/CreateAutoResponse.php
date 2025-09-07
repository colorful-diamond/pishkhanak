<?php

namespace App\Filament\Resources\AutoResponseResource\Pages;

use App\Filament\Resources\AutoResponseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAutoResponse extends CreateRecord
{
    protected static string $resource = AutoResponseResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'PERSIAN_TEXT_070e96dd';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set context_id from URL if present
        if (request()->has('context')) {
            $data['context_id'] = request('context');
        }

        return $data;
    }
}
