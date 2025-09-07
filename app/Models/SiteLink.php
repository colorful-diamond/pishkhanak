<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'location',
        'icon',
        'sort_order',
        'is_active',
        'open_in_new_tab',
        'target',
        'attributes',
        'css_class',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'open_in_new_tab' => 'boolean',
        'attributes' => 'array',
    ];

    // Cache methods
    public static function getCachedLinks(string $location): \Illuminate\Database\Eloquent\Collection
    {
        $cacheKey = "site_links_{$location}";
        
        return Cache::remember($cacheKey, 3600, function () use ($location) {
            return static::where('location', $location)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });
    }

    public static function clearCache(string $location = null): void
    {
        if ($location) {
            Cache::forget("site_links_{$location}");
        } else {
            // Clear all site link caches
            $locations = ['header', 'sidebar', 'footer', 'mobile_nav'];
            foreach ($locations as $loc) {
                Cache::forget("site_links_{$loc}");
            }
        }
    }

    protected static function booted(): void
    {
        static::saved(function ($link) {
            static::clearCache($link->location);
        });

        static::deleted(function ($link) {
            static::clearCache($link->location);
        });
    }
} 