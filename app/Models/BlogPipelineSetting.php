<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BlogPipelineSetting extends Model
{
    use HasFactory;

    protected $table = 'blog_pipeline_settings';

    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    // Cache duration in seconds (1 hour)
    const CACHE_DURATION = 3600;

    // Setting keys
    const KEY_DAILY_PUBLISH_LIMIT = 'daily_publish_limit';
    const KEY_AI_PROCESSING_CONFIG = 'ai_processing_config';
    const KEY_PUBLISHING_SCHEDULE = 'publishing_schedule';
    const KEY_QUALITY_THRESHOLDS = 'quality_thresholds';

    // Get a setting value with caching
    public static function get(string $key, $default = null)
    {
        return Cache::remember("blog_pipeline_setting_{$key}", self::CACHE_DURATION, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    // Set a setting value and clear cache
    public static function set(string $key, $value, ?string $description = null): self
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'description' => $description ?? self::where('key', $key)->first()?->description,
            ]
        );

        Cache::forget("blog_pipeline_setting_{$key}");
        
        return $setting;
    }

    // Clear all settings cache
    public static function clearCache(): void
    {
        $settings = self::all();
        foreach ($settings as $setting) {
            Cache::forget("blog_pipeline_setting_{$setting->key}");
        }
    }

    // Helper methods for specific settings
    public static function getDailyPublishLimit(): int
    {
        $setting = self::get(self::KEY_DAILY_PUBLISH_LIMIT, ['limit' => 100]);
        return $setting['limit'] ?? 100;
    }

    public static function getAiProcessingConfig(): array
    {
        return self::get(self::KEY_AI_PROCESSING_CONFIG, [
            'model' => 'google/gemini-2.5-pro',
            'temperature' => 0.7,
            'max_tokens' => 4000,
            'parallel_jobs' => 5,
            'retry_attempts' => 3,
        ]);
    }

    public static function getPublishingSchedule(): array
    {
        return self::get(self::KEY_PUBLISHING_SCHEDULE, [
            'enabled' => true,
            'times' => ['08:00', '12:00', '16:00', '20:00'],
            'timezone' => 'Asia/Tehran',
            'posts_per_batch' => 25,
        ]);
    }

    public static function getQualityThresholds(): array
    {
        return self::get(self::KEY_QUALITY_THRESHOLDS, [
            'min_score_for_auto_publish' => 0.7,
            'min_content_length' => 500,
            'max_content_length' => 10000,
            'require_review_below_score' => 0.5,
        ]);
    }

    public static function isPublishingEnabled(): bool
    {
        $schedule = self::getPublishingSchedule();
        return $schedule['enabled'] ?? true;
    }

    public static function getMinQualityScore(): float
    {
        $thresholds = self::getQualityThresholds();
        return $thresholds['min_score_for_auto_publish'] ?? 0.7;
    }
}