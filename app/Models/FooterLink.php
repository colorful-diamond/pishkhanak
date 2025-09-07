<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class FooterLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'footer_section_id',
        'title',
        'url',
        'icon',
        'sort_order',
        'is_active',
        'open_in_new_tab',
        'target',
        'attributes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'open_in_new_tab' => 'boolean',
        'attributes' => 'array',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(FooterSection::class, 'footer_section_id');
    }

    // Cache methods
    public static function getCachedLinks(int $sectionId): \Illuminate\Database\Eloquent\Collection
    {
        $cacheKey = "footer_links_section_{$sectionId}";
        
        return Cache::remember($cacheKey, 3600, function () use ($sectionId) {
            return static::where('footer_section_id', $sectionId)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });
    }

    public static function clearCache(int $sectionId = null): void
    {
        if ($sectionId) {
            Cache::forget("footer_links_section_{$sectionId}");
        } else {
            // Clear all footer link caches
            $sections = FooterSection::pluck('id');
            foreach ($sections as $id) {
                Cache::forget("footer_links_section_{$id}");
            }
        }
    }

    protected static function booted(): void
    {
        static::saved(function ($link) {
            static::clearCache($link->footer_section_id);
            FooterSection::clearCache();
        });

        static::deleted(function ($link) {
            static::clearCache($link->footer_section_id);
            FooterSection::clearCache();
        });
    }
} 