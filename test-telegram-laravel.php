<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Http;

echo "=== Testing Telegram with Laravel HTTP Client ===\n\n";

$botToken = '7696804096:AAFUuZaXsoLDYIb8KJ7w-eLlhq4D422C1oc';
$testUrl = "https://api.telegram.org/bot{$botToken}/getMe";

// Test different proxy configurations
$tests = [
    [
        'name' => 'SOCKS5 proxy on port 1080 (like Gemini)',
        'options' => ['proxy' => 'socks5://127.0.0.1:1080']
    ],
    [
        'name' => 'SOCKS5 proxy on port 1090',
        'options' => ['proxy' => 'socks5://127.0.0.1:1090']
    ],
    [
        'name' => 'SOCKS5 with explicit cURL options (port 1080)',
        'options' => [
            'curl' => [
                CURLOPT_PROXY => '127.0.0.1:1080',
                CURLOPT_PROXYTYPE => CURLPROXY_SOCKS5,
            ]
        ]
    ],
    [
        'name' => 'SOCKS5H proxy variant (port 1080)',
        'options' => ['proxy' => 'socks5h://127.0.0.1:1080']
    ],
    [
        'name' => 'HTTP proxy format (port 1080)',
        'options' => ['proxy' => 'http://127.0.0.1:1080']
    ],
];

foreach ($tests as $test) {
    echo "Test: {$test['name']}\n";
    
    try {
        $response = Http::timeout(10)
            ->withoutVerifying()
            ->withOptions($test['options'])
            ->get($testUrl);
        
        if ($response->successful()) {
            echo "✅ Success! Status: {$response->status()}\n";
            $data = $response->json();
            if (isset($data['result']['username'])) {
                echo "Bot username: @{$data['result']['username']}\n";
            }
        } else {
            echo "❌ HTTP Error: {$response->status()}\n";
            echo "Body: {$response->body()}\n";
        }
    } catch (\Exception $e) {
        echo "❌ Exception: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}
