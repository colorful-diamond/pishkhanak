<?php

namespace App\Services;

use App\Models\FooterSection;
use App\Models\FooterLink;
use App\Models\SiteLink;
use App\Models\FooterContent;
use App\Models\ServiceCategory;
use App\Models\Service;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class FooterManagerService
{
    /**
     * Get cached footer sections for a specific location
     */
    public static function getFooterSections(string $location = 'footer'): Collection
    {
        return FooterSection::getCachedSections($location);
    }

    /**
     * Get cached footer links for a specific section
     */
    public static function getFooterLinks(int $sectionId): Collection
    {
        return FooterLink::getCachedLinks($sectionId);
    }

    /**
     * Get cached site links for a specific location
     */
    public static function getSiteLinks(string $location): Collection
    {
        return SiteLink::getCachedLinks($location);
    }

    /**
     * Get cached footer content by key
     */
    public static function getFooterContent(string $key): ?string
    {
        return FooterContent::getCachedContent($key);
    }

    /**
     * Get cached footer content by section
     */
    public static function getFooterContentBySection(string $section): Collection
    {
        return FooterContent::getCachedContent(null, $section);
    }

    /**
     * Get all cached footer content
     */
    public static function getAllFooterContent(): Collection
    {
        return FooterContent::getCachedContent();
    }

    /**
     * Clear all footer and links cache
     */
    public static function clearAllCache(): void
    {
        FooterSection::clearCache();
        FooterLink::clearCache();
        SiteLink::clearCache();
        FooterContent::clearCache();
        Cache::forget('footer_random_services');
        Cache::forget('footer_data_complete');
    }

    /**
     * Get random services for footer display
     */
    public static function getRandomServicesForFooter(): array
    {
        $cacheKey = 'footer_random_services';
        
        return Cache::remember($cacheKey, 3600, function () {
            // Get all active service categories
            $categories = ServiceCategory::active()->ordered()->get();
            
            $footerData = [];
            
            // Add 3 service categories with 7 random services each
            foreach ($categories->take(3) as $category) {
                $services = Service::where('category_id', $category->id)
                    ->where('status', 'active')
                    ->inRandomOrder()
                    ->limit(7)
                    ->get();
                
                $footerData[] = [
                    'title' => $category->name,
                    'type' => 'category',
                    'services' => $services
                ];
            }
            
            // Add 1 random section with 7 services from all categories
            $randomServices = Service::where('status', 'active')
                ->inRandomOrder()
                ->limit(7)
                ->get();
            
            $footerData[] = [
                'title' => 'خدمات منتخب',
                'type' => 'random',
                'services' => $randomServices
            ];
            
            return $footerData;
        });
    }

    /**
     * Get footer data for rendering
     */
    public static function getFooterData(): array
    {
        $cacheKey = 'footer_data_complete';
        
        return Cache::remember($cacheKey, 3600, function () {
            $sections = self::getFooterSections('footer');
            $content = self::getAllFooterContent();
            $randomServices = self::getRandomServicesForFooter();
            
            return [
                'sections' => $sections,
                'content' => $content,
                'random_services' => $randomServices,
                'company_name' => $content->get('company_name', 'پیشخوانک'),
                'description' => $content->get('description', 'راهکاری جامع برای دسترسی آنلاین به انواع خدمات استعلامی و مالی'),
                'phone' => $content->get('phone', ''),
                'mobile' => $content->get('mobile', ''),
                'address' => $content->get('address', ''),
                'email' => $content->get('email', ''),
                'social_links' => self::getFooterContentBySection('social'),
                'legal_links' => self::getFooterContentBySection('legal'),
            ];
        });
    }

    /**
     * Get header links data
     */
    public static function getHeaderLinks(): Collection
    {
        return self::getSiteLinks('header');
    }

    /**
     * Get sidebar links data
     */
    public static function getSidebarLinks(): Collection
    {
        return self::getSiteLinks('sidebar');
    }

    /**
     * Get mobile navigation links data
     */
    public static function getMobileNavLinks(): Collection
    {
        return self::getSiteLinks('mobile_nav');
    }

    /**
     * Get footer links data
     */
    public static function getFooterLinksData(): Collection
    {
        return self::getSiteLinks('footer');
    }

    /**
     * Check if a link should open in new tab
     */
    public static function shouldOpenInNewTab($link): bool
    {
        return $link->open_in_new_tab || $link->target === '_blank';
    }

    /**
     * Get target attribute for a link
     */
    public static function getLinkTarget($link): string
    {
        return $link->target ?? '_self';
    }

    /**
     * Get additional attributes for a link
     */
    public static function getLinkAttributes($link): array
    {
        $attributes = $link->attributes ?? [];
        
        if (self::shouldOpenInNewTab($link)) {
            $attributes['target'] = '_blank';
            $attributes['rel'] = 'noopener noreferrer';
        }
        
        if ($link->css_class) {
            $attributes['class'] = $link->css_class;
        }
        
        return $attributes;
    }

    /**
     * Render link attributes as HTML string
     */
    public static function renderLinkAttributes($link): string
    {
        $attributes = self::getLinkAttributes($link);
        $html = '';
        
        foreach ($attributes as $key => $value) {
            $html .= " {$key}=\"{$value}\"";
        }
        
        return $html;
    }
} 