<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class AiContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_type',
        'model_id',
        'title',
        'slug',
        'short_description',
        'language',
        'status',
        'generation_settings',
        'generation_progress',
        'current_generation_step',
        'section_generation_status',
        'generation_started_at',
        'generation_completed_at',
        'author_id',
        'last_edited_by',
        'ai_headings', // JSON
        'ai_sections', // JSON
        'ai_summary',
        'ai_thumbnails', // JSON
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'twitter_title',
        'twitter_description',
        'schema', // JSON
        'json_ld', // JSON
        'faq', // JSON
        'research_data', // JSON
        'research_completed_at',
        'research_token_count',
        'total_sections',
        'completed_sections',
    ];

    protected $casts = [
        'ai_headings' => 'array',
        'ai_sections' => 'array',
        'ai_thumbnails' => 'array',
        'schema' => 'array',
        'json_ld' => 'array',
        'faq' => 'array',
        'generation_settings' => 'array',
        'section_generation_status' => 'array',
        'generation_started_at' => 'datetime',
        'generation_completed_at' => 'datetime',
        'research_data' => 'array',
        'research_completed_at' => 'datetime',
    ];

    /**
     * Model relationships
     */
    public function sourceable(): MorphTo
    {
        return $this->morphTo('sourceable', 'model_type', 'model_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function lastEditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_edited_by');
    }

    /**
     * Generation status helpers
     */
    public function isGenerating(): bool
    {
        return $this->status === 'generating';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function getSectionStatus(string $sectionHeading): ?string
    {
        return $this->section_generation_status[$sectionHeading] ?? null;
    }

    public function updateSectionStatus(string $sectionHeading, string $status): void
    {
        $statuses = $this->section_generation_status ?? [];
        $statuses[$sectionHeading] = $status;
        $this->update(['section_generation_status' => $statuses]);
    }

    public function markSectionAsCompleted(string $sectionHeading): void
    {
        $this->updateSectionStatus($sectionHeading, 'completed');
    }

    public function markSectionAsFailed(string $sectionHeading): void
    {
        $this->updateSectionStatus($sectionHeading, 'failed');
    }

    public function updateGenerationProgress(int $progress, ?string $currentStep = null): void
    {
        $data = ['generation_progress' => min(100, max(0, $progress))];
        if ($currentStep !== null) {
            $data['current_generation_step'] = $currentStep;
        }
        $this->update($data);
    }

    /**
     * Mutators to automatically trim fields to database limits
     */
    public function setMetaTitleAttribute($value)
    {
        $this->attributes['meta_title'] = $this->trimField($value, 255);
    }

    public function setMetaKeywordsAttribute($value)
    {
        $this->attributes['meta_keywords'] = $this->trimField($value, 255);
    }

    public function setOgTitleAttribute($value)
    {
        $this->attributes['og_title'] = $this->trimField($value, 255);
    }

    public function setTwitterTitleAttribute($value)
    {
        $this->attributes['twitter_title'] = $this->trimField($value, 255);
    }

    /**
     * Helper method to trim fields
     */
    protected function trimField($value, $maxLength)
    {
        if (empty($value)) {
            return null;
        }
        
        $value = trim($value);
        if (strlen($value) > $maxLength) {
            return substr($value, 0, $maxLength - 3) . '...';
        }
        
        return $value;
    }

    /**
     * Generate Unified HTML Content
     * @return string
     */
    public function generateUnifiedHtml($h1 = false , $wrapper = "article" , $summary = false, $faq = false)
    {
        $html = "<$wrapper>";
        if ($h1) {
            $html .= "<h1>{$this->title}</h1>";
        }

        if (!empty($this->ai_sections)) {
            foreach ($this->ai_sections as $section) {
                if(strpos($section, '<section') !== false){
                    $html .= $section;
                }else{
                    $html .= "<section>";
                    $html .= $section;
                    $html .= "</section>";
                }
            }
        }

        if ($summary && $this->ai_summary) {
            $html .= "<section>";
            $html .= "<h2>Summary</h2>";
            $html .= "<p>" . htmlspecialchars($this->ai_summary) . "</p>";
            $html .= "</section>";
        }

        if ($faq && !empty($this->faq)) {
            $html .= "\n\n<section>";
            $html .= "\n<h2>Frequently Asked Questions</h2>";
            foreach ($this->faq as $index => $faq) {
                $html .= "\n<div class=\"accordion-group\" data-accordion=\"default-accordion\">";
                $html .= "\n    <div class=\"accordion py-6 border-b border-gray-200/80 dark:border-gray-700/80 hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-all duration-300\" id=\"heading-" . ($index + 1) . "-pagedone-accordion\">";
                $html .= "\n        <button class=\"accordion-toggle group inline-flex items-center justify-between w-full transition duration-300\" aria-controls=\"collapse-" . ($index + 1) . "-pagedone-accordion\">";
                $html .= "\n            <div class=\"flex items-center\">";
                if(isset($faq['icon'])){
                    $html .= "\n                <div class=\"w-8 h-8 me-5 text-indigo-600 dark:text-indigo-400\">";
                    $html .= "\n                    <svg fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">";
                    $html .= "\n                        " . $faq['icon'];
                    $html .= "\n                    </svg>";
                    $html .= "\n                </div>";
                }
                $html .= "\n                <h5 class=\"text-lg md:text-xl font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300\">";
                $html .= "\n                    " . htmlspecialchars($faq['question']);
                $html .= "\n                </h5>";
                $html .= "\n            </div>";
                $html .= "\n            <svg class=\"w-6 h-6 text-gray-400 transform transition-all duration-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 group-hover:rotate-180\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">";
                $html .= "\n                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 9l-7 7-7-7\" />";
                $html .= "\n            </svg>";
                $html .= "\n        </button>";
                $html .= "\n        <div id=\"collapse-" . ($index + 1) . "-pagedone-accordion\" class=\"accordion-content overflow-hidden transition-all duration-500 ease-in-out\">";
                $html .= "\n            <div class=\"px-6 py-5 mt-4 bg-white/50 dark:bg-gray-800/50 rounded-xl text-dark-blue-500 dark:text-gray-300 leading-relaxed\">";
                $html .= "\n                " . htmlspecialchars($faq['answer']);
                $html .= "\n            </div>";
                $html .= "\n        </div>";
                $html .= "\n    </div>";
                $html .= "\n</div>";
            }
            $html .= "\n</section>";

            $html .= "<script type='application/ld+json'>";
            $faqSchema = [
                "@context" => "https://schema.org",
                "@type" => "FAQPage",
                "mainEntity" => array_map(function($faq) {
                    return [
                        "@type" => "Question",
                        "name" => $faq['question'],
                        "acceptedAnswer" => [
                            "@type" => "Answer",
                            "text" => $faq['answer']
                        ]
                    ];
                }, $this->faq)
            ];
            $html .= json_encode($faqSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $html .= "</script>";
        }

        $html .= "</{$wrapper}>";

        // Decode HTML entities back to normal HTML tags
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Replace any remaining encoded tags
        $html = str_replace(
            ['&lt;', '&gt;', '&quot;', '&#039;', '&amp;'],
            ['<', '>', '"', "'", '&'],
            $html
        );

        $html = str_replace(array('```html', '```' , 'html'), '', $html);
        return $html;
    }

    /**
     * Get the full rendered content combining all sections
     */
    public function getFullContent()
    {
        $content = '';
        
        if ($this->ai_headings && $this->ai_sections) {
            foreach ($this->ai_headings as $index => $heading) {
                // Add heading
                $content .= '<h2>' . htmlspecialchars($heading['title']) . '</h2>' . "\n\n";
                
                // Add section content if exists
                if (isset($this->ai_sections[$index])) {
                    $sectionContent = $this->ai_sections[$index];
                    
                    // Handle case where section content is an array with 'content' key
                    if (is_array($sectionContent)) {
                        if (isset($sectionContent['content'])) {
                            // Section has 'content' key - use it
                            $sectionContent = $sectionContent['content'];
                        } else if (isset($sectionContent[0])) {
                            // Section is array with numeric index
                            $sectionContent = is_string($sectionContent[0]) ? $sectionContent[0] : json_encode($sectionContent);
                        } else {
                            // Unknown array structure - convert to JSON
                            $sectionContent = json_encode($sectionContent);
                        }
                    }
                    
                    $content .= $sectionContent . "\n\n";
                }
                
                // Add sub-headings if exist (without content, just the headings)
                if (isset($heading['sub_headlines']) && is_array($heading['sub_headlines'])) {
                    foreach ($heading['sub_headlines'] as $subHeading) {
                        // Handle case where sub-heading might be an array
                        $subHeadingText = is_array($subHeading) ? ($subHeading['title'] ?? json_encode($subHeading)) : $subHeading;
                        $content .= '<h3>' . htmlspecialchars($subHeadingText) . '</h3>' . "\n";
                    }
                    $content .= "\n";
                }
            }
        }
        
        // Add summary if exists
        if ($this->ai_summary) {
            $content .= '<div class="summary">' . "\n";
            $content .= '<h2>خلاصه</h2>' . "\n";
            $content .= $this->ai_summary . "\n";
            $content .= '</div>' . "\n\n";
        }
        
        // Add FAQ section if exists
        if ($this->faq && is_array($this->faq)) {
            $content .= '<div class="faq-section">' . "\n";
            $content .= '<h2>سوالات متداول</h2>' . "\n";
            foreach ($this->faq as $faq) {
                $content .= '<div class="faq-item">' . "\n";
                $content .= '<h3>' . htmlspecialchars($faq['question']) . '</h3>' . "\n";
                $content .= '<p>' . htmlspecialchars($faq['answer']) . '</p>' . "\n";
                $content .= '</div>' . "\n";
            }
            $content .= '</div>' . "\n\n";
        }
        
        return trim($content);
    }

    /**
     * Get content as plain text (without HTML tags)
     */
    public function getPlainContent()
    {
        return strip_tags($this->getFullContent());
    }

    /**
     * Get content summary for display
     */
    public function getContentSummary($length = 200)
    {
        $plainContent = $this->getPlainContent();
        
        if (strlen($plainContent) <= $length) {
            return $plainContent;
        }
        
        return substr($plainContent, 0, $length) . '...';
    }
}
