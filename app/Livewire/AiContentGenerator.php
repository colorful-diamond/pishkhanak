<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\AiContent;
use App\Services\AiService;
use App\Models\Service;
use App\Services\ServiceFormAnalyzer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Events\CustomUserEvent;
use Illuminate\Support\Facades\Queue;
use App\Jobs\GenerateSectionContentJob;
use Illuminate\Support\Facades\Redis;

class AiContentGenerator extends Component
{

    //Settings
    public $title;
    public $short_description;
    public $language = 'Persian';
    public $model_type = 'fast';
    public $generation_mode = 'offline';
    public $headings_number = 8;
    public $sub_headings_number = 3;
    
    // Auto-process settings
    public $auto_process = false; // Auto-proceed to next steps
    public $step_approval_required = true; // Require manual approval for each step
    
    // Save settings
    public $save_as_default = false; // Save current settings as default

    //Generation
    public $currentStep = 1;
    public $maxSteps = 6;
    public $aiContentId;
    public $status = 'pending';
    public $progress = 0;
    public $error = '';
    public $tone = 'professional'; // Writing tone
    public $contentLength = 'medium'; // Content length preference
    public $targetAudience = 'general'; // Target audience
    public $keywords = ''; // SEO keywords
    public $includeImages = false; // Option to include AI-generated image suggestions
    public $contentFormat = 'article'; // Content format (article, blog, whitepaper, etc.)
    public $customInstructions = ''; // Additional custom instructions
    public $enableAutoSave = true; // Auto-save feature
    public $lastAutoSave = null; // Last auto-save timestamp
    public $generationHistory = []; // Track generation history
    public $revisionCount = 0; // Track content revisions
    public $aiAnalysisResults = null; // Store AI analysis results
    public string $event_hash;

    // Add new properties to track state
    public $headings = [];
    public $sections = [];
    public $currentSection = '';
    public $summary = '';
    public $meta = [];
    public $schema = [];
    public $faq = [];
    
    // Add properties for real-time heading generation
    public $headingGenerationStatus = 'pending'; // pending, generating, complete
    public $generatedHeadingsCount = 0;
    public $totalHeadingsToGenerate = 0;
    
    // Add properties for section generation tracking
    public $sectionGenerationStatus = []; // Track status for each heading section
    public $completedSections = 0;
    public $failedSections = 0;
    
    // Add properties for editing headings
    public $editingHeading = null; // Index of heading being edited
    public $editingSubHeading = null; // [heading_index, sub_heading_index] for sub-heading being edited
    public $editingText = ''; // Temporary text while editing
    
    // Add properties for section management
    public $selectedSection = null; // Currently selected section for preview
    public $sectionPreview = ''; // Content of selected section
    public $showSectionPreview = false; // Whether to show preview modal
    public $editingSection = null; // Index of section being edited
    public $editingSectionContent = ''; // Content being edited
    public $sectionTimeout = 60; // 60 seconds timeout
    public $sectionRetryCount = []; // Track retry attempts per section
    public $maxRetries = 3; // Maximum retry attempts
    public $sectionStartTime = []; // Track when each section started generating

    public $model_name = '';
    public $search_title = '';

    public $generation_type = 'manual';

    public $searchResults = [];
    public $selectedResult = null;

    // Add user_id property for broadcasting
    public $user_id;
    
    // Image generation properties
    public $imageGenerationEnabled = false; // Whether to generate images
    public $imageGenerationStatus = []; // Track status for each section's images
    public $generatedImages = []; // Store generated images for each section
    public $selectedImages = []; // Store user-selected images
    public $imageGenerationSettings = []; // Store image generation settings
    public $showImageSelection = false; // Whether to show image selection modal
    public $currentImageSection = null; // Currently selecting images for this section
    public $imageProcessingComplete = false; // Whether all images are processed

    // Target model settings
    public $target_model_type = null;
    public $target_model_id = null;
    public $update_target_after_completion = false;

    // Define allowed models and their configurations
    protected $allowedModels = [
        'Posts' => [
            'model' => \App\Models\Post::class,
            'title_field' => 'title',
            'description_field' => 'description',
            'searchable_fields' => ['title', 'slug'],
        ],
        'Services' => [
            'model' => \App\Models\Service::class,
            'title_field' => 'title',
            'description_field' => 'description',
            'searchable_fields' => ['title', 'slug'],
        ],
        'Service Categories' => [
            'model' => \App\Models\ServiceCategory::class,
            'title_field' => 'title',
            'description_field' => 'description',
            'searchable_fields' => ['title', 'slug'],
        ],
        'Categories' => [
            'model' => \App\Models\Category::class,
            'title_field' => 'title',
            'description_field' => 'description',
            'searchable_fields' => ['title', 'slug'],
        ],
    ];

    /**
     * Get the description field for a given model instance.
     */
    protected function getModelDescription($model)
    {
        // If this is a Service model, use description with parent fallback as requested
        if ($model instanceof Service) {
            $description = trim((string) ($model->description ?? ''));
            if ($description === '' && $model->parent) {
                $description = trim((string) ($model->parent->description ?? ''));
            }
            return $description;
        }

        $class = class_basename($model);
        foreach ($this->allowedModels as $config) {
            if (class_basename($config['model']) === $class) {
                $field = $config['description_field'] ?? 'description';
                return $model->$field ?? '';
            }
        }
        // Fallback: try 'description' property
        return $model->description ?? '';
    }

    /**
     * Build a structured service context to guide AI content generation.
     * Includes site info, service description (with parent fallback), inputs, pricing, and URLs.
     */
    protected function buildServiceContext(Service $service): string
    {
        $siteName = config('app.name') ?: 'Pishkhanak';
        $siteUrl = config('app.url') ?: 'https://pishkhanak.com';
        $serviceUrl = method_exists($service, 'getUrl') ? $service->getUrl() : null;

        $description = trim((string) ($service->description ?? ''));
        if ($description === '' && $service->parent) {
            $description = trim((string) ($service->parent->description ?? ''));
        }

        // Analyze required inputs using the ServiceFormAnalyzer
        $formAnalyzer = app(ServiceFormAnalyzer::class);
        $analysis = $formAnalyzer->analyzeServiceForm($service);
        $requiredFields = $analysis['required_fields'] ?? [];
        $inputsList = [];
        foreach ($requiredFields as $field) {
            $label = $field['label'] ?? ($field['name'] ?? ($field['type'] ?? ''));
            $desc = $field['description'] ?? '';
            $inputsList[] = $desc ? ($label . ' (' . $desc . ')') : $label;
        }
        $inputsText = empty($inputsList) ? 'بدون ورودی اجباری' : implode(', ', $inputsList);

        // Pricing details
        $isPaid = (bool) ($service->is_paid ?? false);
        $price = $service->price ?? 0;
        $currency = $service->currency ?? 'IRT';
        $pricingText = $isPaid && $price > 0
            ? ("{$price} {$currency}")
            : 'رایگان یا وابسته به شرایط سرویس';

        // Hidden fields (exclude from public inputs if any)
        $hidden = method_exists($service, 'getHiddenFields') ? ($service->getHiddenFields() ?: []) : [];
        $hiddenText = empty($hidden) ? '-' : implode(', ', $hidden);

        // Compose a structured context with markers so downstream prompts can detect service context
        $lines = [];
        $lines[] = '[SERVICE_CONTEXT_BEGIN]';
        $lines[] = 'site_name: ' . $siteName;
        $lines[] = 'site_url: ' . $siteUrl;
        if ($serviceUrl) {
            $lines[] = 'service_url: ' . $serviceUrl;
        }
        $lines[] = 'service_title: ' . ($service->title ?? '');
        $lines[] = 'service_slug: ' . ($service->slug ?? '');
        if ($description !== '') {
            $lines[] = 'service_description: ' . $description;
        }
        $lines[] = 'service_inputs: ' . $inputsText;
        $lines[] = 'hidden_inputs: ' . $hiddenText;
        $lines[] = 'service_pricing: ' . $pricingText;
        $lines[] = 'service_currency: ' . $currency;
        $lines[] = 'service_is_paid: ' . ($isPaid ? 'yes' : 'no');
        $lines[] = 'brand_requirement: در کل محتوا نام «' . $siteName . '» و وب‌سایت ' . $siteUrl . ' را به‌صورت طبیعی ذکر کن.';
        $lines[] = 'output_expectation: یک بخش واضح از «خروجی و نتایج قابل مشاهده برای کاربر» ارائه بده.';
        $lines[] = '[SERVICE_CONTEXT_END]';

        return implode("\n", $lines);
    }

    /**
     * Trim field to specified length and ensure it doesn't exceed database limits
     */
    protected function trimField($value, $maxLength)
    {
        if (empty($value)) {
            return null;
        }
        
        // Remove extra whitespace and trim
        $value = trim(preg_replace('/\s+/', ' ', $value));
        
        // If still empty after trimming, return null
        if (empty($value)) {
            return null;
        }
        
        // Truncate to max length if needed
        if (strlen($value) > $maxLength) {
            $value = substr($value, 0, $maxLength - 3) . '...';
        }
        
        return $value;
    }

