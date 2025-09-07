<?php

namespace App\Services;

use App\Models\AutoResponseContext;
use App\Models\AutoResponse;
use App\Models\AutoResponseLog;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiAutoResponseService
{
    protected $minConfidence;
    protected $enabled;
    protected $cacheEnabled;
    protected $cacheDuration;
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
        $this->minConfidence = config('gemini.auto_response.min_confidence', 0.7);
        $this->enabled = config('gemini.auto_response.enabled', true);
        $this->cacheEnabled = config('gemini.auto_response.cache_enabled', true);
        $this->cacheDuration = config('gemini.auto_response.cache_duration', 60);
    }

    /**
     * Process a ticket for auto-response
     */
    public function processTicket(Ticket $ticket): ?array
    {
        if (!$this->enabled) {
            return null;
        }

        try {
            // Get all active contexts ordered by priority
            $contexts = AutoResponseContext::active()
                ->orderByPriority()
                ->get();

            if ($contexts->isEmpty()) {
                Log::info('No active auto-response contexts found');
                return null;
            }

            // Analyze the ticket with Gemini
            $analysis = $this->analyzeTicketWithGemini($ticket, $contexts);

            if (!$analysis || !isset($analysis['matched_context_id'])) {
                return null;
            }

            // Check if confidence meets threshold
            if ($analysis['confidence'] < $this->minConfidence) {
                $this->logAutoResponse($ticket, null, null, $analysis);
                return null;
            }

            // Get the matched context
            $context = $contexts->find($analysis['matched_context_id']);
            if (!$context) {
                return null;
            }

            // Check context-specific confidence threshold
            if ($analysis['confidence'] < $context->confidence_threshold) {
                $this->logAutoResponse($ticket, $context, null, $analysis);
                return null;
            }

            // Get the best response for the detected language
            $language = $analysis['language'] ?? config('gemini.auto_response.default_language');
            $response = $context->getBestResponseForLanguage($language);

            if (!$response || !$response->is_active) {
                // Try default language if no response in detected language
                if ($language !== 'fa') {
                    $response = $context->getBestResponseForLanguage('fa');
                }
                
                if (!$response || !$response->is_active) {
                    $this->logAutoResponse($ticket, $context, null, $analysis);
                    return null;
                }
            }

            // Log the auto-response
            $log = $this->logAutoResponse($ticket, $context, $response, $analysis);

            // Increment usage count
            $response->incrementUsage();

            return [
                'success' => true,
                'response' => $response,
                'context' => $context,
                'analysis' => $analysis,
                'log' => $log,
            ];

        } catch (Exception $e) {
            Log::error('Error processing ticket for auto-response', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Analyze ticket with Gemini AI
     */
    protected function analyzeTicketWithGemini(Ticket $ticket, $contexts): ?array
    {
        $cacheKey = 'gemini_analysis_' . md5($ticket->subject . $ticket->description);

        if ($this->cacheEnabled) {
            $cached = Cache::get($cacheKey);
            if ($cached) {
                return $cached;
            }
        }

        try {
            // Prepare context data for Gemini
            $contextData = $contexts->map(function ($context) {
                return [
                    'id' => $context->id,
                    'name' => $context->name,
                    'description' => $context->description,
                    'keywords' => $context->keywords,
                    'example_queries' => $context->example_queries,
                ];
            });

            // Create the prompt with system instruction
            $systemInstruction = config('gemini.prompts.context_matching', 'You are an expert customer support ticket analyzer. Analyze the following ticket and match it to the most appropriate context based on keywords, description, and example queries.');
            $prompt = $systemInstruction . "\n\n" . $this->buildAnalysisPrompt($ticket, $contextData);

            // Call Gemini API using GeminiService
            $result = $this->geminiService->generateContent($prompt, 'gemini-2.5-flash');

            $responseText = $result;

            // Extract JSON from response
            $jsonMatch = [];
            if (preg_match('/\{.*\}/s', $responseText, $jsonMatch)) {
                $analysis = json_decode($jsonMatch[0], true);
                
                if ($analysis && is_array($analysis)) {
                    // Add user query to analysis
                    $analysis['user_query'] = $ticket->subject . "\n" . $ticket->description;
                    
                    // Cache the result
                    if ($this->cacheEnabled) {
                        Cache::put($cacheKey, $analysis, now()->addMinutes($this->cacheDuration));
                    }
                    
                    return $analysis;
                }
            }

            Log::warning('Failed to parse Gemini response', [
                'response' => $responseText,
                'ticket_id' => $ticket->id,
            ]);

            return null;

        } catch (Exception $e) {
            Log::error('Gemini API error', [
                'error' => $e->getMessage(),
                'ticket_id' => $ticket->id,
            ]);
            return null;
        }
    }

    /**
     * Build the analysis prompt for Gemini
     */
    protected function buildAnalysisPrompt(Ticket $ticket, $contextData): string
    {
        $userQuery = "Subject: {$ticket->subject}\nDescription: {$ticket->description}";
        
        $contextsJson = json_encode($contextData->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return "User Query:\n{$userQuery}\n\nAvailable Contexts:\n{$contextsJson}\n\nAnalyze this support ticket and determine which context it best matches. 

Please respond with ONLY a JSON object in this exact format:
{
    \"matched_context_id\": <context_id_number>,
    \"confidence\": <confidence_score_between_0_and_1>,
    \"language\": \"<detected_language_code>\",
    \"reasoning\": \"<brief_explanation_of_match>\"
}

Do not include any other text in your response, only the JSON object.";
    }

    /**
     * Log the auto-response attempt
     */
    protected function logAutoResponse(
        Ticket $ticket,
        ?AutoResponseContext $context,
        ?AutoResponse $response,
        array $analysis
    ): AutoResponseLog {
        return AutoResponseLog::create([
            'ticket_id' => $ticket->id,
            'context_id' => $context?->id,
            'response_id' => $response?->id,
            'user_query' => $ticket->subject . "\n" . $ticket->description,
            'ai_analysis' => $analysis,
            'confidence_score' => $analysis['confidence'] ?? 0,
            'escalated_to_support' => !$response,
            'responded_at' => $response ? now() : null,
        ]);
    }

    /**
     * Apply auto-response to ticket
     */
    public function applyAutoResponse(Ticket $ticket, array $responseData): bool
    {
        try {
            $response = $responseData['response'];
            $analysis = $responseData['analysis'];

            // Update ticket with auto-response info
            $ticket->update([
                'is_auto_responded' => true,
                'auto_response_id' => $response->id,
                'auto_responded_at' => now(),
                'status' => $response->mark_as_resolved ? 'resolved' : 'waiting_for_user',
            ]);

            // Create auto-response message
            $variables = [
                'user_name' => $ticket->user->name,
                'ticket_number' => $ticket->ticket_number,
            ];

            $message = TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => null, // System message
                'message' => $response->getFormattedResponse($variables),
                'is_internal' => false,
                'is_auto_response' => true,
            ]);

            // Add links if any
            if ($response->hasLinks()) {
                $linksText = "1b444374";
                foreach ($response->links as $link) {
                    $linksText .= "- {$link['title']}: {$link['url']}\n";
                }
                $message->update(['message' => $message->message . $linksText]);
            }

            // Handle attachments if any
            if ($response->hasAttachments()) {
                foreach ($response->attachments as $attachment) {
                    // You'll need to implement attachment handling based on your system
                    // This is a placeholder for attachment logic
                }
            }

            // Update ticket timestamps
            if (!$ticket->first_response_at) {
                $ticket->update([
                    'first_response_at' => now(),
                    'response_time' => now()->diffInMinutes($ticket->created_at),
                ]);
            }

            return true;

        } catch (Exception $e) {
            Log::error('Error applying auto-response', [
                'ticket_id' => $ticket->id,
                'response_id' => $response->id ?? null,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Check if auto-response is enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Manually trigger context analysis for a ticket
     */
    public function reanalyzeTicket(Ticket $ticket): ?array
    {
        // Clear cache for this ticket
        $cacheKey = 'gemini_analysis_' . md5($ticket->subject . $ticket->description);
        Cache::forget($cacheKey);

        return $this->processTicket($ticket);
    }

    /**
     * Get context suggestions based on a query
     */
    public function getContextSuggestions(string $query, int $limit = 3): array
    {
        try {
            $contexts = AutoResponseContext::active()
                ->orderByPriority()
                ->limit($limit * 2) // Get more to filter later
                ->get();

            if ($contexts->isEmpty()) {
                return [];
            }

            // Simple keyword matching for suggestions
            $suggestions = [];
            foreach ($contexts as $context) {
                $score = 0;
                $keywords = $context->getKeywordsArrayAttribute();
                
                foreach ($keywords as $keyword) {
                    if (stripos($query, $keyword) !== false) {
                        $score += 1;
                    }
                }

                if ($score > 0) {
                    $suggestions[] = [
                        'context' => $context,
                        'score' => $score,
                    ];
                }
            }

            // Sort by score and return top suggestions
            usort($suggestions, fn($a, $b) => $b['score'] <=> $a['score']);
            
            return array_slice($suggestions, 0, $limit);

        } catch (Exception $e) {
            Log::error('Error getting context suggestions', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }
}
