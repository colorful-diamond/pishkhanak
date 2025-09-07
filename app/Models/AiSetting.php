<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class AiSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'model_config',
        'generation_settings',
        'prompt_templates',
        'language_settings',
        'tone_settings',
        'content_formats',
        'target_audiences',
        'custom_instructions',
        'max_tokens',
        'temperature',
        'frequency_penalty',
        'presence_penalty',
        'stop_sequences',
        'ordering',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'model_config' => 'array',
        'generation_settings' => 'array',
        'prompt_templates' => 'array',
        'language_settings' => 'array',
        'tone_settings' => 'array',
        'content_formats' => 'array',
        'target_audiences' => 'array',
        'custom_instructions' => 'array',
        'stop_sequences' => 'array',
    ];

    // Get the formatted model configuration
    public function getFormattedModelConfig(): array
    {
        return $this->model_config ?? [
            'Posts' => [
                'model' => \App\Models\Post::class,
                'title_field' => 'title',
                'description_field' => 'description',
                'searchable_fields' => ['title', 'slug'],
            ],
            'Services' => [
                'model' => \LaraZeus\Bolt\Models\Form::class,
                'title_field' => 'name',
                'description_field' => 'short_description',
                'searchable_fields' => ['name', 'slug'],
            ],
            'Service Categories' => [
                'model' => \LaraZeus\Bolt\Models\Category::class,
                'title_field' => 'title',
                'description_field' => 'description',
                'searchable_fields' => ['title', 'slug'],
            ],
            'News' => [
                'model' => \App\Models\News::class,
                'title_field' => 'title',
                'description_field' => 'content',
                'searchable_fields' => ['title', 'slug'],
            ],
        ];
    }

    // Get default generation settings
    public function getDefaultGenerationSettings(): array
    {
        return [
            'headings_number' => 8,
            'sub_headings_number' => 3,
            'min_words_per_section' => 200,
            'max_words_per_section' => 500,
            'include_faq' => true,
            'faq_count' => 5,
            'include_meta' => true,
            'include_schema' => true,
            'include_summary' => true,
            'enable_auto_save' => true,
            'auto_save_interval' => 300, // 5 minutes
        ];
    }

    // Get default prompt templates
    public function getDefaultPromptTemplates(): array
    {
        return [
            'heading_generation' => 'Generate {count} engaging headings for an article about {topic} targeting {audience} with a {tone} tone.',
            'section_generation' => 'Write a detailed section about {topic} with {tone} tone for {audience}. Include key points and examples.',
            'summary_generation' => 'Create a concise summary of the article about {topic}, highlighting the main points and key takeaways.',
            'faq_generation' => 'Generate {count} frequently asked questions and detailed answers about {topic} that {audience} might ask.',
            'meta_generation' => 'Create SEO-optimized meta title and description for an article about {topic} targeting {audience}.',
        ];
    }

    // Get supported languages
    public function getDefaultLanguageSettings(): array
    {
        return [
            'supported_languages' => [
                'en' => 'English',
                'es' => 'Spanish',
                'fr' => 'French',
                'de' => 'German',
                'it' => 'Italian',
                'pt' => 'Portuguese',
                'ru' => 'Russian',
                'ar' => 'Arabic',
                'zh' => 'Chinese',
                'ja' => 'Japanese',
            ],
            'default_language' => 'en',
            'enable_translation' => true,
            'translation_service' => 'google',
        ];
    }

    // Get available tone settings
    public function getDefaultToneSettings(): array
    {
        return [
            'professional' => [
                'label' => 'Professional',
                'description' => 'Formal and business-like tone',
                'attributes' => ['formal', 'precise', 'authoritative'],
            ],
            'conversational' => [
                'label' => 'Conversational',
                'description' => 'Friendly and engaging tone',
                'attributes' => ['casual', 'approachable', 'relatable'],
            ],
            'academic' => [
                'label' => 'Academic',
                'description' => 'Scholarly and research-oriented tone',
                'attributes' => ['analytical', 'detailed', 'objective'],
            ],
            'creative' => [
                'label' => 'Creative',
                'description' => 'Imaginative and expressive tone',
                'attributes' => ['innovative', 'artistic', 'engaging'],
            ],
            'technical' => [
                'label' => 'Technical',
                'description' => 'Detailed and specification-focused tone',
                'attributes' => ['precise', 'technical', 'instructional'],
            ],
        ];
    }

    // Get available content formats
    public function getDefaultContentFormats(): array
    {
        return [
            'article' => [
                'label' => 'Article',
                'structure' => ['introduction', 'body', 'conclusion'],
                'word_count' => [800, 2000],
            ],
            'blog_post' => [
                'label' => 'Blog Post',
                'structure' => ['hook', 'main_content', 'takeaways'],
                'word_count' => [500, 1500],
            ],
            'product_description' => [
                'label' => 'Product Description',
                'structure' => ['features', 'benefits', 'specifications'],
                'word_count' => [200, 500],
            ],
            'service_page' => [
                'label' => 'Service Page',
                'structure' => ['overview', 'features', 'benefits', 'process', 'cta'],
                'word_count' => [1000, 2500],
            ],
            'landing_page' => [
                'label' => 'Landing Page',
                'structure' => ['hero', 'features', 'testimonials', 'pricing', 'cta'],
                'word_count' => [1500, 3000],
            ],
        ];
    }

    // Get available target audiences
    public function getDefaultTargetAudiences(): array
    {
        return [
            'general' => [
                'label' => 'General Audience',
                'description' => 'Content suitable for a broad audience',
                'characteristics' => ['diverse', 'inclusive', 'accessible'],
            ],
            'professional' => [
                'label' => 'Professional',
                'description' => 'Business and industry professionals',
                'characteristics' => ['experienced', 'career-focused', 'industry-aware'],
            ],
            'technical' => [
                'label' => 'Technical',
                'description' => 'Technical experts and developers',
                'characteristics' => ['technical', 'detail-oriented', 'specialized'],
            ],
            'academic' => [
                'label' => 'Academic',
                'description' => 'Students and educators',
                'characteristics' => ['scholarly', 'research-focused', 'educational'],
            ],
            'consumer' => [
                'label' => 'Consumer',
                'description' => 'General consumers and shoppers',
                'characteristics' => ['value-conscious', 'solution-seeking', 'practical'],
            ],
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Set default values if not provided
            $model->model_config = $model->model_config ?? $model->getFormattedModelConfig();
            $model->generation_settings = $model->generation_settings ?? $model->getDefaultGenerationSettings();
            $model->prompt_templates = $model->prompt_templates ?? $model->getDefaultPromptTemplates();
            $model->language_settings = $model->language_settings ?? $model->getDefaultLanguageSettings();
            $model->tone_settings = $model->tone_settings ?? $model->getDefaultToneSettings();
            $model->content_formats = $model->content_formats ?? $model->getDefaultContentFormats();
            $model->target_audiences = $model->target_audiences ?? $model->getDefaultTargetAudiences();
        });
    }
}
