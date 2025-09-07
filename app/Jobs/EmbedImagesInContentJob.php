<?php

namespace App\Jobs;

use App\Models\AiContent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EmbedImagesInContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $aiContentId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $aiContentId)
    {
        $this->aiContentId = $aiContentId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $aiContent = AiContent::find($this->aiContentId);
            
            if (!$aiContent) {
                Log::error('AI Content not found for embedding images', ['id' => $this->aiContentId]);
                return;
            }

            $thumbnails = $aiContent->ai_thumbnails ?? [];
            $sections = $aiContent->ai_sections ?? [];

            if (empty($thumbnails) || empty($sections)) {
                Log::info('No thumbnails or sections to process', [
                    'id' => $this->aiContentId,
                    'thumbnails_count' => count($thumbnails),
                    'sections_count' => count($sections)
                ]);
                return;
            }

            $updatedSections = [];
            
            foreach ($sections as $index => $section) {
                // Get the section content
                $content = '';
                if (is_array($section) && isset($section['content'])) {
                    $content = $section['content'];
                    $heading = $section['heading'] ?? null;
                } elseif (is_string($section)) {
                    $content = $section;
                    $heading = null;
                }

                // Clean any existing placeholder images first
                $content = preg_replace('/<figure class="ai-content-image[^>]*>.*?<\/figure>/s', '', $content);
                $content = preg_replace('/<img[^>]+src="\/images\/section-\d+-hero\.jpg"9264394c"ai-content-image mb-6">
                                <img src="%s" alt="%s" class="w-full rounded-lg shadow-md" loading="lazy">
                                <figcaption class="text-sm text-gray-600 mt-2 text-center">%s</figcaption>
                            </figure>',
                            $imageUrl,
                            htmlspecialchars($imageTitle, ENT_QUOTES, 'UTF-8'),
                            htmlspecialchars($imageTitle, ENT_QUOTES, 'UTF-8')
                        );

                        // Add image at the beginning of the section content
                        $content = $imageHtml . "\n" . $content;

                        Log::info('Embedded image in section', [
                            'ai_content_id' => $this->aiContentId,
                            'section_index' => $index,
                            'image_url' => $imageUrl
                        ]);
                    }
                }

                // Store the updated section
                if (is_array($section) && isset($section['content'])) {
                    $updatedSections[] = [
                        'content' => $content,
                        'heading' => $heading
                    ];
                } else {
                    $updatedSections[] = $content;
                }
            }

            // Update the AI content with embedded images
            $aiContent->ai_sections = $updatedSections;
            $aiContent->save();

            Log::info('Successfully embedded images in content', [
                'ai_content_id' => $this->aiContentId,
                'sections_processed' => count($updatedSections)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to embed images in content', [
                'ai_content_id' => $this->aiContentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }
}