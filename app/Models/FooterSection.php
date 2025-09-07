<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class FooterSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'icon',
        'sort_order',
        'is_active',
        'location',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public function links(): HasMany
    {
        return $this->hasMany(FooterLink::class)->orderBy('sort_order');
    }

    public function activeLinks(): HasMany
    {
        return $this->hasMany(FooterLink::class)->where('is_active', true)->orderBy('sort_order');
    }

    // Cache methods
    public static function getCachedSections(string $location = 'footer'): \Illuminate\Database\Eloquent\Collection
    {
        $cacheKey = "footer_sections_{$location}";
        
        return Cache::remember($cacheKey, 3600, function () use ($location) {
            return static::where('location', $location)
                ->where('is_active', true)
                ->with(['activeLinks'])
                ->orderBy('sort_order')
                ->get();
        });
    }

    public static function clearCache(string $location = null): void
    {
        if ($location) {
            Cache::forget("footer_sections_{$location}");
        } else {
            // Clear all footer section caches
            Cache::forget('footer_sections_footer');
            Cache::forget('footer_sections_sidebar');
            Cache::forget('footer_sections_header');
        }
    }

    protected static function booted(): void
    {
        static::saved(function ($section) {
            static::clearCache($section->location);
        });

        static::deleted(function ($section) {
            static::clearCache($section->location);
        });
    }
} 