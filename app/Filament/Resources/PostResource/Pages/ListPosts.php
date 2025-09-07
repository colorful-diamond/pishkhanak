<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('تمامی مقالات'),
            'published' => Tab::make('منتشر شده')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'published')),
            'draft' => Tab::make('پیش‌نویس')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'draft')),
            'private' => Tab::make('خصوصی')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'private')),
            'featured' => Tab::make('ویژه')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('featured', true)),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'all';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PostResource\Widgets\PostOverview::class,
        ];
    }
}