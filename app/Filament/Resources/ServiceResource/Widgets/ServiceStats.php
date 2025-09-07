<?php

namespace App\Filament\Resources\ServiceResource\Widgets;

use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class ServiceStats extends BaseWidget
{
    public ?Service $record = null; // The specific Service being edited

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
        return Stat::make(__('filament-panels::resources/service.fields.views'), $this->record->views ?? 0)
            ->description(__('filament-panels::resources/service.fields.total_views'))
            ->color('info');
    }

    protected function getLikesStat(): Stat
    {
        return Stat::make(__('filament-panels::resources/service.fields.likes'), $this->record->likes ?? 0)
            ->description(__('filament-panels::resources/service.fields.total_likes'))
            ->color('success');
    }

    protected function getSharesStat(): Stat
    {
        return Stat::make(__('filament-panels::resources/service.fields.shares'), $this->record->shares ?? 0)
            ->description(__('filament-panels::resources/service.fields.total_shares'))
            ->color('warning');
    }

    protected function getPublishedAtStat(): Stat
    {
        $publishedAt = $this->record->published_at ? $this->record->published_at->toFormattedDateString() : __('filament-panels::resources/service.fields.not_published');
        
        return Stat::make(__('filament-panels::resources/service.fields.published_at'), $publishedAt)
            ->description(__('filament-panels::resources/service.fields.published_date'))
            ->color($this->record->published_at ? 'primary' : 'danger');
    }
}