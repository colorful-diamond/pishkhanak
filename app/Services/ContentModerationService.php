<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ContentModerationService
{
    protected ?GeminiService $gemini;

    public function __construct()
    {
        try {
            $this->gemini = app(GeminiService::class);
        } catch (\Throwable $e) {
            Log::warning('GeminiService unavailable for content moderation', [
                'error' => $e->getMessage(),
            ]);
            $this->gemini = null;
        }
    }

    /**
     * Check text for offensive or prohibited content using Gemini 2.5 Flash.
     *
     * Returns an array:
     * [
     *   'blocked' => bool,
     *   'reason' => string,
     *   'bad_words' => array<string>,
     * ]
     */
    public function checkTextForOffense(string $text): array
    {
        // If GeminiService is unavailable, fail-open
        if ($this->gemini === null) {
            Log::info('Content moderation skipped - GeminiService unavailable');
            return [
                'blocked' => false,
                'reason' => '',
                'bad_words' => [],
            ];
        }

        $instruction = 'You are a strict content moderation system for a Persian/English support platform. '
            . 'Analyze the provided text and decide if it contains insults, hate speech, profanity, harassment, threats, '
            . 'adult/sexual content, spam/scam, or other content that should be blocked under typical community guidelines. '
            . 'Return a JSON object with keys: blocked (boolean), reason (short Persian explanation), bad_words (array of flagged words/phrases). '
            . 'Be conservative and block if clearly offensive. If acceptable, set blocked to false.';

        $messages = [
            ['role' => 'user', 'content' => $text],
        ];

        try {
            $json = $this->gemini->chatCompletionWithJsonValidation(
                $messages,
                $instruction,
                'gemini-2.5-flash',
                ['temperature' => 0.1]
            );

            $blocked = (bool)($json['blocked'] ?? false);
            $reason = (string)($json['reason'] ?? '');
            $badWords = is_array($json['bad_words'] ?? null) ? $json['bad_words'] : [];

            return [
                'blocked' => $blocked,
                'reason' => $reason,
                'bad_words' => $badWords,
            ];
        } catch (\Throwable $e) {
            Log::error('Content moderation failed', [
                'error' => $e->getMessage(),
            ]);

            // Fail-open to avoid accidental false positives when AI is unavailable
            return [
                'blocked' => false,
                'reason' => '',
                'bad_words' => [],
            ];
        }
    }
}


