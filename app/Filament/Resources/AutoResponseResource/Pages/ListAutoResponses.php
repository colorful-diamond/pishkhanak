<?php

namespace App\Filament\Resources\AutoResponseResource\Pages;

use App\Filament\Resources\AutoResponseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAutoResponses extends ListRecords
{
    protected static string $resource = AutoResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // You can add widgets here later if needed
        ];
    }

    public function getTitle(): string
    {
        if (request()->has('context')) {
            $contextName = \App\Models\AutoResponseContext::find(request('context'))?->name;
            return 'PERSIAN_TEXT_24755c5c' . ($contextName ?? 'PERSIAN_TEXT_84df55d3');
        }

        return parent::getTitle();
    }
}
