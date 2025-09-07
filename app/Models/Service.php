<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;

class Service extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasTags;

    /**
     * The relationships that should always be loaded.
     * This helps prevent N+1 queries by eager loading commonly used relationships
     */
    protected $with = ['category', 'author'];



    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'short_title',
        'slug',
        'content',
        'category_id',
        'author_id',
        'summary',
        'description',
        'explanation',
        'status',
        'featured',
        'parent_id',

        // Cost and Price
        'price',
        'cost', // Cost to run the service (API cost)
        'is_paid', // Whether this service requires payment
        'currency', // Currency for pricing (IRT, USD, etc.)
        'hidden_fields', // Fields to hide from service results

        // Media
        'icon',

        // SEO Fields
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        // Schema Fields
        'schema',
        'faqs',
        'related_articles',
        'comment_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
        'featured' => 'boolean',
        'is_paid' => 'boolean',
        'price' => 'integer',
        'cost' => 'integer',
        'hidden_fields' => 'array',
        'schema' => 'array',
        'faqs' => 'array',
        'related_articles' => 'array',
        'comment_status' => 'boolean',
    ];


    //Scopes

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }


    public function scopeWithComments($query)
    {
        return $query->withCount('comments');
    }

    /**
     * Get the category that the post belongs to.
     */
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    /**
     * Get the author of the post.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the parent of the service.
     */
    public function parent()
    {
        return $this->belongsTo(Service::class, 'parent_id');
    }

    /**
     * Get the children of the service.
     */
    public function children()
    {
        return $this->hasMany(Service::class, 'parent_id');
    }

    /**
     * Get the results for this service.
     */
    public function results()
    {
        return $this->hasMany(ServiceResult::class);
    }

    /**
     * Get the full URL for the service.
     * Optimized to prevent N+1 queries by using lazy eager loading if needed
     *
     * @return string
     */
    public function getUrl()
    {
        if ($this->parent_id) {
            // Lazy eager load parent if not already loaded
            if (!$this->relationLoaded('parent')) {
                $this->load('parent');
            }
            
            if ($this->parent) {
                return route('services.show', ['slug1' => $this->parent->slug, 'slug2' => $this->slug]);
            }
        }
        
        return route('services.show', ['slug1' => $this->slug]);
    }

    /**
     * Get the display title (short_title if available, otherwise title)
     *
     * @return string
     */
    public function getDisplayTitle()
    {
        return $this->short_title ?: $this->title;
    }

    /**
     * Find service by slug with proper parent-child handling
     * This fixes the sub-service slug duplication issue
     *
     * @param string $slug
     * @param int|null $parentId
     * @return Service|null
     */
    public static function findBySlugSafely(string $slug, ?int $parentId = null): ?Service
    {
        return static::where('slug', $slug)
            ->where('parent_id', $parentId)
            ->where('status', 'active')
            ->first();
    }

    /**
     * Find service by slug, trying both parent and child services
     * This is a fallback method for cases where parent context is unknown
     *
     * @param string $slug
     * @return Service|null
     */
    public static function findBySlugWithFallback(string $slug): ?Service
    {
        // First try to find as main service (parent_id = null)
        $service = static::where('slug', $slug)
            ->whereNull('parent_id')
            ->where('status', 'active')
            ->first();
            
        if ($service) {
            return $service;
        }
        
        // If not found, try to find as sub-service and return first match
        // Note: This is not ideal but better than the current method
        return static::where('slug', $slug)
            ->whereNotNull('parent_id')
            ->where('status', 'active')
            ->first();
    }

    /**
     * Get the media for the post (thumbnail, icon, and images).
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')->singleFile();
        $this->addMediaCollection('icon')->singleFile();
        $this->addMediaCollection('images');
    }

    /**
     * Mutator and Accessor for ai_headings.
     */

    protected function schema(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => is_string($decoded = json_decode($value , true)) ? json_decode($decoded , true) : $decoded,
            set: fn ($value) => json_encode($value),
        );
    }

    protected function faqs(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => is_string($decoded = json_decode($value , true)) ? json_decode($decoded , true) : $decoded,
            set: fn ($value) => json_encode($value),
        );
    }

    protected function relatedArticles(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => is_string($decoded = json_decode($value , true)) ? json_decode($decoded , true) : $decoded,
            set: fn ($value) => json_encode($value),
        );
    }

    protected function comments(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => is_string($decoded = json_decode($value , true)) ? json_decode($decoded , true) : $decoded,
            set: fn ($value) => json_encode($value),
        );
    }

    /**
     * Store the original content value temporarily
     */
    protected $originalContentValue = null;

    /**
     * Get hidden fields for this service
     *
     * @return array
     */
    public function getHiddenFields(): array
    {
        return $this->hidden_fields ?? [];
    }

    /**
     * Check if a field should be hidden
     *
     * @param string $fieldName
     * @return bool
     */
    public function isFieldHidden(string $fieldName): bool
    {
        return in_array($fieldName, $this->getHiddenFields());
    }

    /**
     * Get the rendered content (either AI content or regular content)
     */
    public function getContentAttribute($value)
    {
        // Store original value in a non-database property
        $this->originalContentValue = $value;
        
        // If content is numeric, try to load AI content
        if (is_numeric($value)) {
            $aiContent = \App\Models\AiContent::find($value);
            if ($aiContent) {
                return $aiContent->getFullContent();
            }
        }
        
        // Return original content if not numeric or AI content not found
        return $value;
    }

    /**
     * Get the AI content relation if content is numeric
     */
    public function aiContent()
    {
        // Check if content is numeric
        $rawContent = $this->getRawContent();
        if (is_numeric($rawContent)) {
            return $this->belongsTo(\App\Models\AiContent::class, 'content');
        }
        
        return null;
    }

    /**
     * Check if this service uses AI content
     */
    public function hasAiContent()
    {
        $rawContent = $this->getRawContent();
        return is_numeric($rawContent);
    }

    /**
     * Get the raw content value (before processing)
     */
    public function getRawContent()
    {
        return $this->originalContentValue ?? $this->getRawOriginal('content');
    }

    /**
     * Get rendered content (either AI content or regular content)
     */
    public function getRenderedContent()
    {
        if ($this->hasAiContent()) {
            $aiContent = $this->aiContent();
            if ($aiContent) {
                return $aiContent->getFullContent();
            }
        }
        
        return $this->getRawContent();
    }

    public function getExplanation()
    {
        if($this->parent_id){
            // Lazy eager load parent if not already loaded
            if (!$this->relationLoaded('parent')) {
                $this->load('parent');
            }
            return $this->parent->explanation ?? $this->explanation;
        }
        return $this->explanation;
    }
}
