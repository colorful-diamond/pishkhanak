<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected string $defaultModel;
    protected string $apiKey;
    protected array $proxyOptions;

    public function __construct()
    {
        $this->defaultModel = 'gpt-4o-mini';
        $this->apiKey = config('services.openai.api_key'); // Assuming API key is stored in config

        // Proxy configuration for SOCKS5
        $this->proxyOptions = [
            'proxy' => 'socks5://127.0.0.1:1080',
        ];
    }

    public function chatCompletion(array $messages, string $model = null, array $options = [])
    {
        // Custom HTTP request for Chat Completion with proxy
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->withOptions($this->proxyOptions) // Add proxy options
          ->post('https://api.openai.com/v1/chat/completions', [
            'model' => $model ?? $this->defaultModel,
            'messages' => $messages,
            ...$options
        ]);

        $result = $response->json();

        return $result['choices'][0]['message']['content'] ?? null;
    }

    public function streamChatCompletion(array $messages, string $model = null, array $options = [])
    {
        // Custom HTTP request for Streamed Chat Completion with proxy
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->withOptions($this->proxyOptions) // Add proxy options
          ->post('https://api.openai.com/v1/chat/completions', [
            'model' => $model ?? $this->defaultModel,
            'messages' => $messages,
            'stream' => true,
            ...$options
        ]);

        return $response->body(); // Handle streaming response as needed
    }

    public function chatCompletionWithJsonValidation(array $messages, string $model = null, array $options = [])
    {
        // Custom HTTP request with JSON validation and proxy
        $response = $this->chatCompletion($messages, $model, $options);
        
        $jsonResponse = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            preg_match('/\{.*\}/s', $response, $matches);
            if (!empty($matches)) {
                $jsonResponse = json_decode($matches[0], true);
            }
        }
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Failed to obtain valid JSON from the AI response");
        }
        
        return $jsonResponse;
    }

    public function generateImage(string $prompt, string $size = '1792x1024'): string
    {
        try {
            // Custom HTTP request for Image Generation with proxy
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->withOptions($this->proxyOptions) // Add proxy options
              ->post('https://api.openai.com/v1/images/generations', [
                'prompt' => $prompt,
                'model' => 'dall-e-3',
                'n' => 1,
                'size' => $size,
                'response_format' => 'url',
            ]);

            $result = $response->json();

            return $result['data'][0]['url'] ?? null;
        } catch (\Exception $e) {
            Log::error("OpenAI Image Generation Error: " . $e->getMessage());
            throw new \Exception("Failed to generate image via OpenAI.");
        }
    }
}