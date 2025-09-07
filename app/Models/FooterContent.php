<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class FooterContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'section',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    // Cache methods
    public static function getCachedContent(string $key = null, string $section = null): mixed
    {
        if ($key) {
            $cacheKey = "footer_content_{$key}";
            return Cache::remember($cacheKey, 3600, function () use ($key) {
                return static::where('key', $key)
                    ->where('is_active', true)
                    ->value('value');
            });
        }

        if ($section) {
            $cacheKey = "footer_content_section_{$section}";
            return Cache::remember($cacheKey, 3600, function () use ($section) {
                return static::where('section', $section)
                    ->where('is_active', true)
                    ->pluck('value', 'key');
            });
        }

        $cacheKey = 'footer_content_all';
        return Cache::remember($cacheKey, 3600, function () {
            return static::where('is_active', true)
                ->pluck('value', 'key');
        });
    }

    public static function clearCache(string $key = null, string $section = null): void
    {
        if ($key) {
            Cache::forget("footer_content_{$key}");
        } elseif ($section) {
            Cache::forget("footer_content_section_{$section}");
        } else {
            Cache::forget('footer_content_all');
        }
    }

    protected static function booted(): void
    {
        static::saved(function ($content) {
            static::clearCache($content->key, $content->section);
        });

        static::deleted(function ($content) {
            static::clearCache($content->key, $content->section);
        });
    }
} 