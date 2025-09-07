<?php

namespace App\Filament\Resources\BlogPipelineResource\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class PostDetailsWidget extends Widget
{
    protected static string $view = 'filament.resources.blog-pipeline.widgets.post-details';

    public ?Model $record = null;

    protected function getViewData(): array
    {
        return [
            'record' => $this->record,
        ];
    }
}