    public function mount(){
        $this->event_hash = Str::random(7);
        $this->user_id = Auth::id();
        
        // Load default settings if they exist
        $this->loadDefaultSettings();
    }

    public function render()
    {
        return view('livewire.ai-content-generator');
    }

    public function startGeneration()
    {

        // Dispatch initial event to update UI
        event(new CustomUserEvent(
            $this->event_hash,
            'test',
            null,
            'dispatch'
        ));

        Log::info('Starting content generation process', [
            'title' => $this->title,
            'language' => $this->language,
            'model_type' => $this->model_type,
            'model_name' => $this->model_name,
            'search_title' => $this->search_title
        ]);

        try {

            if(isset($this->selectedResult)){
                // $this->selectedResult is direct model instance
                $class = class_basename($this->selectedResult);
                
                // Set target model information for updating after completion
                $this->target_model_type = $class;
                $this->target_model_id = $this->selectedResult->id;
                $this->update_target_after_completion = true;
                
                switch($class) {
                    case 'Service':
                        $this->title = $this->selectedResult->title;
                        $this->short_description = $this->getModelDescription($this->selectedResult);
                        break;

                    case 'Post':
                        $this->title = $this->selectedResult->title;
                        $this->short_description = $this->getModelDescription($this->selectedResult);
                        break;

                    case 'Category':
                        $this->title = $this->selectedResult->title;
                        $this->short_description = $this->getModelDescription($this->selectedResult);
                        break;

                    case 'ServiceCategory':
                        $this->title = $this->selectedResult->title;
                        $this->short_description = $this->getModelDescription($this->selectedResult);
                        break;

                    default:
                        throw new \Exception('Invalid model type: ' . $class);
                }
            }
            // Validate initial input
            $this->validate([
                'title' => 'required|string|max:255',
                'short_description' => 'nullable|string|max:8000',
                'language' => 'required|string',
                'model_type' => 'required|string|in:fast,advanced',
                'headings_number' => 'required|integer|min:1|max:10',
                'sub_headings_number' => 'required|integer|min:1|max:5',
            ]);

            // Save settings as default if requested
            if ($this->save_as_default) {
                $this->saveDefaultSettings();
            }

            // If target is a Service, enrich short_description with dynamic service context
            try {
                if ($this->target_model_type === 'Service' && $this->target_model_id) {
                    $service = Service::find($this->target_model_id);
                    if ($service) {
                        $serviceContext = $this->buildServiceContext($service);
                        if (!empty($serviceContext)) {
                            $this->short_description = trim((string) $this->short_description);
                            $this->short_description = $this->short_description === ''
                                ? $serviceContext
                                : ($this->short_description . "\n\n" . $serviceContext);
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to build service context for AI prompt', [
                    'error' => $e->getMessage(),
                ]);
            }

            // Create a new AiContent record
            $aiContent = AiContent::create([
                'model_type' => $this->target_model_type,
                'model_id' => $this->target_model_id,
                'title' => $this->title,
                'slug' => Str::slug($this->title) . '-' . Str::random(6),
                'short_description' => $this->short_description,
                'language' => $this->language,
                'status' => 'generating',
                'generation_settings' => [
                    'headings_number' => $this->headings_number,
                    'sub_headings_number' => $this->sub_headings_number,
                    'generation_mode' => $this->generation_mode,
                    'tone' => $this->tone,
                    'content_length' => $this->contentLength,
                    'target_audience' => $this->targetAudience,
                    'keywords' => $this->keywords,
                    'content_format' => $this->contentFormat,
                    'custom_instructions' => $this->customInstructions,
                ],
                'generation_progress' => 0,
                'current_generation_step' => 'initialization',
                'generation_started_at' => now(),
                'author_id' => Auth::id(),
                'category_id' => 1, // Assuming default category
            ]);

            Log::info('Created initial AI content record', [
                'content_id' => $aiContent->id,
                'status' => 'generating'
            ]);

            $this->aiContentId = $aiContent->id;

            $this->currentStep = 2;
            $this->progress = 16;

            // Dispatch event to update UI
            event(new CustomUserEvent(
                $this->event_hash,
                'generateHeadings',
                null,
                'function'
            ));

            // $this->generateHeadings();
        } catch (\Exception $e) {
            Log::error('Failed to start generation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function generateHeadings()
    {
        Log::info('Starting headings generation', [
            'content_id' => $this->aiContentId
        ]);

        $aiContent = AiContent::find($this->aiContentId);
        if (!$aiContent) {
            Log::error('AI Content not found for headings generation');
            $this->error = __('ai_content.ai_content_not_found');
            return;
        }

        try {
            // Update status to generating - this will trigger a re-render
            $this->headingGenerationStatus = 'generating';
            $this->totalHeadingsToGenerate = $this->headings_number;
            
            // Dispatch to frontend to trigger generation after UI update
            $this->dispatch('start-heading-generation');

        } catch (\Exception $e) {
            Log::error('Headings generation failed', [
                'error' => $e->getMessage()
            ]);
            $this->error = __('ai_content.generation_failed') . ': ' . $e->getMessage();
            $this->headingGenerationStatus = 'failed';
        }
    }

    public function performHeadingGeneration()
    {
        $aiContent = AiContent::find($this->aiContentId);
        if (!$aiContent) {
            $this->error = __('ai_content.ai_content_not_found');
            return;
        }

        try {
            $aiService = app(AiService::class);
            $headingsJson = $aiService->cleanJson($aiService->generateTitles(
                $this->title,
                $this->short_description,
                $this->language,
                $this->model_type,
                $this->headings_number,
                $this->sub_headings_number,
                $this->generation_mode
            ));

            if (!$headingsJson) {
                throw new \Exception('Failed to generate headings.');
            }

            $headlines = json_decode($headingsJson, true);
            if(is_array($headlines)){
                if(count($headlines) == 1){
                    $aiContent->ai_headings = $headlines[array_key_first($headlines)];
                }else{
                    $aiContent->ai_headings = $headlines;
                }
            }else{
                throw new \Exception('Not a valid array');
            }
            $aiContent->save();

            // Update local properties with generated headings
            $this->headings = $aiContent->ai_headings;
            $this->generatedHeadingsCount = count($this->headings);
            $this->headingGenerationStatus = 'complete';
            
            // Dispatch to frontend to show headings and then proceed
            $this->dispatch('headings-generated');

        } catch (\Exception $e) {
            Log::error('Headings generation failed', [
                'error' => $e->getMessage()
            ]);
            $this->error = __('ai_content.generation_failed') . ': ' . $e->getMessage();
            $this->headingGenerationStatus = 'failed';
            $aiContent = AiContent::find($this->aiContentId);
            if ($aiContent) {
                $aiContent->status = 'failed';
                $aiContent->save();
            }
        }
    }

    public function proceedToSections()
    {
        // Initialize section tracking with timeout handling
        $this->initializeSectionTrackingWithTimeout();
        
        // Update progress and move to next step
        $this->currentStep = 3;
        $this->progress = 32;

        // Dispatch event for next function
        event(new CustomUserEvent(
            $this->event_hash,
            'generateSections',
            null,
            'function'
        ));
    }

    public function generateSections()
    {
        Log::info('Starting sections generation', [
            'content_id' => $this->aiContentId
        ]);

        $aiContent = AiContent::find($this->aiContentId);
        if (!$aiContent || !is_array($aiContent->ai_headings)) {
            $this->error = __('ai_content.invalid_content_headings');
            return;
        }

        try {
            Log::debug('Processing sections for each heading', [
                'headings_count' => count($aiContent->ai_headings)
            ]);

            // Initialize empty sections array
            $aiContent->ai_sections = [];
            $aiContent->save();

            // Set all headings to generating status
            foreach ($this->headings as $index => $heading) {
                $this->updateSectionStatus($index, 'generating');
            }

            // Create jobs for each heading
            $jobs = [];
            foreach ($aiContent->ai_headings as $index => $heading) {
                $jobs[] = new GenerateSectionContentJob(
                    $heading,
                    $aiContent->title,
                    $aiContent->short_description,
                    $aiContent->language,
                    $aiContent->model_type,
                    (int)$index + 1,
                    count($aiContent->ai_headings),
                    $aiContent->id,
                    $this->generation_mode
                );
            }

            // Start monitoring section generation progress
            $this->dispatch('start-section-monitoring');
            
            // Push jobs to queue
            foreach ($jobs as $job) {
                Queue::push($job);
            }

        } catch (\Exception $e) {
            Log::error('Sections generation failed', [
                'content_id' => $this->aiContentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error = __('ai_content.generation_failed');
            $aiContent->status = 'failed';
            $aiContent->save();
        }
    }

    public function monitorSectionGeneration()
    {
        Log::info('Monitoring section generation', [
            'content_id' => $this->aiContentId,
            'total_headings' => count($this->headings)
        ]);

        $aiContent = AiContent::find($this->aiContentId);
        if (!$aiContent) {
            $this->error = 'Content not found for monitoring.';
            return;
        }

        // Check current sections and update status
        $currentSections = $aiContent->ai_sections ?? [];
        $totalHeadings = count($this->headings);

        Log::info('Current sections '.$this->completedSections.' of '.$totalHeadings);
        
        // Count completed sections freshly
        $actualCompletedSections = 0;
        foreach ($this->headings as $index => $heading) {
            if (isset($currentSections[$index])) {
                // Section exists, mark as completed
                if ($this->sectionGenerationStatus[$index] !== 'completed') {
                    $this->updateSectionStatus($index, 'completed');
                }
                $actualCompletedSections++;
            } else {
                // Check if we should mark as failed (based on timeout or other criteria)
                // For now, keep as generating unless explicitly failed
                if ($this->sectionGenerationStatus[$index] === 'pending') {
                    $this->updateSectionStatus($index, 'generating');
                }
            }
        }

        // Update completed sections count
        $this->completedSections = $actualCompletedSections;

        // Calculate progress based on completed sections
        // Section generation takes up 16% of total progress (from 32% to 48%)
        if ($totalHeadings > 0) {
            $progressIncrement = (16 / $totalHeadings) * $this->completedSections;
            $this->progress = 32 + $progressIncrement;
            $this->progress = min($this->progress, 48);
        } else {
            $this->progress = 48;
        }

        Log::info('Progress updated', [
            'completed_sections' => $this->completedSections,
            'total_sections' => $totalHeadings,
            'progress' => $this->progress
        ]);

        // Dispatch progress update to frontend
        $this->dispatch('progress-updated', [
            'progress' => $this->progress,
            'completed_sections' => $this->completedSections,
            'total_sections' => $totalHeadings
        ]);

        // Check if all sections are completed
        if ($this->completedSections >= $totalHeadings) {
            $sections = $aiContent->ai_sections;
            ksort($sections);
            $aiContent->ai_sections = $sections;
            $aiContent->save();
            $this->sections = $aiContent->ai_sections;
            
            $this->progress = 48;
            
            // Only auto-proceed if auto_process is enabled
            if ($this->auto_process) {
                // Move to next step automatically
                event(new CustomUserEvent(
                    $this->event_hash,
                    'generateSummary',
                    null,
                    'function'
                ));
            } else {
                // Stop here and wait for manual approval
                $this->dispatch('sections-completed', [
                    'message' => 'All sections completed! Review and proceed when ready.'
                ]);
            }
        } else {
            // Continue monitoring
            $this->dispatch('continue-section-monitoring');
        }
    }

    public function checkSectionProgress()
    {
        $this->monitorSectionGenerationWithTimeout();
    }
    public function generateSummary()
    {
        Log::info('Starting summary generation', [
            'content_id' => $this->aiContentId
        ]);

        $aiContent = AiContent::find($this->aiContentId);
        if (!$aiContent || !is_array($aiContent->ai_sections)) {
            $this->error = __('ai_content.invalid_content_sections');
            return;
        }

        try {
            Log::debug('Preparing content for summary', [
                'sections_count' => count($aiContent->ai_sections)
            ]);

            $aiService = app(AiService::class);

            $fullContent = '';
            foreach ($aiContent->ai_sections as $section) {
                $fullContent .= $section . "\n";
            }

            $summary = $aiService->generateSummary($fullContent, $aiContent->language, $this->generation_mode);

            Log::info('Successfully generated summary', [
                'content_id' => $this->aiContentId,
                'summary_length' => strlen($summary)
            ]);

            if (!$summary) {
                throw new \Exception('Failed to generate summary.');
            }

            $aiContent->ai_summary = $summary;
            $aiContent->save();

            // Update local properties
            $this->currentStep = 5;
            $this->progress = 64;
            $this->summary = $summary;

            // Dispatch event for next function
            event(new CustomUserEvent(
                $this->event_hash,
                'generateMetaAndSchema',
                null,
                'function'
            ));

        } catch (\Exception $e) {
            Log::error('Summary generation failed', [
                'content_id' => $this->aiContentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error = 'Failed to generate summary.';
            $aiContent->status = 'failed';
            $aiContent->save();

            event(new CustomUserEvent(
                $this->aiContentId,
                'error',
                'Failed to generate summary: ' . $e->getMessage(),
                'notify'
            ));
        }
    }

    public function generateMetaAndSchema()
    {
        Log::info('Starting meta and schema generation', [
            'content_id' => $this->aiContentId
        ]);

        $aiContent = AiContent::find($this->aiContentId);
        if (!$aiContent) {
            $this->error = 'AI Content not found.';
            return;
        }

        try {
            Log::debug('Generating meta information');
            $aiService = app(AiService::class);
            $meta = $aiService->generateMeta($aiContent, $aiContent->language);

            // Log::debug('Generating schema information');
            // $schema = $aiService->generateSchema($aiContent, $aiContent->language);

            Log::info('Successfully generated meta and schema', [
                'content_id' => $this->aiContentId,
                'meta_fields' => array_keys($meta),
                // 'schema_type' => $schema['schema'] ?? 'none'
            ]);

            if (!$meta) {
                throw new \Exception('Failed to generate meta.');
            }

            // Apply length limitations and trimming for database fields
            $aiContent->meta_title = $this->trimField($meta['title'] ?? null, 255);
            $aiContent->meta_description = $meta['description'] ?? null;
            $aiContent->meta_keywords = $this->trimField($meta['keywords'] ?? null, 255);
            $aiContent->og_title = $this->trimField($meta['og_title'] ?? null, 255);
            $aiContent->og_description = $meta['og_description'] ?? null;
            $aiContent->twitter_title = $this->trimField($meta['twitter_title'] ?? null, 255);
            $aiContent->twitter_description = $meta['twitter_description'] ?? null;
            // $aiContent->schema = $schema['schema'] ?? null;
            // $aiContent->json_ld = $schema['json_ld'] ?? null;
            $aiContent->save();

            // Update local properties
            $this->currentStep = 6;
            $this->progress = 80;
            $this->meta = $meta;
            // $this->schema = $schema;

            // Dispatch event for next function
            event(new CustomUserEvent(
                $this->event_hash,
                'generateFAQ',
                null,
                'function'
            ));

        } catch (\Exception $e) {
            Log::error('Meta and schema generation failed', [
                'content_id' => $this->aiContentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error = 'Failed to generate meta and schema.';
            $aiContent->status = 'failed';
            $aiContent->save();

            event(new CustomUserEvent(
                $this->aiContentId,
                'error',
                'Failed to generate meta and schema: ' . $e->getMessage(),
                'notify'
            ));
        }
    }

    public function generateFAQ()
    {
        Log::info('Starting FAQ generation', [
            'content_id' => $this->aiContentId
        ]);

        $aiContent = AiContent::find($this->aiContentId);
        if (!$aiContent) {
            $this->error = 'AI Content not found.';
            return;
        }

        try {
            Log::debug('Calling AI service for FAQ generation');

            $aiService = app(AiService::class);
            $faq = $aiService->generateFAQ(implode('', $aiContent->ai_sections), 10, $aiContent->language);

            Log::info('Successfully generated FAQ', [
                'content_id' => $this->aiContentId,
                'faq_count' => count(json_decode($faq, true))
            ]);

            if (!$faq) {
                throw new \Exception('Failed to generate FAQ.');
            }

            $aiContent->faq = json_decode($faq, true);
            $aiContent->status = 'completed';
            $aiContent->save();

            // Update local properties
            $this->currentStep = 7;
            $this->progress = 100;
            $this->faq = $aiContent->faq;
            $this->status = 'completed';

            $aiContent->save();

            // Update target model if specified
            if ($this->update_target_after_completion && $this->target_model_type && $this->target_model_id) {
                $this->updateTargetModel($aiContent->id);
            }

        } catch (\Exception $e) {
            Log::error('FAQ generation failed', [
                'content_id' => $this->aiContentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error = 'Failed to generate FAQ.';
            $aiContent->status = 'failed';
            $aiContent->save();

            event(new CustomUserEvent(
                $this->aiContentId,
                'error',
                'Failed to generate FAQ: ' . $e->getMessage(),
                'notify'
            ));
        }
    }

    public function resetGenerator()
    {
        $this->reset([
            'title',
            'short_description',
            'language',
            'model_type',
            'currentStep',
            'aiContentId',
            'status',
            'progress',
            'error',
            'target_model_type',
            'target_model_id',
            'update_target_after_completion',
        ]);
    }

    /**
     * Update the target model with the generated AI content ID
     */
    public function updateTargetModel($aiContentId)
    {
        try {
            Log::info('Updating target model with AI content', [
                'model_type' => $this->target_model_type,
                'model_id' => $this->target_model_id,
                'ai_content_id' => $aiContentId
            ]);

            // Get the model class
            $modelConfig = collect($this->allowedModels)->first(function ($config, $key) {
                return str_contains($key, $this->target_model_type) || str_contains($this->target_model_type, class_basename($config['model']));
            });

            if (!$modelConfig) {
                throw new \Exception('Model configuration not found for: ' . $this->target_model_type);
            }

            $modelClass = $modelConfig['model'];
            $targetModel = $modelClass::find($this->target_model_id);

            if (!$targetModel) {
                throw new \Exception('Target model not found: ' . $this->target_model_type . ' ID: ' . $this->target_model_id);
            }

            // Update the content field with the AI content ID
            $targetModel->content = $aiContentId;
            $targetModel->save();

            Log::info('Successfully updated target model', [
                'model_type' => $this->target_model_type,
                'model_id' => $this->target_model_id,
                'ai_content_id' => $aiContentId
            ]);

            // Dispatch success notification
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "محتوا با موفقیت در {$this->target_model_type} ذخیره شد!"
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update target model', [
                'model_type' => $this->target_model_type,
                'model_id' => $this->target_model_id,
                'ai_content_id' => $aiContentId,
                'error' => $e->getMessage()
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'خطا در ذخیره محتوا در مدل هدف: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Analyze content readability and SEO metrics
     */
    public function analyzeContent()
    {
        $aiContent = AiContent::find($this->aiContentId);
        if (!$aiContent) {
            $this->error = 'Content not found for analysis.';
            return;
        }

        try {
            $analysisResults = [
                'readability' => $this->calculateReadabilityScore($aiContent->content),
                'seoScore' => $this->calculateSEOScore($aiContent),
                'keywordDensity' => $this->analyzeKeywordDensity($aiContent->content),
                'contentQuality' => $this->assessContentQuality($aiContent),
                'suggestions' => $this->generateContentSuggestions($aiContent),
            ];

            $aiContent->analysis_results = $analysisResults;
            $aiContent->save();

            $this->aiAnalysisResults = $analysisResults;
        } catch (\Exception $e) {
            Log::error("Content Analysis Error: " . $e->getMessage());
            $this->error = 'Failed to analyze content.';
        }
    }

    /**
     * Calculate readability score using multiple algorithms
     */
    protected function calculateReadabilityScore($content)
    {
        $scores = [
            'fleschKincaid' => $this->calculateFleschKincaidScore($content),
            'gunningFog' => $this->calculateGunningFogScore($content),
            'smog' => $this->calculateSMOGScore($content),
            'automatedReadability' => $this->calculateAutomatedReadabilityIndex($content),
        ];

        return [
            'scores' => $scores,
            'averageScore' => array_sum($scores) / count($scores),
            'readabilityLevel' => $this->determineReadabilityLevel($scores['fleschKincaid']),
            'suggestions' => $this->generateReadabilitySuggestions($scores),
        ];
    }

    /**
     * Calculate SEO score based on various factors
     */
    protected function calculateSEOScore($aiContent)
    {
        $seoFactors = [
            'titleOptimization' => $this->analyzeTitleOptimization($aiContent->title),
            'metaDescriptionQuality' => $this->analyzeMetaDescription($aiContent->meta_description),
            'keywordUsage' => $this->analyzeKeywordUsage($aiContent),
            'contentLength' => $this->analyzeContentLength($aiContent->content),
            'headingStructure' => $this->analyzeHeadingStructure($aiContent),
            'internalLinking' => $this->analyzeInternalLinking($aiContent->content),
        ];

        return [
            'factors' => $seoFactors,
            'overallScore' => $this->calculateOverallSEOScore($seoFactors),
            'improvements' => $this->generateSEOImprovements($seoFactors),
        ];
    }

    /**
     * Generate image suggestions for content
     */
    public function generateImageSuggestions()
    {
        if (!$this->includeImages) {
            return;
        }

        $aiContent = AiContent::find($this->aiContentId);
        if (!$aiContent) {
            $this->error = 'Content not found for image generation.';
            return;
        }

        try {
            $aiService = app(AiService::class);
            $suggestions = $aiService->generateImageSuggestions(
                $aiContent->content ?? $aiContent->title,
                $this->language
            );

            $aiContent->image_suggestions = $suggestions;
            $aiContent->save();
        } catch (\Exception $e) {
            Log::error("Image Suggestions Error: " . $e->getMessage());
            $this->error = 'Failed to generate image suggestions.';
        }
    }

    /**
     * Auto-save content periodically
     */
    public function autoSave()
    {
        if (!$this->enableAutoSave || !$this->aiContentId) {
            return;
        }

        try {
            $aiContent = AiContent::find($this->aiContentId);
            if ($aiContent) {
                $aiContent->auto_save_data = [
                    'current_step' => $this->currentStep,
                    'progress' => $this->progress,
                    'last_saved' => now(),
                ];
                $aiContent->save();
                $this->lastAutoSave = now();
            }
        } catch (\Exception $e) {
            Log::error("Auto-save Error: " . $e->getMessage());
        }
    }

    /**
     * Track and manage content revisions
     */
    public function saveRevision()
    {
        $aiContent = AiContent::find($this->aiContentId);
        if (!$aiContent) {
            $this->error = 'Content not found for revision.';
            return;
        }

        try {
            $revision = [
                'version' => ++$this->revisionCount,
                'content' => $aiContent->content,
                'timestamp' => now()->toISOString(),
                'changes' => $this->detectContentChanges($aiContent),
            ];

            $aiContent->revisions = array_merge($aiContent->revisions ?? [], [$revision]);
            $aiContent->save();
        } catch (\Exception $e) {
            Log::error("Revision Save Error: " . $e->getMessage());
            $this->error = 'Failed to save content revision.';
        }
    }

    /**
     * Export content in different formats
     */
    public function exportContent($format = 'html')
    {
        $aiContent = AiContent::find($this->aiContentId);
        if (!$aiContent) {
            $this->error = 'Content not found for export.';
            return;
        }

        try {
            switch ($format) {
                case 'pdf':
                    return $this->exportToPDF($aiContent);
                case 'word':
                    return $this->exportToWord($aiContent);
                case 'markdown':
                    return $this->exportToMarkdown($aiContent);
                default:
                    return $this->exportToHTML($aiContent);
            }
        } catch (\Exception $e) {
            Log::error("Content Export Error: " . $e->getMessage());
            $this->error = 'Failed to export content.';
        }
    }

    /**
     * Generate content performance predictions
     */
    public function generatePerformancePredictions()
    {
        $aiContent = AiContent::find($this->aiContentId);
        if (!$aiContent) {
            $this->error = 'Content not found for prediction.';
            return;
        }

        try {
            $aiService = app(AiService::class);
            $predictions = $aiService->predictContentPerformance(
                $aiContent->content ?? $aiContent->title,
                ['target_audience' => $this->targetAudience, 'format' => $this->contentFormat, 'keywords' => $this->keywords]
            );

            $aiContent->performance_predictions = [
                'engagement_score' => $predictions['engagement'] ?? null,
                'conversion_potential' => $predictions['conversion'] ?? null,
                'seo_ranking_potential' => $predictions['seo'] ?? null,
                'social_share_probability' => $predictions['social'] ?? null,
                'target_audience_match' => $predictions['audience_match'] ?? null,
            ];
            $aiContent->save();
        } catch (\Exception $e) {
            Log::error("Performance Prediction Error: " . $e->getMessage());
            $this->error = 'Failed to generate performance predictions.';
        }
    }

    public function copyContent()
    {
        try {
            $aiContent = AiContent::find($this->aiContentId);
            if (!$aiContent) {
                throw new \Exception('Content not found.');
            }

            // Dispatch a browser event to copy the content
            $this->dispatch('copyContent', [
                'content' => view('components.generated-content', [
                    'title' => $this->title,
                    'meta' => $this->meta,
                    'headings' => $aiContent->ai_headings,
                    'sections' => $aiContent->ai_sections,
                    'faq' => $aiContent->faq
                ])->render()
            ]);

            // Show success message
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Content copied to clipboard!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to copy content: ' . $e->getMessage()
            ]);
        }
    }

    // Add this method to handle search
    public function updatedSearchTitle($value)
    {
        if (!$this->model_name || !isset($this->allowedModels[$this->model_name])) {
            $this->searchResults = [];
            return;
        }

        if (strlen($value) >= 2) {
            try {
                $modelConfig = $this->allowedModels[$this->model_name];
                $modelClass = $modelConfig['model'];

                $query = $modelClass::query();

                // Build search query using configured searchable fields
                $query->where(function ($q) use ($value, $modelConfig) {
                    foreach ($modelConfig['searchable_fields'] as $index => $field) {
                        if ($index === 0) {
                            $q->where($field, 'like', "%{$value}%");
                        } else {
                            $q->orWhere($field, 'like', "%{$value}%");
                        }
                    }
                });

                $this->searchResults = $query->limit(10)->get();

            } catch (\Exception $e) {
                Log::error('Search failed', [
                    'model' => $this->model_name,
                    'error' => $e->getMessage()
                ]);
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Search failed: ' . $e->getMessage()
                ]);
                $this->searchResults = [];
            }
        } else {
            $this->searchResults = [];
        }
    }

    // Add this method to handle result selection
    public function selectResult($id)
    {
        if (!$this->model_name || !isset($this->allowedModels[$this->model_name])) {
            return;
        }

        try {
            $modelConfig = $this->allowedModels[$this->model_name];
            $modelClass = $modelConfig['model'];

            $model = $modelClass::find($id);

            if ($model) {
                $this->selectedResult = $model;
                $this->title = $model->{$modelConfig['title_field']};
                $this->short_description = $model->{$modelConfig['description_field']} ?? '';
                $this->search_title = $model->{$modelConfig['title_field']};
                $this->searchResults = [];

                // Add these lines for better UX feedback
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => ucfirst($this->model_name) . ' selected successfully!'
                ]);

                // Clear search results and update UI
                $this->dispatch('resultSelected');
            }
        } catch (\Exception $e) {
            Log::error('Selection failed', [
                'model' => $this->model_name,
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to select ' . strtolower($this->model_name)
            ]);
        }
    }

    public function clearSelectedResult()
    {
        $this->selectedResult = null;
        $this->search_title = '';
        $this->searchResults = [];  // Also clear search results if you have this property
    }

    // Helper method to get allowed model names
    public function getAllowedModelNames()
    {
        return array_keys($this->allowedModels);
    }

    public function getAllowedModelTitleField($modelName)
    {
        return $this->allowedModels[$modelName]['title_field'];
    }

    /**
     * Refresh headings display manually
     */
    public function refreshHeadings()
    {
        if ($this->aiContentId) {
            $aiContent = AiContent::find($this->aiContentId);
            if ($aiContent && $aiContent->ai_headings) {
                $this->headings = $aiContent->ai_headings;
                $this->generatedHeadingsCount = count($this->headings);
                $this->headingGenerationStatus = 'complete';
                
                // Dispatch update event
                event(new CustomUserEvent(
                    $this->event_hash,
                    'headingsRefreshed',
                    [
                        'headings' => $this->headings,
                        'count' => $this->generatedHeadingsCount,
                        'status' => $this->headingGenerationStatus
                    ],
                    'update'
                ));
            }
        }
    }

    /**
     * Remove a heading by index
     */
    public function removeHeading($index)
    {
        if (isset($this->headings[$index])) {
            unset($this->headings[$index]);
            $this->headings = array_values($this->headings); // Re-index array
            $this->generatedHeadingsCount = count($this->headings);
            
            // Update database
            if ($this->aiContentId) {
                $aiContent = AiContent::find($this->aiContentId);
                if ($aiContent) {
                    $aiContent->ai_headings = $this->headings;
                    $aiContent->save();
                }
            }
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Heading removed successfully!'
            ]);
        }
    }

    /**
     * Rebuild a specific heading
     */
    public function rebuildHeading($index)
    {
        if (!isset($this->headings[$index])) {
            return;
        }

        try {
            $aiService = app(AiService::class);
            
            // Generate new heading for this specific position
            $headingsJson = $aiService->cleanJson($aiService->generateTitles(
                $this->title,
                $this->short_description,
                $this->language,
                $this->model_type,
                1, // Generate only 1 heading
                $this->sub_headings_number
            ));

            $newHeadings = json_decode($headingsJson, true);
            if (is_array($newHeadings) && count($newHeadings) > 0) {
                if (count($newHeadings) == 1) {
                    $newHeading = $newHeadings[array_key_first($newHeadings)][0] ?? $newHeadings[0];
                } else {
                    $newHeading = $newHeadings[0];
                }
                
                $this->headings[$index] = $newHeading;
                
                // Update database
                if ($this->aiContentId) {
                    $aiContent = AiContent::find($this->aiContentId);
                    if ($aiContent) {
                        $aiContent->ai_headings = $this->headings;
                        $aiContent->save();
                    }
                }
                
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Heading rebuilt successfully!'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Heading rebuild failed', ['error' => $e->getMessage()]);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to rebuild heading: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Move heading up
     */
    public function moveHeadingUp($index)
    {
        if ($index > 0 && isset($this->headings[$index])) {
            $temp = $this->headings[$index];
            $this->headings[$index] = $this->headings[$index - 1];
            $this->headings[$index - 1] = $temp;
            
            $this->updateHeadingsInDatabase();
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Heading moved up!'
            ]);
        }
    }

    /**
     * Move heading down
     */
    public function moveHeadingDown($index)
    {
        if ($index < count($this->headings) - 1 && isset($this->headings[$index])) {
            $temp = $this->headings[$index];
            $this->headings[$index] = $this->headings[$index + 1];
            $this->headings[$index + 1] = $temp;
            
            $this->updateHeadingsInDatabase();
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Heading moved down!'
            ]);
        }
    }

    /**
     * Update headings order after drag and drop
     */
    public function updateHeadingsOrder($orderedIndexes)
    {
        $newHeadings = [];
        foreach ($orderedIndexes as $index) {
            if (isset($this->headings[$index])) {
                $newHeadings[] = $this->headings[$index];
            }
        }
        
        $this->headings = $newHeadings;
        $this->updateHeadingsInDatabase();
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Headings reordered successfully!'
        ]);
    }

    /**
     * Helper method to update headings in database
     */
    private function updateHeadingsInDatabase()
    {
        if ($this->aiContentId) {
            $aiContent = AiContent::find($this->aiContentId);
            if ($aiContent) {
                $aiContent->ai_headings = $this->headings;
                $aiContent->save();
            }
        }
    }

    /**
     * Initialize section generation status tracking
     */
    public function initializeSectionTracking()
    {
        $this->sectionGenerationStatus = [];
        $this->completedSections = 0;
        $this->failedSections = 0;
        
        foreach ($this->headings as $index => $heading) {
            $this->sectionGenerationStatus[$index] = 'pending'; // pending, generating, completed, failed
        }
    }

    /**
     * Update section status for a specific heading
     */
    public function updateSectionStatus($index, $status)
    {
        $previousStatus = $this->sectionGenerationStatus[$index] ?? 'pending';
        $this->sectionGenerationStatus[$index] = $status;
        
        // Only update counters if status actually changed
        if ($previousStatus !== $status) {
            // Adjust counters based on previous status
            if ($previousStatus === 'completed') {
                $this->completedSections--;
            } elseif ($previousStatus === 'failed') {
                $this->failedSections--;
            }
            
            // Adjust counters based on new status
            if ($status === 'completed') {
                $this->completedSections++;
            } elseif ($status === 'failed') {
                $this->failedSections++;
            }
            
            // Recalculate progress
            $totalHeadings = count($this->headings);
            if ($totalHeadings > 0) {
                $progressIncrement = (16 / $totalHeadings) * $this->completedSections;
                $this->progress = 32 + $progressIncrement;
                $this->progress = min($this->progress, 48);
            }
            
            Log::info('Section status updated', [
                'index' => $index,
                'status' => $status,
                'completed' => $this->completedSections,
                'failed' => $this->failedSections,
                'progress' => $this->progress
            ]);

            // Dispatch progress update to frontend
            $this->dispatch('progress-updated', [
                'progress' => $this->progress,
                'completed_sections' => $this->completedSections,
                'total_sections' => count($this->headings)
            ]);
        }
        
        // Trigger UI update
        $this->dispatch('section-status-updated', [
            'index' => $index,
            'status' => $status,
            'completed' => $this->completedSections,
            'failed' => $this->failedSections,
            'total' => count($this->headings),
            'progress' => $this->progress
        ]);
    }

    /**
     * Regenerate all headings
     */
    public function regenerateAllHeadings()
    {
        if (!$this->aiContentId) {
            $this->error = 'No content ID available for regeneration.';
            return;
        }

        try {
            // Reset heading status
            $this->headingGenerationStatus = 'generating';
            $this->headings = [];
            $this->generatedHeadingsCount = 0;
            
            // Dispatch to frontend to trigger generation
            $this->dispatch('start-heading-generation');
            
            $this->dispatch('notify', [
                'type' => 'info',
                'message' => 'Regenerating all headings...'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Headings regeneration failed', ['error' => $e->getMessage()]);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to regenerate headings: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Skip to next step (for manual progression)
     */
    public function skipToNextStep()
    {
        switch ($this->currentStep) {
            case 2:
                $this->proceedToSections();
                break;
            case 3:
                $this->proceedToSummary();
                break;
            case 4:
                $this->proceedToMeta();
                break;
            case 5:
                $this->proceedToFAQ();
                break;
            default:
                $this->dispatch('notify', [
                    'type' => 'warning',
                    'message' => 'No next step available.'
                ]);
        }
    }

    /**
     * Proceed to summary generation (manual trigger)
     */
    public function proceedToSummary()
    {
        // Check if image generation is enabled and sections are complete
        if ($this->imageGenerationEnabled && $this->completedSections >= count($this->headings)) {
            $this->startImageGeneration();
            return;
        }
        
        // Update step and progress immediately
        $this->currentStep = 4;
        $this->progress = 48;
        
        // Trigger summary generation
        event(new CustomUserEvent(
            $this->event_hash,
            'generateSummary',
            null,
            'function'
        ));
    }

    /**
     * Proceed to meta generation (manual trigger)
     */
    public function proceedToMeta()
    {
        event(new CustomUserEvent(
            $this->event_hash,
            'generateMetaAndSchema',
            null,
            'function'
        ));
    }

    /**
     * Proceed to FAQ generation (manual trigger)
     */
    public function proceedToFAQ()
    {
        event(new CustomUserEvent(
            $this->event_hash,
            'generateFAQ',
            null,
            'function'
        ));
    }

    /**
     * Save current settings as default to Redis
     */
    public function saveDefaultSettings()
    {
        try {
            $settings = [
                'language' => $this->language,
                'model_type' => $this->model_type,
                'generation_mode' => $this->generation_mode,
                'headings_number' => $this->headings_number,
                'sub_headings_number' => $this->sub_headings_number,
                'auto_process' => $this->auto_process,
                'generation_type' => $this->generation_type,
                'tone' => $this->tone,
                'content_length' => $this->contentLength,
                'target_audience' => $this->targetAudience,
                'keywords' => $this->keywords,
                'include_images' => $this->includeImages,
                'image_generation_enabled' => $this->imageGenerationEnabled,
                'content_format' => $this->contentFormat,
                'custom_instructions' => $this->customInstructions,
                'enable_auto_save' => $this->enableAutoSave,
                'saved_at' => now()->toISOString(),
            ];

            $redisKey = "ai_content_default_settings:user:" . Auth::id();
            Redis::setex($redisKey, 86400 * 365, json_encode($settings)); // Store for 1 year
            
            Log::info('Default settings saved for user', [
                'user_id' => Auth::id(),
                'settings' => $settings
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => __('ai_content.default_settings_saved')
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to save default settings', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => __('ai_content.failed_to_save_settings')
            ]);
        }
    }

    /**
     * Load default settings from Redis
     */
    public function loadDefaultSettings()
    {
        try {
            $redisKey = "ai_content_default_settings:user:" . Auth::id();
            $settingsJson = Redis::get($redisKey);

            if ($settingsJson) {
                $settings = json_decode($settingsJson, true);
                
                if (is_array($settings)) {
                    // Load all available settings
                    $this->language = $settings['language'] ?? $this->language;
                    $this->model_type = $settings['model_type'] ?? $this->model_type;
                    $this->generation_mode = $settings['generation_mode'] ?? $this->generation_mode;
                    $this->headings_number = $settings['headings_number'] ?? $this->headings_number;
                    $this->sub_headings_number = $settings['sub_headings_number'] ?? $this->sub_headings_number;
                    $this->auto_process = $settings['auto_process'] ?? $this->auto_process;
                    $this->generation_type = $settings['generation_type'] ?? $this->generation_type;
                    $this->tone = $settings['tone'] ?? $this->tone;
                    $this->contentLength = $settings['content_length'] ?? $this->contentLength;
                    $this->targetAudience = $settings['target_audience'] ?? $this->targetAudience;
                    $this->keywords = $settings['keywords'] ?? $this->keywords;
                    $this->includeImages = $settings['include_images'] ?? $this->includeImages;
                    $this->imageGenerationEnabled = $settings['image_generation_enabled'] ?? $this->imageGenerationEnabled;
                    $this->contentFormat = $settings['content_format'] ?? $this->contentFormat;
                    $this->customInstructions = $settings['custom_instructions'] ?? $this->customInstructions;
                    $this->enableAutoSave = $settings['enable_auto_save'] ?? $this->enableAutoSave;

                    Log::info('Default settings loaded for user', [
                        'user_id' => Auth::id(),
                        'saved_at' => $settings['saved_at'] ?? 'unknown'
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to load default settings', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            // Don't show error to user as this is not critical
        }
    }

    /**
     * Clear saved default settings
     */
    public function clearDefaultSettings()
    {
        try {
            $redisKey = "ai_content_default_settings:user:" . Auth::id();
            Redis::del($redisKey);

            Log::info('Default settings cleared for user', [
                'user_id' => Auth::id()
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => __('ai_content.default_settings_cleared')
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to clear default settings', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => __('ai_content.failed_to_clear_settings')
            ]);
        }
    }

    /**
     * Check if user has saved default settings
     */
    public function hasDefaultSettings()
    {
        try {
            $redisKey = "ai_content_default_settings:user:" . Auth::id();
            return Redis::exists($redisKey);
        } catch (\Exception $e) {
            Log::error('Failed to check default settings', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Start editing a heading
     */
    public function startEditingHeading($index)
    {
        if (isset($this->headings[$index])) {
            $this->editingHeading = $index;
            $this->editingText = $this->headings[$index]['title'];
            $this->editingSubHeading = null; // Stop editing sub-heading if any
        }
    }

    /**
     * Save edited heading
     */
    public function saveHeading()
    {
        if ($this->editingHeading !== null && isset($this->headings[$this->editingHeading])) {
            $this->validate([
                'editingText' => 'required|string|max:255'
            ]);

            $this->headings[$this->editingHeading]['title'] = trim($this->editingText);
            $this->updateHeadingsInDatabase();
            
            $this->editingHeading = null;
            $this->editingText = '';
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => __('ai_content.heading_updated_successfully')
            ]);
        }
    }

    /**
     * Cancel editing heading
     */
    public function cancelEditingHeading()
    {
        $this->editingHeading = null;
        $this->editingText = '';
    }

    /**
     * Start editing a sub-heading
     */
    public function startEditingSubHeading($headingIndex, $subHeadingIndex)
    {
        if (isset($this->headings[$headingIndex]['sub_headlines'][$subHeadingIndex])) {
            $this->editingSubHeading = [$headingIndex, $subHeadingIndex];
            $this->editingText = $this->headings[$headingIndex]['sub_headlines'][$subHeadingIndex];
            $this->editingHeading = null; // Stop editing heading if any
        }
    }

    /**
     * Save edited sub-heading
     */
    public function saveSubHeading()
    {
        if ($this->editingSubHeading !== null && is_array($this->editingSubHeading)) {
            [$headingIndex, $subHeadingIndex] = $this->editingSubHeading;
            
            if (isset($this->headings[$headingIndex]['sub_headlines'][$subHeadingIndex])) {
                $this->validate([
                    'editingText' => 'required|string|max:255'
                ]);

                $this->headings[$headingIndex]['sub_headlines'][$subHeadingIndex] = trim($this->editingText);
                $this->updateHeadingsInDatabase();
                
                $this->editingSubHeading = null;
                $this->editingText = '';
                
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => __('ai_content.sub_heading_updated_successfully')
                ]);
            }
        }
    }

    /**
     * Cancel editing sub-heading
     */
    public function cancelEditingSubHeading()
    {
        $this->editingSubHeading = null;
        $this->editingText = '';
    }

    /**
     * Add new heading
     */
    public function addNewHeading()
    {
        $newHeading = [
            'title' => __('ai_content.new_heading_title'),
            'sub_headlines' => [__('ai_content.new_sub_heading_title')]
        ];
        
        $this->headings[] = $newHeading;
        $this->generatedHeadingsCount = count($this->headings);
        $this->updateHeadingsInDatabase();
        
        // Start editing the new heading immediately
        $this->startEditingHeading(count($this->headings) - 1);
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('ai_content.new_heading_added')
        ]);
    }

    /**
     * Add new sub-heading to a specific heading
     */
    public function addSubHeading($headingIndex)
    {
        if (isset($this->headings[$headingIndex])) {
            if (!isset($this->headings[$headingIndex]['sub_headlines'])) {
                $this->headings[$headingIndex]['sub_headlines'] = [];
            }
            
            $this->headings[$headingIndex]['sub_headlines'][] = __('ai_content.new_sub_heading_title');
            $this->updateHeadingsInDatabase();
            
            // Start editing the new sub-heading immediately
            $subHeadingIndex = count($this->headings[$headingIndex]['sub_headlines']) - 1;
            $this->startEditingSubHeading($headingIndex, $subHeadingIndex);
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => __('ai_content.new_sub_heading_added')
            ]);
        }
    }

    /**
     * Remove sub-heading
     */
    public function removeSubHeading($headingIndex, $subHeadingIndex)
    {
        if (isset($this->headings[$headingIndex]['sub_headlines'][$subHeadingIndex])) {
            unset($this->headings[$headingIndex]['sub_headlines'][$subHeadingIndex]);
            $this->headings[$headingIndex]['sub_headlines'] = array_values($this->headings[$headingIndex]['sub_headlines']);
            $this->updateHeadingsInDatabase();
            
            // Cancel editing if we were editing this sub-heading
            if ($this->editingSubHeading !== null && 
                $this->editingSubHeading[0] === $headingIndex && 
                $this->editingSubHeading[1] === $subHeadingIndex) {
                $this->cancelEditingSubHeading();
            }
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => __('ai_content.sub_heading_removed')
            ]);
        }
    }

    /**
     * Handle key press events for editing
     */
    public function handleKeyPress($key)
    {
        if ($key === 'Enter') {
            if ($this->editingHeading !== null) {
                $this->saveHeading();
            } elseif ($this->editingSubHeading !== null) {
                $this->saveSubHeading();
            }
        } elseif ($key === 'Escape') {
            if ($this->editingHeading !== null) {
                $this->cancelEditingHeading();
            } elseif ($this->editingSubHeading !== null) {
                $this->cancelEditingSubHeading();
            }
        }
    }

    /**
     * Click on a heading to show section preview
     */
    public function clickHeading($index)
    {
        if (isset($this->headings[$index])) {
            $this->selectedSection = $index;
            $this->loadSectionPreview($index);
        }
    }

    /**
     * Load section preview content
     */
    public function loadSectionPreview($index)
    {
        $aiContent = AiContent::find($this->aiContentId);
        if ($aiContent && isset($aiContent->ai_sections[$index])) {
            $this->sectionPreview = $aiContent->ai_sections[$index];
            $this->showSectionPreview = true;
            
            $this->dispatch('show-section-preview', [
                'index' => $index,
                'title' => $this->headings[$index]['title'] ?? 'Section ' . ($index + 1)
            ]);
        } else {
            $this->sectionPreview = '';
            $this->showSectionPreview = false;
            
            $this->dispatch('notify', [
                'type' => 'info',
                'message' => 'این بخش هنوز در حال تولید است یا محتوایی ندارد.'
            ]);
        }
    }

    /**
     * Close section preview
     */
    public function closeSectionPreview()
    {
        $this->showSectionPreview = false;
        $this->selectedSection = null;
        $this->sectionPreview = '';
    }

    /**
     * Start editing a section
     */
    public function editSection($index)
    {
        $aiContent = AiContent::find($this->aiContentId);
        if ($aiContent && isset($aiContent->ai_sections[$index])) {
            $this->editingSection = $index;
            $this->editingSectionContent = $aiContent->ai_sections[$index];
            $this->closeSectionPreview();
            
            $this->dispatch('start-section-edit', [
                'index' => $index,
                'title' => $this->headings[$index]['title'] ?? 'Section ' . ($index + 1)
            ]);
        }
    }

    /**
     * Save edited section content
     */
    public function saveSectionEdit()
    {
        if ($this->editingSection !== null) {
            $this->validate([
                'editingSectionContent' => 'required|string|min:50'
            ]);

            $aiContent = AiContent::find($this->aiContentId);
            if ($aiContent) {
                $sections = $aiContent->ai_sections ?? [];
                $sections[$this->editingSection] = $this->editingSectionContent;
                $aiContent->ai_sections = $sections;
                $aiContent->save();
                
                // Update local sections
                $this->sections = $sections;
                
                $this->editingSection = null;
                $this->editingSectionContent = '';
                
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'بخش با موفقیت به‌روزرسانی شد!'
                ]);
            }
        }
    }

    /**
     * Cancel section editing
     */
    public function cancelSectionEdit()
    {
        $this->editingSection = null;
        $this->editingSectionContent = '';
    }

    /**
     * Rebuild/regenerate a specific section
     */
    public function rebuildSection($index)
    {
        if (!isset($this->headings[$index])) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'شماره بخش نامعتبر است.'
            ]);
            return;
        }

        try {
            $aiContent = AiContent::find($this->aiContentId);
            if (!$aiContent) {
                throw new \Exception('AI Content not found');
            }

            // Update status to generating
            $this->updateSectionStatus($index, 'generating');
            $this->sectionStartTime[$index] = time();
            $this->sectionRetryCount[$index] = ($this->sectionRetryCount[$index] ?? 0) + 1;

            // Create new job for this section
            $job = new GenerateSectionContentJob(
                $this->headings[$index],
                $aiContent->title,
                $aiContent->short_description,
                $aiContent->language,
                $aiContent->model_type,
                $index + 1,
                count($this->headings),
                $aiContent->id,
                $this->generation_mode
            );
            
            Queue::push($job);
            
            // Start monitoring for this specific section
            $this->dispatch('monitor-section-rebuild', ['index' => $index]);
            
            $this->dispatch('notify', [
                'type' => 'info',
                'message' => 'Regenerating section: ' . ($this->headings[$index]['title'] ?? 'Section ' . ($index + 1))
            ]);
            
        } catch (\Exception $e) {
            Log::error('Section rebuild failed', [
                'index' => $index,
                'error' => $e->getMessage()
            ]);
            
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to rebuild section: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Remove a section
     */
    public function removeSection($index)
    {
        if (!isset($this->headings[$index])) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'شماره بخش نامعتبر است.'
            ]);
            return;
        }

        try {
            $aiContent = AiContent::find($this->aiContentId);
            if ($aiContent) {
                $sections = $aiContent->ai_sections ?? [];
                unset($sections[$index]);
                $aiContent->ai_sections = $sections;
                $aiContent->save();
                
                // Update local sections
                $this->sections = $sections;
                
                // Update section status
                $this->updateSectionStatus($index, 'removed');
                
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'بخش با موفقیت حذف شد!'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Section removal failed', [
                'index' => $index,
                'error' => $e->getMessage()
            ]);
            
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'حذف بخش شکست خورد: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Check for section timeouts and handle retries
     */
    public function checkSectionTimeouts()
    {
        $currentTime = time();
        $timeoutOccurred = false;

        foreach ($this->sectionStartTime as $index => $startTime) {
            if ($this->sectionGenerationStatus[$index] === 'generating') {
                if ($currentTime - $startTime > $this->sectionTimeout) {
                    $retryCount = $this->sectionRetryCount[$index] ?? 0;
                    
                    if ($retryCount < $this->maxRetries) {
                        // Retry the section
                        $this->retrySectionGeneration($index);
                        $timeoutOccurred = true;
                    } else {
                        // Max retries reached, mark as failed
                        $this->updateSectionStatus($index, 'failed');
                        $this->dispatch('notify', [
                            'type' => 'error',
                            'message' => 'تولید بخش پس از ' . $this->maxRetries . ' تلاش شکست خورد: ' . 
                                       ($this->headings[$index]['title'] ?? 'بخش ' . ($index + 1))
                        ]);
                    }
                }
            }
        }

        return $timeoutOccurred;
    }

    /**
     * Retry section generation
     */
    public function retrySectionGeneration($index)
    {
        try {
            $aiContent = AiContent::find($this->aiContentId);
            if (!$aiContent) {
                throw new \Exception('AI Content not found');
            }

            // Update retry count and start time
            $this->sectionRetryCount[$index] = ($this->sectionRetryCount[$index] ?? 0) + 1;
            $this->sectionStartTime[$index] = time();
            
            // Create new job for this section
            $job = new GenerateSectionContentJob(
                $this->headings[$index],
                $aiContent->title,
                $aiContent->short_description,
                $aiContent->language,
                $aiContent->model_type,
                $index + 1,
                count($this->headings),
                $aiContent->id,
                $this->generation_mode
            );
            
            Queue::push($job);
            
            $retryCount = $this->sectionRetryCount[$index];
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'تلاش مجدد برای تولید بخش (تلاش ' . $retryCount . '/' . $this->maxRetries . '): ' . 
                           ($this->headings[$index]['title'] ?? 'بخش ' . ($index + 1))
            ]);
            
        } catch (\Exception $e) {
            Log::error('Section retry failed', [
                'index' => $index,
                'error' => $e->getMessage()
            ]);
            
            $this->updateSectionStatus($index, 'failed');
        }
    }

    /**
     * Enhanced section monitoring with timeout handling
     */
    public function monitorSectionGenerationWithTimeout()
    {
        // Check for timeouts first
        $this->checkSectionTimeouts();
        
        // Continue with normal monitoring
        $this->monitorSectionGeneration();
    }

    /**
     * Initialize section tracking with timeout handling
     */
    public function initializeSectionTrackingWithTimeout()
    {
        $this->initializeSectionTracking();
        
        // Initialize timeout tracking
        $this->sectionStartTime = [];
        $this->sectionRetryCount = [];
        
        foreach ($this->headings as $index => $heading) {
            $this->sectionStartTime[$index] = time();
            $this->sectionRetryCount[$index] = 0;
        }
    }

    // ===== IMAGE GENERATION METHODS =====

    /**
     * Start image generation process
     */
    public function startImageGeneration()
    {
        try {
            $imageService = app(\App\Services\ImageGenerationService::class);
            $this->imageGenerationSettings = $imageService->getImageSettings();
            
            Log::info('Starting image generation', [
                'ai_content_id' => $this->aiContentId,
                'headings_count' => count($this->headings),
                'settings' => $this->imageGenerationSettings
            ]);

            // Update progress and step
            $this->currentStep = 4; // Set to step 4 (image generation)
            $this->progress = 50;
            
            $this->dispatch('progress-updated', [
                'progress' => $this->progress,
                'step' => $this->currentStep
            ]);

            // Generate prompts for all headings
            $prompts = $imageService->generateImagePrompts($this->headings, $this->language, $this->imageGenerationSettings);
            
            // Initialize image generation status
            $this->imageGenerationStatus = [];
            foreach ($this->headings as $index => $heading) {
                $this->imageGenerationStatus[$index] = 'generating';
            }

            // Dispatch jobs for concurrent image generation
            foreach ($prompts as $index => $promptData) {
                \App\Jobs\GenerateImageJob::dispatch(
                    $this->aiContentId,
                    $index,
                    $promptData['prompt'],
                    $promptData['title'],
                    $this->imageGenerationSettings
                );
            }

            $this->dispatch('notify', [
                'type' => 'info',
                'message' => 'در حال تولید تصاویر برای ' . count($this->headings) . ' بخش...'
            ]);

            // Start monitoring image generation
            $this->monitorImageGeneration();

        } catch (\Exception $e) {
            Log::error('Failed to start image generation', [
                'error' => $e->getMessage(),
                'ai_content_id' => $this->aiContentId
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'شروع تولید تصاویر شکست خورد: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Monitor image generation progress
     */
    public function monitorImageGeneration()
    {
        try {
            $aiContent = AiContent::find($this->aiContentId);
            if (!$aiContent || !$aiContent->ai_thumbnails) {
                return;
            }

            $thumbnails = $aiContent->ai_thumbnails;
            $completedCount = 0;
            $failedCount = 0;

            foreach ($this->headings as $index => $heading) {
                if (isset($thumbnails[$index])) {
                    $status = $thumbnails[$index]['status'] ?? 'pending';
                    
                    if ($status === 'pending_selection') {
                        $this->imageGenerationStatus[$index] = 'completed';
                        $this->generatedImages[$index] = $thumbnails[$index]['images'];
                        $completedCount++;
                    } elseif ($status === 'failed') {
                        $this->imageGenerationStatus[$index] = 'failed';
                        $failedCount++;
                    }
                }
            }

            // Update progress
            $totalSections = count($this->headings);
            if ($totalSections > 0) {
                $progressPercent = 50 + (($completedCount + $failedCount) / $totalSections) * 15; // 15% for image generation
                $this->progress = min(65, $progressPercent);
                
                $this->dispatch('progress-updated', [
                    'progress' => $this->progress
                ]);
            }

            // Check if all images are generated
            if (($completedCount + $failedCount) >= $totalSections) {
                $this->completeImageGeneration($completedCount, $failedCount);
            } else {
                // Continue monitoring
                $this->dispatch('continue-image-monitoring');
            }

        } catch (\Exception $e) {
            Log::error('Image generation monitoring failed', [
                'error' => $e->getMessage(),
                'ai_content_id' => $this->aiContentId
            ]);
        }
    }

    /**
     * Complete image generation process
     */
    public function completeImageGeneration($completedCount, $failedCount)
    {
        $totalSections = count($this->headings);
        
        if ($completedCount > 0) {
            $this->showImageSelection = true;
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "تولید تصاویر کامل شد! {$completedCount} از {$totalSections} تصویر آماده انتخاب است."
            ]);
        } else {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'تولید تصاویر شکست خورد. ادامه به مرحله خلاصه...'
            ]);
            $this->proceedToActualSummary();
        }
    }

    /**
     * Select image for a section
     */
    public function selectImage($sectionIndex, $imageIndex)
    {
        try {
            $aiContent = AiContent::find($this->aiContentId);
            if (!$aiContent) {
                throw new \Exception('AI Content not found');
            }

            $thumbnails = $aiContent->ai_thumbnails ?? [];
            
            if (isset($thumbnails[$sectionIndex]) && isset($thumbnails[$sectionIndex]['images'][$imageIndex])) {
                $thumbnails[$sectionIndex]['selected_image'] = $imageIndex;
                $thumbnails[$sectionIndex]['status'] = 'selected';
                
                $aiContent->update(['ai_thumbnails' => $thumbnails]);
                
                $this->selectedImages[$sectionIndex] = $imageIndex;
                
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'تصویر برای بخش انتخاب شد!'
                ]);
                
                // Check if all images are selected
                $this->checkImageSelectionComplete();
            }

        } catch (\Exception $e) {
            Log::error('Image selection failed', [
                'section_index' => $sectionIndex,
                'image_index' => $imageIndex,
                'error' => $e->getMessage()
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'انتخاب تصویر شکست خورد: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Check if all image selections are complete
     */
    public function checkImageSelectionComplete()
    {
        $totalSections = count($this->headings);
        $selectedCount = count($this->selectedImages);
        
        if ($selectedCount >= $totalSections) {
            $this->processSelectedImages();
        }
    }

    /**
     * Process selected images (add titles and save)
     */
    public function processSelectedImages()
    {
        try {
            $imageService = app(\App\Services\ImageGenerationService::class);
            $aiContent = AiContent::find($this->aiContentId);
            
            if (!$aiContent) {
                throw new \Exception('AI Content not found');
            }

            $thumbnails = $aiContent->ai_thumbnails ?? [];
            $processedImages = [];

            foreach ($this->selectedImages as $sectionIndex => $imageIndex) {
                if (isset($thumbnails[$sectionIndex]) && isset($thumbnails[$sectionIndex]['images'][$imageIndex])) {
                    $selectedImage = $thumbnails[$sectionIndex]['images'][$imageIndex];
                    $title = $this->headings[$sectionIndex]['title'];
                    
                    // Add title to image (with settings)
                    $processedImage = $imageService->addTitleToImage(
                        $selectedImage['path'],
                        $title,
                        $this->imageGenerationSettings
                    );
                    
                    $thumbnails[$sectionIndex]['final_image'] = $processedImage;
                    $thumbnails[$sectionIndex]['status'] = 'processed';
                    $processedImages[$sectionIndex] = $processedImage;
                }
            }

            // Update AI content with processed images
            $aiContent->update(['ai_thumbnails' => $thumbnails]);
            
            // Clean up temporary images
            $this->cleanupTempImages($imageService);
            
            $this->imageProcessingComplete = true;
            $this->showImageSelection = false;
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'تصاویر با موفقیت پردازش شدند!'
            ]);

            // Proceed to summary
            $this->proceedToActualSummary();

        } catch (\Exception $e) {
            Log::error('Image processing failed', [
                'error' => $e->getMessage(),
                'ai_content_id' => $this->aiContentId
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'پردازش تصاویر شکست خورد: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Skip image selection and proceed to summary
     */
    public function skipImageSelection()
    {
        $this->showImageSelection = false;
        $this->proceedToActualSummary();
        
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => 'انتخاب تصاویر رد شد. ادامه به مرحله خلاصه...'
        ]);
    }

    /**
     * Proceed to actual summary generation (after images)
     */
    public function proceedToActualSummary()
    {
        $this->currentStep = 5; // Update to actual summary step
        $this->progress = 65;
        
        $this->dispatch('progress-updated', [
            'progress' => $this->progress,
            'step' => $this->currentStep
        ]);

        // Trigger summary generation
        event(new CustomUserEvent(
            $this->event_hash,
            'generateSummary',
            null,
            'function'
        ));
    }

    /**
     * Clean up temporary images
     */
    public function cleanupTempImages($imageService)
    {
        try {
            $aiContent = AiContent::find($this->aiContentId);
            if (!$aiContent || !$aiContent->ai_thumbnails) {
                return;
            }

            $tempPaths = [];
            foreach ($aiContent->ai_thumbnails as $sectionData) {
                if (isset($sectionData['images'])) {
                    foreach ($sectionData['images'] as $image) {
                        if (isset($image['path'])) {
                            $tempPaths[] = $image['path'];
                        }
                    }
                }
            }

            if (!empty($tempPaths)) {
                $imageService->cleanupTempImages($tempPaths);
            }

        } catch (\Exception $e) {
            Log::warning('Cleanup of temp images failed', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Toggle image generation setting
     */
    public function toggleImageGeneration()
    {
        $this->imageGenerationEnabled = !$this->imageGenerationEnabled;
        
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => $this->imageGenerationEnabled ? 
                'تولید تصاویر فعال شد' : 'تولید تصاویر غیرفعال شد'
        ]);
    }

    /**
     * Get image generation settings
     */
    public function getImageGenerationSettings()
    {
        try {
            $imageService = app(\App\Services\ImageGenerationService::class);
            return $imageService->getImageSettings();
        } catch (\Exception $e) {
            Log::error('Failed to get image settings', [
                'error' => $e->getMessage()
            ]);
            return [
                'image_quality' => 'standard',
                'image_count' => 4,
                'style_option' => 'vector',
                'custom_style_prompt' => '',
                'add_text_overlay' => true,
                'text_overlay_position' => 'bottom',
                'text_overlay_style' => 'dark'
            ];
        }
    }

    /**
     * Get style options for image generation
     */
    public function getStyleOptions()
    {
        try {
            $imageService = app(\App\Services\ImageGenerationService::class);
            return $imageService->getStyleOptions();
        } catch (\Exception $e) {
            Log::error('Failed to get style options', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Save image generation settings
     */
    public function saveImageSettings(array $settings)
    {
        try {
            $imageService = app(\App\Services\ImageGenerationService::class);
            $success = $imageService->saveImageSettings($settings);
            
            if ($success) {
                $this->imageGenerationSettings = $settings;
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'تنظیمات تولید تصویر ذخیره شد!'
                ]);
            } else {
                throw new \Exception('Failed to save settings');
            }
        } catch (\Exception $e) {
            Log::error('Failed to save image settings', [
                'settings' => $settings,
                'error' => $e->getMessage()
            ]);
            
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'خطا در ذخیره تنظیمات: ' . $e->getMessage()
            ]);
        }
    }
}
