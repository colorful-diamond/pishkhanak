<?php

namespace App\Filament\Resources\BlogPipelineResource\Pages;

use App\Filament\Resources\BlogPipelineResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlogPipeline extends EditRecord
{
    protected static string $resource = BlogPipelineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}