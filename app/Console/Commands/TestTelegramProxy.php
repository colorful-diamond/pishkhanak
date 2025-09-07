<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramProxyService;
use Illuminate\Support\Facades\Http;

class TestTelegramProxy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:test-proxy {--setup : Setup and test the proxy connection}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Telegram proxy server connection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Testing Telegram Proxy Connection...');
        $this->newLine();

        // Check configuration
        $proxyUrl = config('telegram-proxy.server_url', 'http://46.62.173.170/telegram-proxy');
        $apiKey = config('telegram-proxy.api_key', '');

        if ($this->option('setup')) {
            return $this->setupProxy();
        }

        if (empty($apiKey) || $apiKey === 'your-api-key-here') {
            $this->error('❌ API key not configured!');
            $this->info('Please update your .env file with:');
            $this->line('TELEGRAM_PROXY_API_KEY=your-actual-api-key');
            return 1;
        }

        $this->info("Proxy URL: $proxyUrl");
        $this->info("API Key: " . substr($apiKey, 0, 10) . '...');
        $this->newLine();

        // Test 1: Basic connectivity
        $this->info('Test 1: Basic Connectivity');
        try {
            $response = Http::timeout(10)
                ->withHeaders(['X-API-Key' => $apiKey])
                ->post($proxyUrl, [
                    'action' => 'test',
                    'api_key' => $apiKey
                ]);

            if ($response->successful()) {
                $this->info('✅ Proxy server is reachable');
                $data = $response->json();
                if (isset($data['message'])) {
                    $this->info('Response: ' . $data['message']);
                }
            } else {
                $this->error('❌ Proxy server returned error: ' . $response->status());
                $this->error('Response: ' . $response->body());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Failed to connect to proxy server');
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();

        // Test 2: Send test message
        $this->info('Test 2: Sending Test Message');
        
        if ($this->confirm('Do you want to send a test message to Telegram?')) {
            try {
                $testMessage = "🧪 <b>Test Message from Pishkhanak</b>\n\n";
                $testMessage .= "✅ Proxy connection successful\n";
                $testMessage .= "📅 Time: " . now()->format('Y-m-d H:i:s') . "\n";
                $testMessage .= "🖥 Server: " . gethostname();

                $response = Http::timeout(30)
                    ->withHeaders(['X-API-Key' => $apiKey])
                    ->post($proxyUrl, [
                        'action' => 'sendMessage',
                        'api_key' => $apiKey,
                        'chat_id' => config('telegram-proxy.channel_id'),
                        'text' => $testMessage,
                        'parse_mode' => 'HTML'
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['ok']) && $data['ok']) {
                        $this->info('✅ Test message sent successfully!');
                        if (isset($data['result']['message_id'])) {
                            $this->info('Message ID: ' . $data['result']['message_id']);
                        }
                    } else {
                        $this->error('❌ Telegram API error: ' . json_encode($data));
                    }
                } else {
                    $this->error('❌ Failed to send message');
                    $this->error('Response: ' . $response->body());
                }
            } catch (\Exception $e) {
                $this->error('❌ Error sending test message');
                $this->error('Error: ' . $e->getMessage());
            }
        }

        $this->newLine();

        // Test 3: Using TelegramProxyService
        $this->info('Test 3: Testing TelegramProxyService');
        
        try {
            $service = new TelegramProxyService();
            $result = $service->sendNotification("🔔 Service test: " . now());
            
            if ($result && isset($result['ok']) && $result['ok']) {
                $this->info('✅ TelegramProxyService working correctly');
            } else {
                $this->error('❌ TelegramProxyService failed');
                if ($result) {
                    $this->error('Response: ' . json_encode($result));
                }
            }
        } catch (\Exception $e) {
            $this->error('❌ Service error: ' . $e->getMessage());
        }

        $this->newLine();
        $this->info('🏁 Testing complete!');

        return 0;
    }

    /**
     * Setup proxy configuration
     */
    protected function setupProxy()
    {
        $this->info('🚀 Telegram Proxy Setup');
        $this->newLine();

        // Get proxy server details
        $proxyUrl = $this->ask('Enter your proxy server URL', 'http://46.62.173.170/telegram-proxy');
        $apiKey = $this->ask('Enter the API key from your proxy server');

        // Update .env file
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        // Add or update proxy settings
        $settings = [
            'TELEGRAM_PROXY_ENABLED' => 'true',
            'TELEGRAM_PROXY_URL' => $proxyUrl,
            'TELEGRAM_PROXY_API_KEY' => $apiKey,
        ];

        foreach ($settings as $key => $value) {
            if (strpos($envContent, $key) !== false) {
                $envContent = preg_replace("/{$key}=.*/", "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envFile, $envContent);

        $this->info('✅ Configuration saved to .env');
        $this->newLine();

        // Clear config cache
        $this->call('config:clear');

        // Test connection
        $this->info('Testing connection...');
        $this->call('telegram:test-proxy');

        return 0;
    }
}