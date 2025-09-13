<?php

namespace App\Jobs;

use App\Models\AutoResponseContext;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\AutoResponseLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSimpleAutoResponseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Ticket $ticket;
    public int $timeout = 120;
    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Skip if ticket already has auto-response
            if ($this->ticket->is_auto_responded) {
                return;
            }

            // Skip if auto-response is disabled
            if (!config('gemini.auto_response.enabled', true)) {
                return;
            }

            Log::info('Processing simple auto-response', [
                'ticket_id' => $this->ticket->id,
                'ticket_number' => $this->ticket->ticket_number
            ]);

            $result = $this->matchAndRespond();

            if ($result) {
                Log::info('Auto-response generated successfully', [
                    'ticket_id' => $this->ticket->id,
                    'context_id' => $result['context_id'],
                    'confidence' => $result['confidence']
                ]);
            } else {
                Log::info('No suitable auto-response found', [
                    'ticket_id' => $this->ticket->id
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Auto-response job failed', [
                'ticket_id' => $this->ticket->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Match ticket content to context and create response
     */
    private function matchAndRespond(): ?array
    {
        $text = $this->ticket->subject . ' ' . $this->ticket->description;
        $contexts = AutoResponseContext::active()
            ->with('activeResponses')
            ->orderBy('priority', 'desc')
            ->get();

        $bestMatch = null;
        $bestScore = 0;
        $minScore = 0.3; // 30% minimum keyword match

        foreach ($contexts as $context) {
            $score = $this->calculateContextScore($text, $context);
            
            if ($score > $bestScore && $score >= $minScore && $context->activeResponses()->exists()) {
                $bestScore = $score;
                $bestMatch = $context;
            }
        }

        if (!$bestMatch) {
            return null;
        }

        // Check if score meets context-specific threshold
        if ($bestScore < $bestMatch->confidence_threshold) {
            $this->logAutoResponse($bestMatch, null, $bestScore, 'Confidence below threshold');
            return null;
        }

        $response = $bestMatch->getBestResponseForLanguage('fa');
        if (!$response) {
            $this->logAutoResponse($bestMatch, null, $bestScore, 'No response available');
            return null;
        }

        // Create auto-response
        $this->createAutoResponse($bestMatch, $response, $bestScore);

        return [
            'context_id' => $bestMatch->id,
            'response_id' => $response->id,
            'confidence' => $bestScore
        ];
    }

    /**
     * Calculate context matching score based on keywords
     */
    private function calculateContextScore(string $text, AutoResponseContext $context): float
    {
        $keywords = array_map('trim', explode(',', $context->keywords));
        $matchCount = 0;
        $totalKeywords = count($keywords);

        if ($totalKeywords === 0) {
            return 0;
        }

        foreach ($keywords as $keyword) {
            if (empty($keyword)) {
                continue;
            }

            // Use case-insensitive search for better matching
            if (mb_stripos($text, $keyword) !== false) {
                $matchCount++;
            }
        }

        return $matchCount / $totalKeywords;
    }

    /**
     * Create auto-response message
     */
    private function createAutoResponse(AutoResponseContext $context, $response, float $confidence): void
    {
        // Format response with variables
        $responseText = $this->formatResponse($response->response_text);

        // Create ticket message (auto-response)
        $message = TicketMessage::create([
            'ticket_id' => $this->ticket->id,
            'user_id' => 1, // System/Admin user - adjust as needed
            'message' => $responseText,
            'is_internal' => false
        ]);

        // Update ticket
        $this->ticket->update([
            'is_auto_responded' => true,
            'auto_response_id' => $response->id,
            'auto_responded_at' => now(),
            'first_response_at' => now()
        ]);

        // Increment response usage
        $response->incrementUsage();

        // Log successful auto-response
        $this->logAutoResponse($context, $response, $confidence, 'Success', $message->id);
    }

    /**
     * Format response text with variable substitution
     */
    private function formatResponse(string $responseText): string
    {
        $variables = [
            'ticket_number' => $this->ticket->ticket_number,
            'user_name' => $this->ticket->user->name ?? 'کاربر مهمان',
            'user_mobile' => $this->ticket->user->mobile ?? '',
            'created_date' => $this->ticket->created_at->format('Y/m/d'),
            'created_time' => $this->ticket->created_at->format('H:i'),
        ];

        foreach ($variables as $key => $value) {
            $responseText = str_replace('{{' . $key . '}}', $value, $responseText);
        }

        return $responseText;
    }

    /**
     * Log auto-response attempt
     */
    private function logAutoResponse(
        AutoResponseContext $context,
        $response = null,
        float $confidence = 0,
        string $status = 'processed',
        ?int $messageId = null
    ): void {
        try {
            AutoResponseLog::create([
                'ticket_id' => $this->ticket->id,
                'context_id' => $context->id,
                'response_id' => $response ? $response->id : null,
                'confidence_score' => $confidence,
                'was_sent' => $response !== null,
                'processing_time' => 0, // Could track actual time if needed
                'matched_keywords' => '', // Could store matched keywords if needed
                'status' => $status,
                'message_id' => $messageId,
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to log auto-response', [
                'ticket_id' => $this->ticket->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}