<?php
/**
 * NICS24 Provider Test Script
 * 
 * This script tests the NICS24 credit score provider by making direct API calls
 * to the local Node.js server and handles the complete OTP flow.
 * 
 * Usage:
 *   php test-nics24-provider.php
 *   php test-nics24-provider.php --mobile=09123456789 --national_code=1234567890
 *   php test-nics24-provider.php --provider=nics24 --debug
 * 
 * Requirements:
 *   - Node.js inquiry-provider server running on port 9999
 *   - Redis server running
 *   - NICS24 credentials configured in config.js
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(600); // 10 minutes timeout for the complete flow

class NICS24ProviderTester
{
    private string $localApiUrl = 'http://127.0.0.1:9999';
    private string $serviceSlug = 'credit-score-rating';
    private array $config;
    private bool $debug = false;
    private ?string $logFile = null;

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'mobile' => '09123456789',
            'national_code' => '1234567890',
            'provider' => 'nics24',
            'timeout' => 300,
            'poll_interval' => 2,
            'debug' => false
        ], $config);

        $this->debug = $this->config['debug'];
        $this->logFile = __DIR__ . '/nics24-test-' . date('Y-m-d-H-i-s') . '.log';

        $this->log("🚀 NICS24 Provider Test Started", 'INFO');
        $this->log("Configuration: " . json_encode($this->config, JSON_PRETTY_PRINT), 'DEBUG');
    }

    /**
     * Main test function
     */
    public function runTest(): array
    {
        try {
            $this->log("=== Starting NICS24 Credit Score Test ===", 'INFO');
            
            // Step 1: Test server connectivity
            $this->testServerConnectivity();
            
            // Step 2: Start credit score request
            $requestResult = $this->startCreditScoreRequest();
            
            if (!$requestResult['success']) {
                throw new Exception('Failed to start request: ' . $requestResult['message']);
            }

            $requestHash = $requestResult['data']['requestHash'];
            $this->log("Request started successfully. Hash: {$requestHash}", 'SUCCESS');

            // Step 3: Monitor progress and handle OTP flow
            $result = $this->monitorRequestProgress($requestHash);

            $this->log("=== Test Completed ===", 'INFO');
            return $result;

        } catch (Exception $e) {
            $this->log("Test failed: " . $e->getMessage(), 'ERROR');
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ];
        }
    }

    /**
     * Test server connectivity
     */
    private function testServerConnectivity(): void
    {
        $this->log("Testing server connectivity...", 'INFO');
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->localApiUrl . '/health',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'User-Agent: NICS24-Tester/1.0'
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception("Connection error: {$error}");
        }

        if ($httpCode !== 200) {
            throw new Exception("Server returned HTTP {$httpCode}. Response: {$response}");
        }

        $this->log("✅ Server connectivity OK", 'SUCCESS');
    }

    /**
     * Start credit score request
     */
    private function startCreditScoreRequest(): array
    {
        $this->log("Starting credit score request...", 'INFO');
        
        $requestData = [
            'mobile' => $this->config['mobile'],
            'national_code' => $this->config['national_code'],
            'provider' => $this->config['provider']
        ];

        $this->log("Request data: " . json_encode($requestData, JSON_PRETTY_PRINT), 'DEBUG');

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->localApiUrl . '/api/services/' . $this->serviceSlug,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($requestData),
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'User-Agent: NICS24-Tester/1.0'
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception("Request failed: {$error}");
        }

        $responseData = json_decode($response, true);
        
        $this->log("API Response - HTTP {$httpCode}: " . json_encode($responseData, JSON_PRETTY_PRINT), 'DEBUG');

        if ($httpCode !== 200) {
            throw new Exception("API returned HTTP {$httpCode}. Response: {$response}");
        }

        if (!$responseData || !isset($responseData['status'])) {
            throw new Exception("Invalid response format: {$response}");
        }

        return [
            'success' => $responseData['status'] === 'success',
            'message' => $responseData['message'] ?? 'Unknown status',
            'data' => $responseData['data'] ?? []
        ];
    }

    /**
     * Monitor request progress and handle OTP
     */
    private function monitorRequestProgress(string $requestHash): array
    {
        $this->log("Monitoring request progress for hash: {$requestHash}", 'INFO');
        
        $startTime = time();
        $maxWaitTime = $this->config['timeout'];
        $pollInterval = $this->config['poll_interval'];
        $otpSubmitted = false;

        while (time() - $startTime < $maxWaitTime) {
            $progress = $this->getProgressStatus($requestHash);
            
            if (!$progress) {
                $this->log("No progress data found", 'WARNING');
                sleep($pollInterval);
                continue;
            }

            $this->displayProgress($progress);

            // Handle different stages
            switch ($progress['stage']) {
                case 'otp_sent':
                    if (!$otpSubmitted) {
                        $otp = $this->handleOtpInput();
                        $this->submitOtp($requestHash, $otp);
                        $otpSubmitted = true;
                    }
                    break;

                case 'completed':
                    $this->log("✅ Request completed successfully!", 'SUCCESS');
                    return [
                        'success' => true,
                        'message' => 'Credit score inquiry completed',
                        'data' => $progress,
                        'duration' => time() - $startTime
                    ];

                case 'failed':
                case 'error':
                    $this->log("❌ Request failed: " . ($progress['message'] ?? 'Unknown error'), 'ERROR');
                    return [
                        'success' => false,
                        'error' => $progress['message'] ?? 'Request failed',
                        'data' => $progress,
                        'duration' => time() - $startTime
                    ];

                case 'timeout':
                    $this->log("⏰ Request timed out", 'WARNING');
                    return [
                        'success' => false,
                        'error' => 'Request timed out',
                        'data' => $progress,
                        'duration' => time() - $startTime
                    ];
            }

            sleep($pollInterval);
        }

        $this->log("⏰ Maximum wait time exceeded", 'ERROR');
        return [
            'success' => false,
            'error' => 'Maximum wait time exceeded',
            'duration' => time() - $startTime
        ];
    }

    /**
     * Get progress status from API
     */
    private function getProgressStatus(string $requestHash): ?array
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->localApiUrl . '/api/progress/' . $requestHash,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'User-Agent: NICS24-Tester/1.0'
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            return $data['data'] ?? null;
        }

        return null;
    }

    /**
     * Display progress to user
     */
    private function displayProgress(array $progress): void
    {
        $percentage = $progress['percentage'] ?? 0;
        $stage = $progress['stage'] ?? 'unknown';
        $message = $progress['message'] ?? 'Processing...';
        
        $progressBar = $this->createProgressBar($percentage);
        
        echo "\r\033[K"; // Clear line
        echo "Progress: {$progressBar} {$percentage}% | Stage: {$stage} | {$message}";
        
        if ($percentage >= 100 || in_array($stage, ['completed', 'failed', 'error', 'timeout'])) {
            echo "\n";
        }
        
        $this->log("Progress: {$percentage}% - {$stage} - {$message}", 'INFO');
    }

    /**
     * Create progress bar
     */
    private function createProgressBar(int $percentage): string
    {
        $width = 20;
        $filled = intval($width * $percentage / 100);
        $empty = $width - $filled;
        
        return '[' . str_repeat('█', $filled) . str_repeat('░', $empty) . ']';
    }

    /**
     * Handle OTP input from user
     */
    private function handleOtpInput(): string
    {
        echo "\n";
        $this->log("📱 OTP has been sent to " . $this->config['mobile'], 'INFO');
        echo "📱 OTP has been sent to " . $this->config['mobile'] . "\n";
        echo "Please enter the 5-digit OTP code: ";
        
        $otp = trim(fgets(STDIN));
        
        if (!preg_match('/^\d{5}$/', $otp)) {
            echo "Invalid OTP format. Please enter exactly 5 digits.\n";
            return $this->handleOtpInput();
        }
        
        $this->log("OTP entered: {$otp}", 'INFO');
        return $otp;
    }

    /**
     * Submit OTP to the system
     */
    private function submitOtp(string $requestHash, string $otp): void
    {
        $this->log("Submitting OTP: {$otp}", 'INFO');
        
        $otpData = [
            'requestHash' => $requestHash,
            'otp' => $otp,
            'mobile' => $this->config['mobile'],
            'national_code' => $this->config['national_code']
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->localApiUrl . '/api/submit-otp',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($otpData),
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'User-Agent: NICS24-Tester/1.0'
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->log("OTP submission response - HTTP {$httpCode}: {$response}", 'DEBUG');
        
        if ($httpCode === 200) {
            $this->log("✅ OTP submitted successfully", 'SUCCESS');
            echo "✅ OTP submitted successfully\n";
        } else {
            $this->log("❌ Failed to submit OTP - HTTP {$httpCode}", 'ERROR');
            echo "❌ Failed to submit OTP\n";
        }
    }

    /**
     * Log messages
     */
    private function log(string $message, string $level = 'INFO'): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] [{$level}] {$message}";
        
        // Always log to file
        if ($this->logFile) {
            file_put_contents($this->logFile, $logMessage . "\n", FILE_APPEND | LOCK_EX);
        }
        
        // Output to console based on level and debug setting
        if ($this->debug || in_array($level, ['SUCCESS', 'ERROR', 'WARNING'])) {
            $colors = [
                'INFO' => "\033[0;36m",    // Cyan
                'SUCCESS' => "\033[0;32m", // Green
                'ERROR' => "\033[0;31m",   // Red
                'WARNING' => "\033[0;33m", // Yellow
                'DEBUG' => "\033[0;90m",   // Dark Gray
            ];
            
            $color = $colors[$level] ?? "\033[0m";
            $reset = "\033[0m";
            
            if ($level !== 'INFO' || $this->debug) {
                echo "{$color}{$logMessage}{$reset}\n";
            }
        }
    }

    /**
     * Get log file path
     */
    public function getLogFile(): string
    {
        return $this->logFile ?? '';
    }
}

