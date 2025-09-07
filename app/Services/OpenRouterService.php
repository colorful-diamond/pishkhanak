<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class OpenRouterService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $defaultModel;
    protected array $proxyOptions;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key');
        $this->baseUrl = 'https://openrouter.ai/api/v1';
        $this->defaultModel = 'openai/gpt-4o-mini'; // You can change this to your preferred default model

        // Proxy configuration for SOCKS5
        $this->proxyOptions = [
            'proxy' => 'socks5://127.0.0.1:1080',
        ];
    }
    

    public function chatCompletion(array $messages, string $model = null, array $options = [])
    {
        $url = "{$this->baseUrl}/chat/completions";
        $body = [
            'model' => $model ?? $this->defaultModel,
            'messages' => $messages,
            ...$options
        ];

        $response = Http::withHeaders($this->getHeaders())
            ->withOptions($this->proxyOptions)
            ->timeout(300)
            ->post($url, $body);

        if (!$response->successful()) {
            throw new \Exception("HTTP error! status: {$response->status()}");
        }

        return $response->json()['choices'][0]['message']['content'];
    }

    public function streamChatCompletion(array $messages, string $model = null, array $options = [])
    {
        $url = "{$this->baseUrl}/chat/completions";
        $body = [
            'model' => $model ?? $this->defaultModel,
            'messages' => $messages,
            'stream' => true,
            ...$options
        ];

        return Http::withHeaders($this->getHeaders())
            ->withOptions($this->proxyOptions)
            ->withOptions(['stream' => true])
            ->post($url, $body);
    }

    public function getGenerationStats(string $generationId)
    {
        $url = "{$this->baseUrl}/generation?id={$generationId}";

        $response = Http::withHeaders($this->getHeaders())
            ->withOptions($this->proxyOptions)
            ->get($url);

        if (!$response->successful()) {
            throw new \Exception("HTTP error! status: {$response->status()}");
        }

        return $response->json()['data'];
    }

    public function chatCompletionWithJsonValidation(array $messages, string $model = null, array $options = [])
    {
        $response = $this->chatCompletion($messages, $model, $options);
        
        // Attempt to parse the response as JSON
        $jsonResponse = json_decode($response, true);
        
        // If parsing fails, try to extract JSON from the response
        if (json_last_error() !== JSON_ERROR_NONE) {
            preg_match('/\{.*\}/s', $response, $matches);
            if (!empty($matches)) {
                $jsonResponse = json_decode($matches[0], true);
            }
        }
        
        // If we still don't have valid JSON, throw an exception
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Failed to obtain valid JSON from the AI response");
        }
        
        return $jsonResponse;
    }

    protected function getHeaders(): array
    {
        return [
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
            'HTTP-Referer' => Config::get('app.url'),
            'X-Title' => Config::get('app.name'),
        ];
    }
}