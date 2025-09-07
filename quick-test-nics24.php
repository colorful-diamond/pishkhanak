<?php
/**
 * Quick NICS24 Test Script
 * 
 * Simple script to quickly test NICS24 provider connectivity and basic functionality.
 * 
 * Usage: php quick-test-nics24.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

class QuickNICS24Test
{
    private string $localApiUrl = 'http://127.0.0.1:9999';
    
    public function runQuickTest(): void
    {
        echo "🔍 Quick NICS24 Provider Test\n";
        echo "============================\n\n";
        
        // Test 1: Server connectivity
        echo "1️⃣ Testing server connectivity...\n";
        if ($this->testServerHealth()) {
            echo "   ✅ Server is running\n\n";
        } else {
            echo "   ❌ Server is not accessible\n";
            echo "   💡 Make sure Node.js server is running: node localApiServer.js\n\n";
            return;
        }
        
        // Test 2: Provider availability
        echo "2️⃣ Testing NICS24 provider availability...\n";
        if ($this->testProviderAvailability()) {
            echo "   ✅ NICS24 provider is available\n\n";
        } else {
            echo "   ❌ NICS24 provider is not available\n";
            echo "   💡 Check if nics24 provider is properly configured\n\n";
            return;
        }
        
        // Test 3: Basic request structure
        echo "3️⃣ Testing basic request structure...\n";
        if ($this->testRequestStructure()) {
            echo "   ✅ Request structure is valid\n\n";
        } else {
            echo "   ❌ Request structure validation failed\n\n";
            return;
        }
        
        // Test 4: Configuration check
        echo "4️⃣ Checking NICS24 configuration...\n";
        $this->checkConfiguration();
        
        echo "✅ Quick test completed successfully!\n";
        echo "💡 To run full test with OTP flow: php test-nics24-provider.php\n";
    }
    
    private function testServerHealth(): bool
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->localApiUrl . '/health',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        return !$error && $httpCode === 200;
    }
    
    private function testProviderAvailability(): bool
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->localApiUrl . '/api/providers',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json'
            ]
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            return isset($data['providers']) && in_array('nics24', $data['providers']);
        }
        
        return false;
    }
    
    private function testRequestStructure(): bool
    {
        $testData = [
            'mobile' => '09123456789',
            'nationalCode' => '1234567890',
            'provider' => 'nics24'
        ];
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->localApiUrl . '/api/services/credit-score-rating',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($testData),
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ]
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "   📊 HTTP Status: {$httpCode}\n";
        
        if ($response) {
            $data = json_decode($response, true);
            if ($data) {
                echo "   📋 Response: " . ($data['message'] ?? 'Valid structure') . "\n";
                return isset($data['status']);
            }
        }
        
        return $httpCode === 200;
    }
    
    private function checkConfiguration(): void
    {
        $configFile = __DIR__ . '/bots/inquiry-provider/providers/nics24/config.js';
        
        if (file_exists($configFile)) {
            echo "   ✅ Config file exists\n";
            
            $content = file_get_contents($configFile);
            
            // Check for placeholder credentials
            if (strpos($content, 'your_nics24_username') !== false) {
                echo "   ⚠️  WARNING: Using placeholder credentials!\n";
                echo "   💡 Update config.js with real NICS24 credentials\n";
            } else {
                echo "   ✅ Credentials appear to be configured\n";
            }
            
            // Check for required fields
            $requiredFields = ['baseUrl', 'captchaApiUrl', 'users'];
            foreach ($requiredFields as $field) {
                if (strpos($content, $field) !== false) {
                    echo "   ✅ {$field} configured\n";
                } else {
                    echo "   ❌ {$field} missing\n";
                }
            }
        } else {
            echo "   ❌ Config file not found: {$configFile}\n";
        }
        
        // Check session directory
        $sessionDir = __DIR__ . '/bots/inquiry-provider/providers/nics24/sessions';
        if (is_dir($sessionDir)) {
            echo "   ✅ Sessions directory exists\n";
            if (is_writable($sessionDir)) {
                echo "   ✅ Sessions directory is writable\n";
            } else {
                echo "   ⚠️  Sessions directory is not writable\n";
            }
        } else {
            echo "   ⚠️  Sessions directory not found\n";
        }
        
        echo "\n";
    }
}

// Main execution
if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.\n");
}

try {
    $tester = new QuickNICS24Test();
    $tester->runQuickTest();
} catch (Exception $e) {
    echo "💥 Error: " . $e->getMessage() . "\n";
    exit(1);
}