// Command line argument parsing
function parseArguments($argv): array
{
    $config = [];
    
    foreach ($argv as $arg) {
        if (strpos($arg, '--') === 0) {
            $parts = explode('=', substr($arg, 2), 2);
            $key = $parts[0];
            $value = $parts[1] ?? true;
            
            // Convert string 'true'/'false' to boolean
            if ($value === 'true') $value = true;
            if ($value === 'false') $value = false;
            
            $config[$key] = $value;
        }
    }
    
    return $config;
}

// Usage information
function showUsage(): void
{
    echo "NICS24 Provider Test Script\n";
    echo "==========================\n\n";
    echo "Usage:\n";
    echo "  php test-nics24-provider.php [options]\n\n";
    echo "Options:\n";
    echo "  --mobile=09123456789       Mobile number for OTP (default: 09123456789)\n";
    echo "  --national_code=1234567890 National code (default: 1234567890)\n";
    echo "  --provider=nics24          Provider name (default: nics24)\n";
    echo "  --timeout=300              Timeout in seconds (default: 300)\n";
    echo "  --poll_interval=2          Progress polling interval (default: 2)\n";
    echo "  --debug                    Enable debug output\n";
    echo "  --help                     Show this help message\n\n";
    echo "Examples:\n";
    echo "  php test-nics24-provider.php\n";
    echo "  php test-nics24-provider.php --debug\n";
    echo "  php test-nics24-provider.php --mobile=09123456789 --national_code=1234567890\n";
    echo "  php test-nics24-provider.php --provider=nics24 --timeout=600 --debug\n\n";
    echo "Requirements:\n";
    echo "  - Node.js inquiry-provider server running on port 9999\n";
    echo "  - Redis server running\n";
    echo "  - NICS24 credentials configured in config.js\n";
}

