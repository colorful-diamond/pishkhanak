<?php

namespace App\Traits;

use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\JsonLd;

trait SeoTrait
{
    /**
     * Convert meta keywords to array format
     */
    private function normalizeKeywords($keywords)
    {
        if (is_string($keywords)) {
            return array_filter(array_map('trim', explode(',', $keywords)));
        }
        
        return is_array($keywords) ? $keywords : [];
    }

    /**
     * Set comprehensive SEO meta tags
     */
    protected function setSeo(array $data = [])
    {
        $title = $data['title'] ?? config('seotools.meta.defaults.title');
        $description = $data['description'] ?? config('seotools.meta.defaults.description');
        $keywords = $this->normalizeKeywords($data['keywords'] ?? config('seotools.meta.defaults.keywords'));
        $canonical = $data['canonical'] ?? url()->current();
        $image = $data['image'] ?? asset('assets/logo-lg.png');
        $type = $data['type'] ?? 'website';
        $locale = $data['locale'] ?? 'fa_IR';

        // Set SEOMeta
        SEOMeta::setTitle($title)
            ->setDescription($description)
            ->setKeywords($keywords)
            ->setCanonical($canonical)
            ->addMeta('robots', 'index, follow')
            ->addMeta('author', 'پیشخوانک')
            ->addMeta('language', 'fa')
            ->addMeta('revisit-after', '7 days')
            ->addMeta('rating', 'general')
            ->addMeta('distribution', 'global');

        // Set OpenGraph
        OpenGraph::setTitle($data['og_title'] ?? $title)
            ->setDescription($data['og_description'] ?? $description)
            ->setUrl($canonical)
            ->setType($type)
            ->setSiteName('پیشخوانک')
            ->addProperty('locale', $locale)
            ->addProperty('locale:alternate', 'en_US')
            ->addImage($image, [
                'height' => 630,
                'width' => 1200
            ]);

        // Set TwitterCard
        TwitterCard::setType('summary_large_image')
            ->setSite('@estelam_net')
            ->setTitle($data['twitter_title'] ?? $title)
            ->setDescription($data['twitter_description'] ?? $description)
            ->setImage($image);

        // Set JsonLd
        JsonLd::setType($data['jsonld_type'] ?? 'WebPage')
            ->setTitle($title)
            ->setDescription($description)
            ->setUrl($canonical);
            
        if ($image) {
            JsonLd::addImage($image);
        }

        // Add organization schema
        if (!isset($data['skip_organization'])) {
            $this->addOrganizationSchema();
        }

        // Add breadcrumbs if provided
        if (isset($data['breadcrumbs']) && is_array($data['breadcrumbs'])) {
            $this->addBreadcrumbSchema($data['breadcrumbs']);
        }

        return $this;
    }

    /**
     * Add organization schema
     */
    protected function addOrganizationSchema()
    {
        JsonLd::addValue('publisher', [
            '@type' => 'Organization',
            'name' => 'پیشخوانک',
            'url' => url('/'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => asset('assets/logo-lg.png'),
                'width' => 600,
                'height' => 600
            ],
            'sameAs' => [
                'https://t.me/pishkhanak',
                'https://instagram.com/pishkhanak'
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'contactType' => 'customer service',
                'email' => 'info@pishkhanak.com'
            ]
        ]);
    }

    /**
     * Add breadcrumb schema
     */
    protected function addBreadcrumbSchema(array $breadcrumbs)
    {
        $breadcrumbList = [
            '@type' => 'BreadcrumbList',
            'itemListElement' => []
        ];

        foreach ($breadcrumbs as $index => $breadcrumb) {
            $breadcrumbList['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $breadcrumb['name'],
                'item' => $breadcrumb['url'] ?? null
            ];
        }

        JsonLd::addValue('breadcrumb', $breadcrumbList);
    }

    /**
     * Set SEO for service pages
     */
    protected function setServiceSeo($service, $parent = null)
    {
        $title = $service->meta_title ?? $service->title;
        $description = $service->meta_description ?? 
            ($service->description ? strip_tags($service->description) : 
            'استعلام آنلاین ' . $service->title);
        
        $keywords = array_merge(
            $this->normalizeKeywords($service->meta_keywords),
            ['استعلام', $service->title, 'آنلاین', 'پیشخوانک']
        );

        $breadcrumbs = [
            ['name' => 'خانه', 'url' => route('app.page.home')],
            ['name' => 'خدمات', 'url' => route('services.category', $service->category->slug)]
        ];

        if ($parent) {
            $breadcrumbs[] = ['name' => $parent->title, 'url' => route('services.show', $parent->slug)];
        }

        $breadcrumbs[] = ['name' => $service->title];

        $this->setSeo([
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'type' => 'article',
            'jsonld_type' => 'Service',
            'breadcrumbs' => $breadcrumbs,
            'image' => $service->thumbnail ? asset('thumbnails/' . $service->id . '/' . $service->thumbnail) : null
        ]);

        // Add service-specific schema
        JsonLd::addValue('provider', [
            '@type' => 'Organization',
            'name' => 'پیشخوانک'
        ]);

        JsonLd::addValue('serviceType', $service->title);
        JsonLd::addValue('category', $service->category->name);
    }

    /**
     * Set SEO for blog posts
     */
    protected function setBlogSeo($post)
    {
        $title = $post->meta_title ?? $post->title;
        $description = $post->meta_description ?? 
            ($post->excerpt ?? strip_tags(substr($post->content, 0, 150)));
        
        $keywords = array_merge(
            $this->normalizeKeywords($post->meta_keywords),
            ['وبلاگ', 'پیشخوانک', 'مقاله']
        );

        $breadcrumbs = [
            ['name' => 'خانه', 'url' => route('app.page.home')],
            ['name' => 'وبلاگ', 'url' => route('app.blog.index')],
            ['name' => $post->title]
        ];

        $this->setSeo([
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'type' => 'article',
            'jsonld_type' => 'Article',
            'breadcrumbs' => $breadcrumbs,
            'image' => $post->featured_image ? asset($post->featured_image) : null
        ]);

        // Add article-specific schema
        JsonLd::addValue('author', [
            '@type' => 'Person',
            'name' => $post->author->name ?? 'پیشخوانک'
        ]);

        JsonLd::addValue('datePublished', $post->created_at->toISOString());
        JsonLd::addValue('dateModified', $post->updated_at->toISOString());
        JsonLd::addValue('wordCount', str_word_count(strip_tags($post->content)));
    }

    /**
     * Set SEO for category pages
     */
    protected function setCategorySeo($category)
    {
        $title = $category->meta_title ?? ('خدمات ' . $category->name);
        $description = $category->meta_description ?? 
            ('مجموعه کامل خدمات ' . $category->name);
        
        $keywords = array_merge(
            $this->normalizeKeywords($category->meta_keywords),
            ['خدمات', $category->name, 'استعلام', 'پیشخوانک']
        );

        $breadcrumbs = [
            ['name' => 'خانه', 'url' => route('app.page.home')],
            ['name' => 'خدمات ' . $category->name]
        ];

        $this->setSeo([
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'type' => 'website',
            'jsonld_type' => 'CollectionPage',
            'breadcrumbs' => $breadcrumbs
        ]);
    }
} 