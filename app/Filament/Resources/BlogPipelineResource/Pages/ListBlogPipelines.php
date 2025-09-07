<?php

namespace App\Filament\Resources\BlogPipelineResource\Pages;

use App\Filament\Resources\BlogPipelineResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBlogPipelines extends ListRecords
{
    protected static string $resource = BlogPipelineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}