// Main execution
if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.\n");
}

// Parse command line arguments
$config = parseArguments($argv);

// Show help if requested
if (isset($config['help'])) {
    showUsage();
    exit(0);
}

// Show header
echo "\n";
echo "🔍 NICS24 Provider Test Script\n";
echo "===============================\n\n";

try {
    // Create and run tester
    $tester = new NICS24ProviderTester($config);
    $result = $tester->runTest();
    
    // Display final results
    echo "\n=== FINAL RESULTS ===\n";
    if ($result['success']) {
        echo "✅ Test Status: SUCCESS\n";
        echo "⏱️  Duration: " . ($result['duration'] ?? 'Unknown') . " seconds\n";
        if (isset($result['data'])) {
            echo "📊 Final Data: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
        }
    } else {
        echo "❌ Test Status: FAILED\n";
        echo "💥 Error: " . ($result['error'] ?? 'Unknown error') . "\n";
        echo "⏱️  Duration: " . ($result['duration'] ?? 'Unknown') . " seconds\n";
    }
    
    echo "\n📋 Log file: " . $tester->getLogFile() . "\n";
    
    exit($result['success'] ? 0 : 1);

} catch (Exception $e) {
    echo "💥 Fatal Error: " . $e->getMessage() . "\n";
    exit(1);
}