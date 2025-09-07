<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Tags\HasTags;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Services\AiService;
use App\Jobs\GenerateFinalContentJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use Illuminate\Bus\Batch;
use Throwable;

class Post extends Model implements HasMedia
{
    use HasFactory, HasSlug, HasTags, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'category_id',
        'summary',
        'description',
        'thumbnail',
        'images',
        'status',
        'published_at',
        'featured',
        'views',
        'likes',
        'shares',
        'author_id',

        //AI Generated Fields
        'ai_title',
        'ai_summary',
        'ai_description',
        'ai_thumbnail',
        'ai_images',
        'ai_headings',
        'ai_sections',
        'ai_content',

        // SEO Fields
        'meta',
        // Schema Fields
        'schema',
        'related_services',
        'faqs',
        'related_articles',
        'comments',
    ];


    //Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeLatestPublished($query)
    {
        return $query->published()->latest('published_at');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeWithViews($query)
    {
        return $query->withCount('views');
    }

    public function scopeWithComments($query)
    {
        return $query->withCount('comments');
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->preventOverwrite();
    }

    /**
     * Get the category that the post belongs to.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the author of the post.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function relatedPosts()
    {
        return $this->hasMany(Post::class, 'related_posts');
    }

    public function relatedServices()
    {
        return $this->hasMany(Service::class, 'related_services');
    }


    /**
     * Get the media for the post (thumbnail and images).
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')->singleFile();
        $this->addMediaCollection('images');
    }

    public function finishedAiContentGeneration()
    {
        $this->content = '';
        $sections = $this->ai_sections;
        ksort($sections);
        $this->ai_sections = $sections;
        foreach($sections as $section){
            $this->content .= $section;
        }
        $this->save();
        $this->generateFinalContent();
    }

    public function generateFinalContent()
    {
        $jobs = [];
        $jobs[] = new GenerateFinalContentJob($this->id , 'beginning');
        $jobs[] = new GenerateFinalContentJob($this->id , 'summary');
        $jobs[] = new GenerateFinalContentJob($this->id , 'schema');
        $jobs[] = new GenerateFinalContentJob($this->id , 'faqs');

        // Dispatch jobs in parallel and wait for all to complete
        $results = Bus::batch($jobs)->then(function (Batch $batch) {
            Log::info('All jobs in the Final Content batch completed successfully.');
            GenerateFinalContentJob::dispatch($this->id , 'meta');
        })->catch(function (Batch $batch, Throwable $e) {
            // First job failure detected
            Log::error('A job in the Final Content batch failed.', ['error' => $e->getMessage()]);
        })->finally(function (Batch $batch) {
            // The batch has finished executing
            Log::info('The Final Content batch has finished executing.');
        })->dispatch();
    }

    /**
     * Mutator and Accessor for ai_headings.
     */
    protected function aiHeadings(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => is_string($decoded = json_decode($value , true)) ? json_decode($decoded , true) : $decoded,
            set: fn ($value) => json_encode($value),
        );
    }

    /**
     * Mutator and Accessor for ai_sections.
     */
    protected function aiSections(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => is_string($decoded = json_decode($value , true)) ? json_decode($decoded , true) : $decoded,
            set: fn ($value) => json_encode($value),
        );
    }


    protected function images(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => is_string($decoded = json_decode($value , true)) ? json_decode($decoded , true) : $decoded,
            set: fn ($value) => json_encode($value),
        );
    }

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
}
