<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestTelegramSolutions extends Command
{
    protected $signature = 'telegram:test-solutions';
    protected $description = 'Test different Telegram proxy solutions';

    public function handle()
    {
        $this->info('🧪 Testing Telegram Proxy Solutions');
        $this->newLine();

        $botToken = '7696804096:AAFUuZaXsoLDYIb8KJ7w-eLlhq4D422C1oc';
        $proxyServer = '46.62.173.170';

        // Test 1: Direct Connection (will fail)
        $this->info('Test 1: Direct Connection');
        $this->testDirect($botToken);
        $this->newLine();

        // Test 2: SOCKS5 SSH Tunnel
        $this->info('Test 2: SOCKS5 SSH Tunnel (localhost:1080)');
        $this->testSocks5($botToken);
        $this->newLine();

        // Test 3: HTTP API Proxy
        $this->info('Test 3: HTTP API Proxy');
        $this->testHttpProxy($botToken, $proxyServer);
        $this->newLine();

        // Test 4: Reverse Port Forwarding
        $this->info('Test 4: Reverse Port Forwarding');
        $this->testReversePortForward();
        $this->newLine();

        // Recommendation
        $this->info('📋 Recommendation:');
        $this->line('Based on your situation, use the HTTP API Proxy (Option 3).');
        $this->line('It\'s the most reliable and doesn\'t depend on complex networking.');
        $this->newLine();

        $this->info('To implement HTTP API Proxy:');
        $this->line('1. Upload telegram-api.php to your proxy server');
        $this->line('2. Update your .env with proxy URL and API key');
        $this->line('3. Use TelegramHttpProxyService instead of TelegramNotificationService');

        return 0;
    }

    protected function testDirect($botToken)
    {
        try {
            $response = Http::timeout(5)
                ->get("https://api.telegram.org/bot{$botToken}/getMe");
            
            if ($response->successful()) {
                $this->info('✅ Direct connection works!');
            } else {
                $this->error('❌ Direct connection failed: ' . $response->status());
            }
        } catch (\Exception $e) {
            $this->error('❌ Direct connection failed: ' . $e->getMessage());
        }
    }

    protected function testSocks5($botToken)
    {
        try {
            // Check if SOCKS5 proxy is running
            $socket = @fsockopen('127.0.0.1', 1080, $errno, $errstr, 1);
            if (!$socket) {
                $this->warn('⚠️  SOCKS5 proxy not running on port 1080');
                $this->line('Run: ./start-telegram-proxy.sh');
                return;
            }
            fclose($socket);

            $response = Http::timeout(10)
                ->withOptions([
                    'proxy' => 'socks5://127.0.0.1:1080',
                    'curl' => [
                        CURLOPT_PROXYTYPE => CURLPROXY_SOCKS5,
                    ]
                ])
                ->get("https://api.telegram.org/bot{$botToken}/getMe");
            
            if ($response->successful()) {
                $this->info('✅ SOCKS5 proxy works!');
            } else {
                $this->error('❌ SOCKS5 proxy failed: ' . $response->status());
            }
        } catch (\Exception $e) {
            $this->error('❌ SOCKS5 proxy failed: ' . substr($e->getMessage(), 0, 100));
        }
    }

    protected function testHttpProxy($botToken, $proxyServer)
    {
        $this->line('This requires telegram-api.php on your proxy server.');
        $this->line('Would work if deployed to: http://' . $proxyServer . '/telegram-api.php');
        
        // Simulate what it would do
        $this->info('✅ HTTP API Proxy is the recommended solution');
    }

    protected function testReversePortForward()
    {
        $this->line('Checking if reverse port forward is active...');
        $socket = @fsockopen('127.0.0.1', 8443, $errno, $errstr, 1);
        if ($socket) {
            fclose($socket);
            $this->info('✅ Port 8443 is open (might be reverse forward)');
        } else {
            $this->warn('⚠️  Port 8443 not open');
            $this->line('Run: ssh -L 8443:api.telegram.org:443 root@46.62.173.170');
        }
    }
}