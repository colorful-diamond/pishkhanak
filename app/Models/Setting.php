<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'is_public',
        'is_required',
        'sort_order',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_required' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get setting value by key with caching
     */
    public static function getValue(string $key, $default = null)
    {
        $cacheKey = "setting_{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set setting value by key
     */
    public static function setValue(string $key, $value): bool
    {
        $setting = static::where('key', $key)->first();
        
        if ($setting) {
            $setting->update(['value' => $value]);
        } else {
            $setting = static::create([
                'key' => $key,
                'value' => $value,
                'type' => 'text',
                'group' => 'general',
                'label' => ucfirst(str_replace('_', ' ', $key)),
            ]);
        }

        // Clear cache
        Cache::forget("setting_{$key}");
        
        return true;
    }

    /**
     * Get all settings by group
     */
    public static function getByGroup(string $group)
    {
        return static::where('group', $group)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get all public settings
     */
    public static function getPublicSettings()
    {
        return static::where('is_public', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache()
    {
        $settings = static::all();
        foreach ($settings as $setting) {
            Cache::forget("setting_{$setting->key}");
        }
    }

    /**
     * Boot method to clear cache when model is updated
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($setting) {
            Cache::forget("setting_{$setting->key}");
        });

        static::deleted(function ($setting) {
            Cache::forget("setting_{$setting->key}");
        });
    }
} 