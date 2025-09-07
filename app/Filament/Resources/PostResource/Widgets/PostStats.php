<?php

namespace App\Filament\Resources\PostResource\Widgets;

use App\Models\Post;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class PostStats extends BaseWidget
{
    public ?Post $record = null; // The specific post being edited

    protected function getStats(): array
    {
        return [
            $this->getViewsStat(),
            $this->getLikesStat(),
            $this->getSharesStat(),
            $this->getPublishedAtStat(),
        ];
    }

    protected function getViewsStat(): Stat
    {
        return Stat::make(__('filament-panels::resources/post.fields.views'), $this->record->views ?? 0)
            ->description(__('filament-panels::resources/post.fields.total_views'))
            ->color('info');
    }

    protected function getLikesStat(): Stat
    {
        return Stat::make(__('filament-panels::resources/post.fields.likes'), $this->record->likes ?? 0)
            ->description(__('filament-panels::resources/post.fields.total_likes'))
            ->color('success');
    }

    protected function getSharesStat(): Stat
    {
        return Stat::make(__('filament-panels::resources/post.fields.shares'), $this->record->shares ?? 0)
            ->description(__('filament-panels::resources/post.fields.total_shares'))
            ->color('warning');
    }

    protected function getPublishedAtStat(): Stat
    {
        $publishedAt = $this->record->published_at ? $this->record->published_at->toFormattedDateString() : __('filament-panels::resources/post.fields.not_published');
        
        return Stat::make(__('filament-panels::resources/post.fields.published_at'), $publishedAt)
            ->description(__('filament-panels::resources/post.fields.published_date'))
            ->color($this->record->published_at ? 'primary' : 'danger');
    }
}