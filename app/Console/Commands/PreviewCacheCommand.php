<?php

namespace App\Console\Commands;

use App\Services\PreviewCacheService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PreviewCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'preview-cache:manage {action} {--card=} {--iban=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage preview cache system (stats|clear|test)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        
        switch ($action) {
            case 'stats':
                $this->showStats();
                break;
                
            case 'clear':
                $this->clearCache();
                break;
                
            case 'test':
                $this->testCache();
                break;
                
            default:
                $this->error('Invalid action. Use: stats, clear, or test');
                return 1;
        }
        
        return 0;
    }
    
    /**
     * Show cache statistics
     */
    private function showStats()
    {
        $this->info('📊 Preview Cache Statistics');
        $this->line('─────────────────────────────');
        
        $stats = PreviewCacheService::getCacheStats();
        
        if (empty($stats)) {
            $this->warn('Unable to retrieve cache statistics.');
            return;
        }
        
        $this->line("Card Inquiries: {$stats['card_inquiries']}");
        $this->line("IBAN Inquiries: {$stats['iban_inquiries']}");
        $this->line("Account Inquiries: {$stats['account_inquiries']}");
        $this->line("Total Cached Items: {$stats['total_cached_items']}");
        $this->line("Redis Memory Usage: {$stats['memory_usage']}");
        
        $this->newLine();
        $this->info('✅ Cache statistics retrieved successfully');
    }
    
    /**
     * Clear cache entries
     */
    private function clearCache()
    {
        $cardNumber = $this->option('card');
        $iban = $this->option('iban');
        
        if ($cardNumber) {
            $success = PreviewCacheService::clearCardCache($cardNumber);
            if ($success) {
                $this->info("✅ Card cache cleared for: " . substr($cardNumber, 0, 6) . '******' . substr($cardNumber, -4));
            } else {
                $this->error("❌ Failed to clear card cache");
            }
        } elseif ($iban) {
            $success = PreviewCacheService::clearIbanCache($iban);
            if ($success) {
                $this->info("✅ IBAN cache cleared for: " . substr($iban, 0, 8) . '****' . substr($iban, -4));
            } else {
                $this->error("❌ Failed to clear IBAN cache");
            }
        } else {
            $this->warn('Please specify --card or --iban option to clear specific cache entries.');
            $this->line('Example: php artisan preview-cache:manage clear --card=1234567890123456');
            $this->line('Example: php artisan preview-cache:manage clear --iban=IR123456789012345678901234');
        }
    }
    
    /**
     * Test cache functionality
     */
    private function testCache()
    {
        $this->info('🧪 Testing Preview Cache System');
        $this->line('─────────────────────────────');
        
        // Test card caching
        $this->testCardCaching();
        
        $this->newLine();
        
        // Test IBAN caching
        $this->testIbanCaching();
        
        $this->newLine();
        $this->info('✅ Cache testing completed');
    }
    
    /**
     * Test card caching functionality
     */
    private function testCardCaching()
    {
        $testCard = '5892101447086871'; // Test card number
        
        $this->line('🔧 Testing Card Caching...');
        
        // Clear any existing cache
        PreviewCacheService::clearCardCache($testCard);
        
        // Test cache miss
        $cached = PreviewCacheService::getCardInquiry($testCard);
        if ($cached === null) {
            $this->line('✅ Cache MISS working correctly');
        } else {
            $this->error('❌ Expected cache MISS but got data');
            return;
        }
        
        // Test cache set
        $testData = [
            'owner_name' => 'احمد محمدی',
            'bank_name' => 'بانک سپه',
            'bank_logo' => 'https://example.com/sepah.svg',
            'engagement_message' => 'تست کارت'
        ];
        
        $setResult = PreviewCacheService::setCardInquiry($testCard, $testData);
        if ($setResult) {
            $this->line('✅ Cache SET working correctly');
        } else {
            $this->error('❌ Failed to set cache data');
            return;
        }
        
        // Test cache hit
        $cached = PreviewCacheService::getCardInquiry($testCard);
        if ($cached && isset($cached['owner_name']) && $cached['owner_name'] === 'احمد محمدی') {
            $this->line('✅ Cache HIT working correctly');
            
            if ($this->getOutput()->isVerbose()) {
                $this->line('📄 Cached data: ' . json_encode($cached, JSON_UNESCAPED_UNICODE));
            }
        } else {
            $this->error('❌ Cache HIT failed or data corrupted');
        }
        
        // Clean up
        PreviewCacheService::clearCardCache($testCard);
        $this->line('🧹 Test data cleaned up');
    }
    
    /**
     * Test IBAN caching functionality
     */
    private function testIbanCaching()
    {
        $testIban = 'IR123456789012345678901234'; // Test IBAN
        
        $this->line('🔧 Testing IBAN Caching...');
        
        // Clear any existing cache
        PreviewCacheService::clearIbanCache($testIban);
        
        // Test cache miss
        $cached = PreviewCacheService::getIbanInquiry($testIban);
        if ($cached === null) {
            $this->line('✅ Cache MISS working correctly');
        } else {
            $this->error('❌ Expected cache MISS but got data');
            return;
        }
        
        // Test cache set
        $testData = [
            'owner_name' => 'محمد احمدی',
            'bank_name' => 'بانک ملی ایران',
            'bank_logo' => 'https://example.com/melli.svg',
            'account_number' => '1234567890',
            'iban' => $testIban,
            'engagement_message' => 'تست شبا'
        ];
        
        $setResult = PreviewCacheService::setIbanInquiry($testIban, $testData);
        if ($setResult) {
            $this->line('✅ Cache SET working correctly');
        } else {
            $this->error('❌ Failed to set cache data');
            return;
        }
        
        // Test cache hit
        $cached = PreviewCacheService::getIbanInquiry($testIban);
        if ($cached && isset($cached['owner_name']) && $cached['owner_name'] === 'محمد احمدی') {
            $this->line('✅ Cache HIT working correctly');
            
            if ($this->getOutput()->isVerbose()) {
                $this->line('📄 Cached data: ' . json_encode($cached, JSON_UNESCAPED_UNICODE));
            }
        } else {
            $this->error('❌ Cache HIT failed or data corrupted');
        }
        
        // Clean up
        PreviewCacheService::clearIbanCache($testIban);
        $this->line('🧹 Test data cleaned up');
    }
} 