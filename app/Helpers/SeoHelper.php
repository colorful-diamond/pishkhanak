<?php

namespace App\Helpers;

use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\SEOMeta;

class SeoHelper
{
    /**
     * Add global website schema
     */
    public static function addWebsiteSchema()
    {
        JsonLd::addValue('@context', 'https://schema.org');
        JsonLd::addValue('@type', 'WebSite');
        JsonLd::addValue('name', 'پیشخوانک');
        JsonLd::addValue('alternateName', 'Pishkhanak');
        JsonLd::addValue('url', url('/'));
        JsonLd::addValue('description', 'پیشخوانک، مرجع جامع خدمات استعلام آنلاین');
        
        JsonLd::addValue('publisher', [
            '@type' => 'Organization',
            'name' => 'پیشخوانک',
            'logo' => [
                '@type' => 'ImageObject',
                'url' => asset('assets/logo-lg.png')
            ]
        ]);

        JsonLd::addValue('potentialAction', [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => route('app.page.home') . '?q={search_term_string}'
            ],
            'query-input' => 'required name=search_term_string'
        ]);
    }

    /**
     * Add organization schema
     */
    public static function addOrganizationSchema()
    {
        return [
            '@type' => 'Organization',
            'name' => 'پیشخوانک',
            'alternateName' => 'Pishkhanak',
            'url' => url('/'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => asset('assets/logo-lg.png'),
                'width' => 600,
                'height' => 600
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'contactType' => 'customer service',
                'email' => 'info@pishkhanak.com',
                'availableLanguage' => 'Persian'
            ],
            'sameAs' => [
                'https://t.me/pishkhanak',
                'https://instagram.com/pishkhanak'
            ],
            'description' => 'پیشخوانک، مرجع جامع خدمات استعلام آنلاین شامل استعلام بانکی، خودرو، مالیاتی و سایر خدمات',
            'foundingDate' => '2024',
            'address' => [
                '@type' => 'PostalAddress',
                'addressCountry' => 'IR',
                'addressLocality' => 'تهران'
            ]
        ];
    }

    /**
     * Add service schema for specific services
     */
    public static function addServiceSchema($service)
    {
        return [
            '@type' => 'Service',
            'name' => $service->title,
            'description' => strip_tags($service->description ?? 'خدمات استعلام آنلاین ' . $service->title),
            'provider' => self::addOrganizationSchema(),
            'serviceType' => $service->title,
            'category' => $service->category->name ?? 'خدمات استعلام',
            'url' => route('services.show', $service->slug),
            'areaServed' => [
                '@type' => 'Country',
                'name' => 'ایران'
            ]
        ];
    }

    /**
     * Generate meta keywords from content
     */
    public static function generateKeywords($content, $additional = [])
    {
        $commonKeywords = ['پیشخوانک', 'استعلام آنلاین', 'خدمات'];
        $contentKeywords = [];
        
        if (is_string($content)) {
            // Extract keywords from content (simplified approach)
            $words = str_word_count(strip_tags($content), 1);
            $contentKeywords = array_slice(array_unique($words), 0, 5);
        }
        
        return array_unique(array_merge($commonKeywords, $additional, $contentKeywords));
    }

    /**
     * Set common viewport and mobile meta tags
     */
    public static function setMobileMeta()
    {
        SEOMeta::addMeta('viewport', 'width=device-width, initial-scale=1, maximum-scale=5')
            ->addMeta('mobile-web-app-capable', 'yes')
            ->addMeta('apple-mobile-web-app-capable', 'yes')
            ->addMeta('apple-mobile-web-app-status-bar-style', 'black-translucent')
            ->addMeta('theme-color', '#ffffff')
            ->addMeta('msapplication-TileColor', '#2d89ef');
    }

    /**
     * Add hreflang tags for multilingual support
     */
    public static function addHreflangTags($url)
    {
        SEOMeta::addMeta('hreflang', 'fa', [], true, 'link')
            ->addMeta('hreflang', 'x-default', $url, true, 'link');
    }
} 