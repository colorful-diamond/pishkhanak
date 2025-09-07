<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Service;
use App\Models\AiContent;
use App\Services\QuickResearchService;
use App\Services\EnhancedContentGenerator;
use App\Services\GeminiService;

class ProcessServiceContentWithMonitoringJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Service $service;
    protected array $options;
    protected AiContent $aiContent;
    protected $status;

    public $timeout = 300; // 5 minutes timeout (optimized from 900s)
    public $tries = 3;
    public $backoff = [30, 60, 120]; // Exponential backoff

    /**
     * Create a new job instance.
     */
    public function __construct(Service $service, array $options = [])
    {
        $this->service = $service;
        $this->options = array_merge([
            'use_web_search' => true,
            'force' => false,
            'model_type' => 'advanced',
            'language' => 'Persian'
        ], $options);
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info('Starting service content processing with monitoring', [
            'service_id' => $this->service->id,
            'service_title' => $this->service->title,
            'options' => $this->options
        ]);

        try {
            // Step 1: Get or create AI content record
            $this->aiContent = $this->getOrCreateAiContent();

            // Step 2: Generate research data (mandatory)
            $researchData = $this->generateResearchData();
            
            // Validate research data
            if (empty($researchData) || !is_array($researchData)) {
                throw new \Exception('Research data generation failed - cannot proceed without research');
            }

            // Update AI content with research data
            $this->aiContent->update([
                'research_data' => $researchData,
                'research_completed_at' => now(),
                'research_token_count' => $researchData['_metadata']['token_count'] ?? 0
            ]);

            // Step 3: Generate AI headings
            $headings = $this->generateAiHeadings();

            // Step 4: Generate content for each section
            $sections = [];
            foreach ($headings['service_headings'] as $index => $heading) {
                $sectionContent = $this->generateSectionContent(
                    $heading,
                    $index + 1,
                    count($headings['service_headings']),
                    $researchData
                );
                
                $sections[] = [
                    'heading' => $heading,
                    'content' => $sectionContent,
                    'section_number' => $index + 1
                ];
            }

            // Step 5: Generate FAQ
            $faq = $this->generateFaq();

            // Step 6: Update AI content with final results
            $this->aiContent->update([
                'ai_headings' => json_encode($headings),
                'ai_sections' => json_encode($sections),
                'faq' => json_encode($faq),
                'status' => 'completed',
                'generation_completed_at' => now(),
                'total_sections' => count($sections),
                'completed_sections' => count($sections)
            ]);

            // Step 7: Update service with AI content reference
            $this->service->update([
                'content' => (string) $this->aiContent->id
            ]);

            Log::info('Service content processed successfully with monitoring', [
                'service_id' => $this->service->id,
                'ai_content_id' => $this->aiContent->id,
                'sections_count' => count($sections),
                'faq_count' => count($faq),
                'has_research' => !empty($researchData)
            ]);

        } catch (\Exception $e) {
            $this->handleFailure($e);
        }
    }

    /**
     * Get or create AI content record
     */
    protected function getOrCreateAiContent(): AiContent
    {
        // Check if service already has AI content
        if (!$this->options['force'] && is_numeric($this->service->content)) {
            $existingAiContent = AiContent::find($this->service->content);
            if ($existingAiContent) {
                Log::info('Using existing AI content', [
                    'service_id' => $this->service->id,
                    'ai_content_id' => $existingAiContent->id
                ]);
                return $existingAiContent;
            }
        }
        
        // Create new AI content
        $serviceTitle = $this->service->getDisplayTitle();
        $shortDescription = $this->generateShortDescription();
        
        return AiContent::create([
            'title' => $serviceTitle,
            'slug' => Str::slug($serviceTitle) . '-' . time(),
            'short_description' => $shortDescription,
            'language' => 'Persian',
            'model_type' => 'Service',
            'model_id' => $this->service->id,
            'status' => 'generating',
            'generation_settings' => [
                'temperature' => 0.7,
                'max_tokens' => 4000,
                'use_web_search' => $this->options['use_web_search']
            ],
            'generation_started_at' => now(),
            'total_sections' => 0,
            'completed_sections' => 0
        ]);
    }

    /**
     * Generate research data for the service
     */
    protected function generateResearchData(): array
    {
        Log::info('Generating research data for service', [
            'service_id' => $this->service->id,
            'service_title' => $this->service->title
        ]);

        try {
            $geminiService = app(GeminiService::class);
            $researchService = new QuickResearchService($geminiService);
            
            $researchData = $researchService->generateQuickResearch(
                $this->service->title,
                $this->service->description ?? '',
                [
                    'short_title' => $this->service->short_title,
                    'service_id' => $this->service->id
                ]
            );

            Log::info('Research data generated successfully', [
                'service_id' => $this->service->id,
                'data_size' => strlen(json_encode($researchData)),
                'has_sufficient_data' => $researchData['_metadata']['has_sufficient_data'] ?? false
            ]);

            return $researchData;

        } catch (\Exception $e) {
            Log::error('Research data generation failed', [
                'service_id' => $this->service->id,
                'error' => $e->getMessage()
            ]);
            
            // Research is mandatory - throw exception to fail the job
            throw new \Exception('Research generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate AI headings for the service
     */
    protected function generateAiHeadings(): array
    {
        // Create standard headings structure for service content
        $headings = [
            'service_headings' => [
                [
                    'title' => 'معرفی کامل ' . $this->service->title,
                    'sub_headlines' => [
                        'مزایای استفاده از پیشخوانک',
                        'نحوه دریافت خدمت',
                        'هزینه‌ها و زمان‌بندی'
                    ]
                ],
                [
                    'title' => 'مراحل انجام ' . ($this->service->short_title ?? $this->service->title),
                    'sub_headlines' => [
                        'مدارک مورد نیاز',
                        'فرآیند ثبت‌نام‌ها',
                        'نیازها همه و توضیحات'
                    ]
                ],
                [
                    'title' => 'سوالات متداول درباره ' . ($this->service->short_title ?? $this->service->title),
                    'sub_headlines' => [
                        'مشکلات رایج و راه‌حل‌ها',
                        'تماس با پیشخوانک',
                        'پیگیری‌ها و تغییرات'
                    ]
                ]
            ]
        ];

        Log::info('Generated AI headings', [
            'service_id' => $this->service->id,
            'headings_count' => count($headings['service_headings'])
        ]);

        return $headings;
    }

    /**
     * Generate content for a specific section
     */
    protected function generateSectionContent(array $heading, int $sectionNumber, int $totalSections, array $researchData): string
    {
        Log::info('Generating section content', [
            'service_id' => $this->service->id,
            'section_number' => $sectionNumber,
            'heading' => $heading['title'] ?? 'Unknown'
        ]);

        try {
            $contentGenerator = app(EnhancedContentGenerator::class);
            
            $keywords = $this->extractKeywords();
            
            $content = $contentGenerator->generateEnhancedSectionContent(
                $heading,
                $this->service->title,
                $this->service->short_title ?? $this->service->title,
                $sectionNumber,
                $totalSections,
                $this->options['language'],
                $this->options['model_type'],
                'offline',
                [], // servicesForLinking
                [], // usedLinks
                $researchData,
                $keywords
            );

            Log::info('Section content generated', [
                'service_id' => $this->service->id,
                'section_number' => $sectionNumber,
                'content_length' => strlen($content)
            ]);

            return $content;

        } catch (\Exception $e) {
            Log::error('Section content generation failed', [
                'service_id' => $this->service->id,
                'section_number' => $sectionNumber,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Generate FAQ section
     */
    protected function generateFaq(): array
    {
        return [
            [
                'question' => 'چه امکانات قابل اعتماد است؟',
                'answer' => 'بله، پیشخوانک به عنوان یک سامانه معتبر امنیت و دقت اطلاعات را تضمین می‌کند.'
            ],
            [
                'question' => 'چگونه می‌توانم از این خدمت استفاده کنم؟',
                'answer' => 'با مراجعه به وب‌سایت pishkhanak.com و انتخاب سرویس مورد نظر می‌توانید به راحتی از خدمت استفاده کنید.'
            ],
            [
                'question' => 'چه اطلاعاتی من محفوظ است؟',
                'answer' => 'بله، تمام اطلاعات کاربران با بالاترین استانداردهای امنیتی محافظت می‌شود.'
            ]
        ];
    }

    /**
     * Extract keywords from service data
     */
    protected function extractKeywords(bool $asString = false): array|string
    {
        $keywords = [
            'پیشخوانک',
            $this->service->title,
            $this->service->short_title ?? ''
        ];

        // Add service-specific keywords
        if (strpos($this->service->title, 'بانک') !== false) {
            $keywords[] = 'خدمات بانکی';
        }
        if (strpos($this->service->title, 'استعلام') !== false) {
            $keywords[] = 'استعلام آنلاین';
        }

        $keywords = array_filter(array_unique($keywords));

        return $asString ? implode(', ', $keywords) : $keywords;
    }

    /**
     * Generate short description for the service
     */
    protected function generateShortDescription(): string
    {
        $description = $this->service->description ?? $this->service->short_title ?? $this->service->title;
        
        if (strlen($description) > 200) {
            $description = substr($description, 0, 197) . '...';
        }
        
        return $description;
    }

    /**
     * Handle job failure
     */
    protected function handleFailure(\Exception $e)
    {
        Log::error('Service content processing failed', [
            'service_id' => $this->service->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        // Update AI content status if exists
        if (isset($this->aiContent)) {
            $this->aiContent->update([
                'status' => 'failed',
                'generation_completed_at' => now()
            ]);
        }

        throw $e;
    }
}