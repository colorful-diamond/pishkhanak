<?php

namespace App\Filament\Resources\AiContentResource\Pages;

use App\Filament\Resources\AiContentResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateAiContent extends CreateRecord
{
    protected static string $resource = AiContentResource::class;

    protected function getActions(): array
    {
        return [
            Action::make('cancel')
                ->label('Cancel')
                ->color('gray')
                ->action(fn () => $this->redirect($this->getResource()::getUrl('index'))),
        ];
    }
}
