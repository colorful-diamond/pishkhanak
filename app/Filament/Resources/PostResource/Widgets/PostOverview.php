<?php

namespace App\Filament\Resources\PostResource\Widgets;

use App\Models\Post;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache; // Add this import

class PostOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            $this->getPublishedPostsStat(),
            $this->getDraftPostsStat(),
            $this->getFeaturedPostsStat(),
            $this->getMostViewedPostStat(),
            $this->getMostLikedPostStat(),
            $this->getMostSharedPostStat(),
            $this->getTotalPostsStat(),

            $this->getRecentPostsStat(),
            $this->getAveragePostLengthStat(),
        ];
    }

    protected function getTotalPostsStat(): Stat
    {

        $totalPosts = Cache::remember('total_posts', 180, function () {
            return Post::count();
        });
        $previousTotalPosts = Cache::remember('previous_total_posts', 180, function () {
            return Post::where('created_at', '<', now()->subMonth())->count();
        });

        $difference = $totalPosts - $previousTotalPosts;

        return Stat::make(__('filament-panels::resources/post.fields.total_posts'), $totalPosts)
            ->description($difference >= 0 ? "+$difference " . __('filament-panels::resources/post.fields.from_last_month') : "$difference " . __('filament-panels::resources/post.fields.from_last_month'))
            ->descriptionIcon($difference >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->color($difference >= 0 ? 'success' : 'danger');
    }

    protected function getPublishedPostsStat(): Stat
    {
        $publishedPosts = Cache::remember('published_posts', 180, function () {
            return Post::where('status', 'published')->count();
        });

        $totalPosts = Cache::remember('total_posts', 180, function () {
            return Post::count();
        });

        $percentage = $totalPosts > 0 ? round(($publishedPosts / $totalPosts) * 100, 2) : 0;

        return Stat::make(__('filament-panels::resources/post.fields.published_posts'), $publishedPosts)
            ->description("$percentage% " . __('filament-panels::resources/post.fields.of_total_posts'))
            ->color('success');
    }

    protected function getDraftPostsStat(): Stat
    {
        $draftPosts = Cache::remember('draft_posts', 180, function () {
            return Post::where('status', 'draft')->count();
        });

        $totalPosts = Cache::remember('total_posts', 180, function () {
            return Post::count();
        });

        $percentage = $totalPosts > 0 ? round(($draftPosts / $totalPosts) * 100, 2) : 0;

        return Stat::make(__('filament-panels::resources/post.fields.draft_posts'), $draftPosts)
            ->description("$percentage% " . __('filament-panels::resources/post.fields.of_total_posts'))
            ->color('warning');
    }

    protected function getFeaturedPostsStat(): Stat
    {
        $featuredPosts = Cache::remember('featured_posts', 180, function () {
            return Post::where('featured', true)->count();
        });

        $totalPosts = Cache::remember('total_posts', 180, function () {
            return Post::count();
        });

        $percentage = $totalPosts > 0 ? round(($featuredPosts / $totalPosts) * 100, 2) : 0;

        return Stat::make(__('filament-panels::resources/post.fields.featured_posts'), $featuredPosts)
            ->description("$percentage% " . __('filament-panels::resources/post.fields.of_total_posts'))
            ->color('primary');
    }

    protected function getMostViewedPostStat(): Stat
    {
        $mostViewedPost = Cache::remember('most_viewed_post', 180, function () {
            return Post::orderBy('views', 'desc')->first();
        });

        return Stat::make(__('filament-panels::resources/post.fields.most_viewed_post'), $mostViewedPost ? $mostViewedPost->title : __('filament-panels::resources/post.fields.na'))
            ->description($mostViewedPost ? "{$mostViewedPost->views} " . __('filament-panels::resources/post.fields.views') : '')
            ->color('info');
    }

    protected function getMostLikedPostStat(): Stat
    {
        $mostLikedPost = Cache::remember('most_liked_post', 180, function () {
            return Post::orderBy('likes', 'desc')->first();
        });

        return Stat::make(__('filament-panels::resources/post.fields.most_liked_post'), $mostLikedPost ? $mostLikedPost->title : __('filament-panels::resources/post.fields.na'))
            ->description($mostLikedPost ? "{$mostLikedPost->likes} " . __('filament-panels::resources/post.fields.likes') : '')
            ->color('success');
    }

    protected function getMostSharedPostStat(): Stat
    {
        $mostSharedPost = Cache::remember('most_shared_post', 180, function () {
            return Post::orderBy('shares', 'desc')->first();
        });

        return Stat::make(__('filament-panels::resources/post.fields.most_shared_post'), $mostSharedPost ? $mostSharedPost->title : __('filament-panels::resources/post.fields.na'))
            ->description($mostSharedPost ? "{$mostSharedPost->shares} " . __('filament-panels::resources/post.fields.shares') : '')
            ->color('warning');
    }

    protected function getRecentPostsStat(): Stat
    {
        $recentPosts = Cache::remember('recent_posts', 180, function () {
            return Post::where('created_at', '>=', now()->subDays(30))->count();
        });

        $previousMonthPosts = Cache::remember('previous_month_posts', 180, function () {
            return Post::whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])->count();
        });

        $difference = $recentPosts - $previousMonthPosts;

        return Stat::make(__('filament-panels::resources/post.fields.posts_last_30_days'), $recentPosts)
            ->description($difference >= 0 ? "+$difference " . __('filament-panels::resources/post.fields.from_previous_30_days') : "$difference " . __('filament-panels::resources/post.fields.from_previous_30_days'))
            ->descriptionIcon($difference >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->color($difference >= 0 ? 'success' : 'danger');
    }

    protected function getAveragePostLengthStat(): Stat
    {
        $averageLength = Cache::remember('average_post_length', 180, function () {
            return Post::whereNotNull('content')->avg(DB::raw('LENGTH(content)'));
        });

        $averageLength = $averageLength ? round($averageLength) : 0;

        return Stat::make(__('filament-panels::resources/post.fields.average_post_length'), "$averageLength " . __('filament-panels::resources/post.fields.characters'))
            ->description(__('filament-panels::resources/post.fields.based_on_content'))
            ->color('primary');
    }
}