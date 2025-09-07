<?php

namespace App\Filament\Resources\ServiceResource\Widgets;

use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache; // Add this import

class ServiceOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            $this->getPublishedServicesStat(),
            $this->getDraftServicesStat(),
            $this->getFeaturedServicesStat(),
            $this->getTotalServicesStat(),
            $this->getRecentServicesStat(),
            $this->getAverageServiceLengthStat(),
        ];
    }

    protected function getTotalServicesStat(): Stat
    {

        $totalServices = Cache::remember('total_services', 180, function () {
            return Service::count();
        });
        $previousTotalServices = Cache::remember('previous_total_services', 180, function () {
            return Service::where('created_at', '<', now()->subMonth())->count();
        });

        $difference = $totalServices - $previousTotalServices;

        return Stat::make(__('filament-panels::resources/service.fields.total_services'), $totalServices)
            ->description($difference >= 0 ? "+$difference " . __('filament-panels::resources/service.fields.from_last_month') : "$difference " . __('filament-panels::resources/service.fields.from_last_month'))
            ->descriptionIcon($difference >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->color($difference >= 0 ? 'success' : 'danger');
    }

    protected function getPublishedServicesStat(): Stat
    {
        $publishedServices = Cache::remember('published_services', 180, function () {
            return Service::where('status', 'published')->count();
        });

        $totalServices = Cache::remember('total_services', 180, function () {
            return Service::count();
        });

        $percentage = $totalServices > 0 ? round(($publishedServices / $totalServices) * 100, 2) : 0;

        return Stat::make(__('filament-panels::resources/service.fields.published_services'), $publishedServices)
            ->description("$percentage% " . __('filament-panels::resources/service.fields.of_total_services'))
            ->color('success');
    }

    protected function getDraftServicesStat(): Stat
    {
        $draftServices = Cache::remember('draft_services', 180, function () {
            return Service::where('status', 'draft')->count();
        });

        $totalServices = Cache::remember('total_services', 180, function () {
            return Service::count();
        });

        $percentage = $totalServices > 0 ? round(($draftServices / $totalServices) * 100, 2) : 0;

        return Stat::make(__('filament-panels::resources/service.fields.draft_services'), $draftServices)
            ->description("$percentage% " . __('filament-panels::resources/service.fields.of_total_services'))
            ->color('warning');
    }

    protected function getFeaturedServicesStat(): Stat
    {
        $featuredServices = Cache::remember('featured_services', 180, function () {
            return Service::where('featured', true)->count();
        });

        $totalServices = Cache::remember('total_services', 180, function () {
            return Service::count();
        });

        $percentage = $totalServices > 0 ? round(($featuredServices / $totalServices) * 100, 2) : 0;

        return Stat::make(__('filament-panels::resources/service.fields.featured_services'), $featuredServices)
            ->description("$percentage% " . __('filament-panels::resources/service.fields.of_total_services'))
            ->color('primary');
    }
    protected function getRecentServicesStat(): Stat
    {
        $recentServices = Cache::remember('recent_services', 180, function () {
            return Service::where('created_at', '>=', now()->subDays(30))->count();
        });

        $previousMonthServices = Cache::remember('previous_month_services', 180, function () {
            return Service::whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])->count();
        });

        $difference = $recentServices - $previousMonthServices;

        return Stat::make(__('filament-panels::resources/service.fields.services_last_30_days'), $recentServices)
            ->description($difference >= 0 ? "+$difference " . __('filament-panels::resources/service.fields.from_previous_30_days') : "$difference " . __('filament-panels::resources/service.fields.from_previous_30_days'))
            ->descriptionIcon($difference >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->color($difference >= 0 ? 'success' : 'danger');
    }

    protected function getAverageServiceLengthStat(): Stat
    {
        $averageLength = Cache::remember('average_service_length', 180, function () {
            return Service::whereNotNull('content')->avg(DB::raw('LENGTH(content)'));
        });

        $averageLength = $averageLength ? round($averageLength) : 0;

        return Stat::make(__('filament-panels::resources/service.fields.average_service_length'), "$averageLength " . __('filament-panels::resources/service.fields.characters'))
            ->description(__('filament-panels::resources/service.fields.based_on_content'))
            ->color('primary');
    }
}