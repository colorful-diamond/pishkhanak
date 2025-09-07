<?php

namespace App\Helpers;

class AiContentHelper
{
    /**
     * Normalize AI headings to ensure sub_headlines are in correct format for Filament
     * Converts string sub_headlines to objects with 'title' field
     */
    public static function normalizeHeadings($headings)
    {
        if (!$headings || !is_array($headings)) {
            return [];
        }
        
        $normalized = [];
        
        foreach ($headings as $heading) {
            $normalizedHeading = [
                'title' => $heading['title'] ?? ''
            ];
            
            // Normalize sub_headlines
            if (isset($heading['sub_headlines']) && is_array($heading['sub_headlines'])) {
                $normalizedSubs = [];
                
                foreach ($heading['sub_headlines'] as $sub) {
                    if (is_string($sub)) {
                        // Convert string to object format
                        $normalizedSubs[] = ['title' => $sub];
                    } elseif (is_array($sub) && isset($sub['title'])) {
                        // Already in correct format
                        $normalizedSubs[] = $sub;
                    } elseif (is_array($sub)) {
                        // Array but missing title field, try to extract
                        $title = $sub[0] ?? $sub['name'] ?? $sub['heading'] ?? json_encode($sub);
                        $normalizedSubs[] = ['title' => $title];
                    } else {
                        // Unknown format, convert to string
                        $normalizedSubs[] = ['title' => (string)$sub];
                    }
                }
                
                $normalizedHeading['sub_headlines'] = $normalizedSubs;
            } else {
                $normalizedHeading['sub_headlines'] = [];
            }
            
            $normalized[] = $normalizedHeading;
        }
        
        return $normalized;
    }
    
    /**
     * Validate and fix AI content headings before saving
     */
    public static function validateHeadings($content)
    {
        if ($content && $content->ai_headings) {
            $headings = $content->ai_headings;
            
            // If it's a string, decode it
            if (is_string($headings)) {
                $headings = json_decode($headings, true);
            }
            
            // Normalize the headings
            $normalized = self::normalizeHeadings($headings);
            
            // Save back if changed
            if ($normalized !== $headings) {
                $content->ai_headings = $normalized;
                return true; // Indicates changes were made
            }
        }
        
        return false; // No changes needed
    }
}