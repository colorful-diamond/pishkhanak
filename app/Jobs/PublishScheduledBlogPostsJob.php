<?php

namespace App\Jobs;

use App\Models\BlogContentPipeline;
use App\Models\BlogPublicationQueue;
use App\Models\BlogProcessingLog;
use App\Models\BlogPipelineSetting;
use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PublishScheduledBlogPostsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $publishDate;
    protected $batchSize;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300; // 5 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(?Carbon $publishDate = null, ?int $batchSize = null)
    {
        $this->publishDate = $publishDate ?? now();
        $schedule = BlogPipelineSetting::getPublishingSchedule();
        $this->batchSize = $batchSize ?? $schedule['posts_per_batch'] ?? 25;
        $this->onQueue('blog-publishing');
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Check if publishing is enabled
        if (!BlogPipelineSetting::isPublishingEnabled()) {
            Log::info('Blog publishing is disabled');
            return;
        }

        Log::info('Starting scheduled blog post publication', [
            'date' => $this->publishDate->toDateString(),
            'batch_size' => $this->batchSize,
        ]);

        // Get posts scheduled for publication
        $scheduledPosts = BlogPublicationQueue::readyToPublish()
            ->with('pipeline')
            ->limit($this->batchSize)
            ->get();

        if ($scheduledPosts->isEmpty()) {
            Log::info('No posts scheduled for publication');
            $this->checkAndQueueMorePosts();
            return;
        }

        $published = 0;
        $failed = 0;

        foreach ($scheduledPosts as $queueItem) {
            try {
                $this->publishPost($queueItem);
                $published++;
            } catch (\Exception $e) {
                $this->handlePublicationFailure($queueItem, $e);
                $failed++;
            }
        }

        Log::info('Blog publication batch completed', [
            'published' => $published,
            'failed' => $failed,
        ]);

        // Check if we need to queue more posts for processing
        $this->checkAndQueueMorePosts();
    }

    /**
     * Publish a single blog post
     */
    protected function publishPost(BlogPublicationQueue $queueItem)
    {
        $pipeline = $queueItem->pipeline;

        if (!$pipeline) {
            throw new \Exception('Pipeline record not found');
        }

        // Validate content before publishing
        $this->validateContent($pipeline);

        DB::beginTransaction();

        try {
            // Prepare post data
            $postData = $pipeline->prepareForPublication();
            
            // Create the blog post
            $post = Post::create($postData);

            // Handle media/images if available
            if (!empty($pipeline->ai_images)) {
                $this->attachImages($post, $pipeline->ai_images);
            }

            // Handle tags
            if (!empty($pipeline->meta_keywords)) {
                $tags = array_map('trim', explode(',', $pipeline->meta_keywords));
                $post->attachTags($tags);
            }

            // Mark as published
            $pipeline->markAsPublished($post->id);
            $queueItem->markAsPublished();

            // Log successful publication
            BlogProcessingLog::logPublish(
                $pipeline->id,
                BlogProcessingLog::STATUS_COMPLETED,
                [
                    'post_id' => $post->id,
                    'published_at' => now()->toDateTimeString(),
                ]
            );

            DB::commit();

            Log::info('Blog post published successfully', [
                'pipeline_id' => $pipeline->id,
                'post_id' => $post->id,
                'title' => $post->title,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Validate content before publishing
     */
    protected function validateContent(BlogContentPipeline $pipeline)
    {
        $thresholds = BlogPipelineSetting::getQualityThresholds();

        // Check quality score
        if ($pipeline->quality_score < $thresholds['min_score_for_auto_publish']) {
            throw new \Exception("Quality score too low: {$pipeline->quality_score}");
        }

        // Check if review is required
        if ($pipeline->requires_review) {
            throw new \Exception('Content requires manual review before publication');
        }

        // Check content length
        $content = $pipeline->ai_content ?? $pipeline->original_content;
        $contentLength = strlen($content);

        if ($contentLength < $thresholds['min_content_length']) {
            throw new \Exception("Content too short: {$contentLength} characters");
        }

        if ($contentLength > $thresholds['max_content_length']) {
            throw new \Exception("Content too long: {$contentLength} characters");
        }

        // Check for required fields
        if (empty($pipeline->ai_title) && empty($pipeline->title)) {
            throw new \Exception('No title available');
        }

        if (empty($content)) {
            throw new \Exception('No content available');
        }
    }

    /**
     * Attach images to the post
     */
    protected function attachImages(Post $post, array $images)
    {
        foreach ($images as $index => $imageUrl) {
            try {
                if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                    $post->addMediaFromUrl($imageUrl)
                        ->toMediaCollection($index === 0 ? 'thumbnail' : 'images');
                }
            } catch (\Exception $e) {
                Log::warning('Failed to attach image to post', [
                    'post_id' => $post->id,
                    'image_url' => $imageUrl,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Handle publication failure
     */
    protected function handlePublicationFailure(BlogPublicationQueue $queueItem, \Exception $e)
    {
        Log::error('Failed to publish blog post', [
            'queue_id' => $queueItem->id,
            'pipeline_id' => $queueItem->pipeline_id,
            'error' => $e->getMessage(),
        ]);

        $queueItem->markAsFailed($e->getMessage());

        BlogProcessingLog::logPublish(
            $queueItem->pipeline_id,
            BlogProcessingLog::STATUS_FAILED,
            ['error' => $e->getMessage()]
        );

        // If this is a temporary error, reschedule for tomorrow
        if ($this->isTemporaryError($e)) {
            $queueItem->reschedule(now()->addDay());
        }
    }

    /**
     * Check if the error is temporary and worth retrying
     */
    protected function isTemporaryError(\Exception $e): bool
    {
        $temporaryErrors = [
            'Connection timed out',
            'Too many connections',
            'Lock wait timeout',
        ];

        foreach ($temporaryErrors as $error) {
            if (str_contains($e->getMessage(), $error)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check and queue more posts for AI processing if needed
     */
    protected function checkAndQueueMorePosts()
    {
        // Get the number of posts in the publication queue for the next 7 days
        $upcomingCount = BlogPublicationQueue::upcoming()
            ->where('publish_date', '<=', now()->addDays(7))
            ->count();

        $dailyLimit = BlogPipelineSetting::getDailyPublishLimit();
        $targetBuffer = $dailyLimit * 7; // Keep 7 days worth of content ready

        if ($upcomingCount < $targetBuffer) {
            $needed = $targetBuffer - $upcomingCount;
            
            Log::info('Queueing more posts for processing', [
                'current_buffer' => $upcomingCount,
                'target_buffer' => $targetBuffer,
                'needed' => $needed,
            ]);

            $this->queuePostsForProcessing($needed);
        }
    }

    /**
     * Queue posts for AI processing
     */
    protected function queuePostsForProcessing(int $count)
    {
        $posts = BlogContentPipeline::imported()
            ->orderBy('created_at')
            ->limit($count)
            ->get();

        foreach ($posts as $pipeline) {
            // Create processing queue entry
            $queue = \App\Models\BlogProcessingQueue::create([
                'pipeline_id' => $pipeline->id,
                'priority' => \App\Models\BlogProcessingQueue::PRIORITY_NORMAL,
                'status' => \App\Models\BlogProcessingQueue::STATUS_PENDING,
                'queued_at' => now(),
                'processing_config' => BlogPipelineSetting::getAiProcessingConfig(),
            ]);

            // Update pipeline status
            $pipeline->update(['status' => BlogContentPipeline::STATUS_QUEUED]);

            // Dispatch the processing job
            ProcessBlogContentJob::dispatch($pipeline, $queue);
        }

        Log::info("Queued {$posts->count()} posts for AI processing");
    }
}