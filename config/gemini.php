<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Gemini API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for Google Gemini AI API
    | used for auto-response context matching in the ticketing system.
    |
    */

    'api_key' => env('GEMINI_API_KEY'),

    'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),

    'temperature' => env('GEMINI_TEMPERATURE', 0.3),

    'max_tokens' => env('GEMINI_MAX_TOKENS', 1000),

    /*
    |--------------------------------------------------------------------------
    | Auto-Response Configuration
    |--------------------------------------------------------------------------
    */

    'auto_response' => [
        // Minimum confidence score to auto-respond (0-1)
        'min_confidence' => env('AUTO_RESPONSE_MIN_CONFIDENCE', 0.7),

        // Enable/disable auto-response system
        'enabled' => env('AUTO_RESPONSE_ENABLED', true),

        // Languages supported for auto-response
        'languages' => ['fa', 'en'],

        // Default language
        'default_language' => 'fa',

        // Maximum context checks per request
        'max_context_checks' => 10,

        // Cache duration for AI responses (in minutes)
        'cache_duration' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | System Prompts
    |--------------------------------------------------------------------------
    */

    'prompts' => [
        'context_matching' => 'You are an AI assistant that analyzes support tickets and matches them to predefined contexts.
Given a user query and a list of contexts with their descriptions, keywords, and example queries, determine which context best matches the user query.

Return a JSON response with the following structure:
{
    "matched_context_id": <context_id or null>,
    "confidence": <0-1>,
    "reasoning": "<brief explanation>",
    "matched_keywords": ["keyword1", "keyword2"],
    "intent": "<detected intent>",
    "sentiment": "<positive/negative/neutral>",
    "language": "<detected language: fa/en>"
}

If no context matches with sufficient confidence, return matched_context_id as null.
Be strict in matching - only match if the query clearly relates to the context.',

        'language_detection' => 'Detect the language of the following text and return either "fa" for Persian/Farsi or "en" for English. Return only the language code.',
    ],

];
