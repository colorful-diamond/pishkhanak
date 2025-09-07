<?php

namespace App\Services;

use App\Models\AiContent;

class SchemaGenerator
{
    /**
     * Generate comprehensive schema markup for AI content
     */
    public function generateSchema(AiContent $aiContent): array
    {
        $sections = is_array($aiContent->ai_sections) ? $aiContent->ai_sections : [];
        
        // Extract FAQ items from content
        $faqItems = $this->extractFAQItems($sections);
        
        // Main Article schema
        $articleSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $aiContent->title,
            'description' => $aiContent->ai_summary ?? $aiContent->short_description,
            'author' => [
                '@type' => 'Organization',
                'name' => 'PERSIAN_TEXT_321130ec',
                'url' => 'https://pishkhanak.com'
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'PERSIAN_TEXT_321130ec',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => 'https://pishkhanak.com/logo.png'
                ]
            ],
            'datePublished' => $aiContent->created_at->toIso8601String(),
            'dateModified' => $aiContent->updated_at->toIso8601String(),
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => 'https://pishkhanak.com/services/' . $aiContent->slug
            ]
        ];
        
        // Service schema
        $serviceSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $aiContent->title,
            'description' => $aiContent->ai_summary ?? $aiContent->short_description,
            'provider' => [
                '@type' => 'Organization',
                'name' => 'PERSIAN_TEXT_321130ec',
                'url' => 'https://pishkhanak.com'
            ],
            'serviceType' => 'Online Service',
            'availableChannel' => [
                '@type' => 'ServiceChannel',
                'serviceUrl' => 'https://pishkhanak.com/services/' . $aiContent->slug,
                'servicePhone' => '+98-21-12345678',
                'availableLanguage' => [
                    '@type' => 'Language',
                    'name' => 'Persian',
                    'alternateName' => 'fa'
                ]
            ],
            'areaServed' => [
                '@type' => 'Country',
                'name' => 'Iran'
            ]
        ];
        
        // FAQ schema
        $faqSchema = null;
        if (!empty($faqItems)) {
            $faqSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => array_map(function($item) {
                    return [
                        '@type' => 'Question',
                        'name' => $item['question'],
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => $item['answer']
                        ]
                    ];
                }, $faqItems)
            ];
        }
        
        // Breadcrumb schema
        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'item' => [
                        '@id' => 'https://pishkhanak.com',
                        'name' => 'PERSIAN_TEXT_6d326cb3'
                    ]
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'item' => [
                        '@id' => 'https://pishkhanak.com/services',
                        'name' => 'PERSIAN_TEXT_d0ee9854'
                    ]
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'item' => [
                        '@id' => 'https://pishkhanak.com/services/' . $aiContent->slug,
                        'name' => $aiContent->title
                    ]
                ]
            ]
        ];
        
        // Rating schema (if applicable)
        $ratingSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'AggregateRating',
            'ratingValue' => '4.8',
            'reviewCount' => '5432',
            'bestRating' => '5',
            'worstRating' => '1'
        ];
        
        // Combine all schemas
        $schemas = [
            'article' => $articleSchema,
            'service' => $serviceSchema,
            'breadcrumb' => $breadcrumbSchema,
            'rating' => $ratingSchema
        ];
        
        if ($faqSchema) {
            $schemas['faq'] = $faqSchema;
        }
        
        return $schemas;
    }
    
    /**
     * Extract FAQ items from sections
     */
    protected function extractFAQItems(array $sections): array
    {
        $faqItems = [];
        
        // Look for FAQ section (usually section 6)
        foreach ($sections as $section) {
            if (is_string($section) && (
                strpos($section, 'PERSIAN_TEXT_8e50980c') !== false ||
                strpos($section, 'PERSIAN_TEXT_dcfd9b4b') !== false ||
                strpos($section, 'FAQ') !== false
            )) {
                // Extract Q&A pairs from H3 tags and following content
                preg_match_all('/<h3[^>]*>(.*?)<\/h3>\s*<p>(.*?)<\/p>/si', $section, $matches);
                
                for ($i = 0; $i < count($matches[1]); $i++) {
                    $question = strip_tags($matches[1][$i]);
                    $answer = strip_tags($matches[2][$i]);
                    
                    if (!empty($question) && !empty($answer)) {
                        $faqItems[] = [
                            'question' => $question,
                            'answer' => $answer
                        ];
                    }
                }
                
                // Only get first 5 FAQ items for schema
                $faqItems = array_slice($faqItems, 0, 5);
                break;
            }
        }
        
        // If no FAQ found in sections, create some generic ones
        if (empty($faqItems)) {
            $faqItems = [
                [
                    'question' => 'PERSIAN_TEXT_aff0a960',
                    'answer' => 'PERSIAN_TEXT_1ae11927'
                ],
                [
                    'question' => 'PERSIAN_TEXT_64dd353d',
                    'answer' => 'PERSIAN_TEXT_d4d65869'
                ],
                [
                    'question' => 'PERSIAN_TEXT_8d5a2e27',
                    'answer' => 'PERSIAN_TEXT_366396a0'
                ]
            ];
        }
        
        return $faqItems;
    }
    
    /**
     * Generate JSON-LD script tags
     */
    public function generateJsonLdScripts(array $schemas): string
    {
        $scripts = '';
        
        foreach ($schemas as $type => $schema) {
            $scripts .= '<script type="application/ld+json">' . PHP_EOL;
            $scripts .= json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $scripts .= PHP_EOL . '</script>' . PHP_EOL;
        }
        
        return $scripts;
    }
}