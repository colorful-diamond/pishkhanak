<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ServiceCategory extends Model implements HasMedia
{
    use HasFactory, HasSlug, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'background_color',
        'border_color',
        'icon_color',
        'hover_border_color',
        'hover_background_color',
        'background_icon',
        'display_order',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * Get the posts for the category.
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'category_id');
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc');
    }

    /**
     * Register media collections for the category.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('background_image')->singleFile();
    }

    /**
     * Check if the category has a background (either image or SVG icon).
     */
    public function hasBackground()
    {
        return $this->hasMedia('background_image') || !empty($this->background_icon);
    }

    /**
     * Get the background type (image, svg, or none).
     */
    public function getBackgroundType()
    {
        if ($this->hasMedia('background_image')) {
            return 'image';
        } elseif (!empty($this->background_icon)) {
            return 'svg';
        }
        return 'none';
    }
}