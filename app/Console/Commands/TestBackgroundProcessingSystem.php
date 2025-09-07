<?php

namespace App\Console\Commands;

use App\Services\LocalRequestService;
use App\Http\Controllers\Services\ServiceControllerFactory;
use App\Models\Service;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

class TestBackgroundProcessingSystem extends Command
{
    protected $signature = 'test:background-system {--service=credit-score-rating}';
    protected $description = 'Test the complete background processing system';

    private LocalRequestService $localRequestService;
    private array $testResults = [];

    public function handle()
    {
        $this->localRequestService = app(LocalRequestService::class);
        $serviceSlug = $this->option('service');

        $this->info("🧪 Testing Background Processing System");
        $this->info("Service: {$serviceSlug}");
        $this->newLine();

        // Run all tests
        $this->testRedisConnection();
        $this->testServiceController($serviceSlug);
        $this->testLocalRequestService();
        $this->testLocalApiServer();
        $this->testQueueSystem();
        $this->testRedisUpdaterBot();
        $this->testCompleteFlow($serviceSlug);

        // Show results
        $this->showTestResults();

        return $this->hasFailures() ? 1 : 0;
    }

    private function testRedisConnection()
    {
        $this->info("1️⃣ Testing Redis Connection...");
        
        try {
            // Test basic Redis operations
            Redis::set('test_key', 'test_value');
            $value = Redis::get('test_key');
            Redis::del('test_key');

            if ($value === 'test_value') {
                $this->addSuccess('Redis Connection', 'Basic read/write operations work');
            } else {
                $this->addFailure('Redis Connection', 'Read/write test failed');
                return;
            }

            // Test Redis TTL
            Redis::setex('test_ttl', 5, 'ttl_test');
            $ttl = Redis::ttl('test_ttl');
            
            if ($ttl > 0 && $ttl <= 5) {
                $this->addSuccess('Redis TTL', 'TTL functionality works');
            } else {
                $this->addFailure('Redis TTL', 'TTL test failed');
            }

            // Test Pub/Sub
            Redis::publish('test_channel', 'test_message');
            $this->addSuccess('Redis Pub/Sub', 'Publish functionality works');

        } catch (\Exception $e) {
            $this->addFailure('Redis Connection', $e->getMessage());
        }
    }

    private function testServiceController(string $serviceSlug)
    {
        $this->info("2️⃣ Testing Service Controller...");

        try {
            // Find service
            $service = Service::where('slug', $serviceSlug)->first();
            if (!$service) {
                $this->addFailure('Service Model', "Service '{$serviceSlug}' not found in database");
                return;
            }
            $this->addSuccess('Service Model', "Service '{$serviceSlug}' found");

            // Get controller
            $controller = ServiceControllerFactory::getController($service);
            if (!$controller) {
                $this->addFailure('Service Controller', "No controller found for '{$serviceSlug}'");
                return;
            }
            $this->addSuccess('Service Controller', "Controller found: " . get_class($controller));

            // Check if it's using LocalApiController
            if ($controller instanceof \App\Http\Controllers\Services\LocalApiController) {
                $this->addSuccess('Controller Type', 'Uses LocalApiController for background processing');
            } else {
                $this->addFailure('Controller Type', 'Not using LocalApiController - no background processing');
            }

        } catch (\Exception $e) {
            $this->addFailure('Service Controller', $e->getMessage());
        }
    }

    private function testLocalRequestService()
    {
        $this->info("3️⃣ Testing LocalRequestService...");

        try {
            // Test creating request
            $testData = [
                'mobile' => '09123456789',
                'national_code' => '1234567890'
            ];

            $request = $this->localRequestService->createRequest(
                'test-service',
                1,
                $testData,
                123,
                'test-session',
                150
            );

            if (isset($request['hash']) && str_starts_with($request['hash'], 'req_')) {
                $this->addSuccess('Create Request', "Request created with hash: {$request['hash']}");
                $testHash = $request['hash'];
            } else {
                $this->addFailure('Create Request', 'Invalid request hash format');
                return;
            }

            // Test getting request
            $retrieved = $this->localRequestService->getRequest($testHash);
            if ($retrieved && $retrieved['hash'] === $testHash) {
                $this->addSuccess('Get Request', 'Request retrieved successfully');
            } else {
                $this->addFailure('Get Request', 'Failed to retrieve request');
                return;
            }

            // Test updating progress
            $updated = $this->localRequestService->updateProgress($testHash, 50, 'testing', 'Test progress');
            if ($updated) {
                $this->addSuccess('Update Progress', 'Progress updated successfully');
            } else {
                $this->addFailure('Update Progress', 'Failed to update progress');
            }

            // Test OTP required
            $otpSet = $this->localRequestService->markAsOtpRequired($testHash, ['hash' => 'test-otp', 'expiry' => 300]);
            if ($otpSet) {
                $this->addSuccess('Mark OTP Required', 'OTP status set successfully');
            } else {
                $this->addFailure('Mark OTP Required', 'Failed to set OTP status');
            }

            // Test completion
            $completed = $this->localRequestService->markAsCompleted($testHash, ['message' => 'Test completed']);
            if ($completed) {
                $this->addSuccess('Mark Completed', 'Request marked as completed');
            } else {
                $this->addFailure('Mark Completed', 'Failed to mark as completed');
            }

            // Test status retrieval
            $status = $this->localRequestService->getRequestStatus($testHash);
            if ($status && $status['status'] === 'completed') {
                $this->addSuccess('Get Status', 'Status retrieval works correctly');
            } else {
                $this->addFailure('Get Status', 'Status retrieval failed');
            }

            // Cleanup test data
            Redis::del('local_request:' . $testHash);

        } catch (\Exception $e) {
            $this->addFailure('LocalRequestService', $e->getMessage());
        }
    }

    private function testLocalApiServer()
    {
        $this->info("4️⃣ Testing Local API Server...");

        $url = config('services.local_api.url', 'http://127.0.0.1:9999');

        try {
            // Test server is running
            $response = Http::timeout(5)->get($url);
            if ($response->successful()) {
                $this->addSuccess('Local API Server', 'Server is running and responding');
            } else {
                $this->addFailure('Local API Server', "Server returned status: {$response->status()}");
                return;
            }

            // Test services endpoint
            $servicesResponse = Http::timeout(5)->get("{$url}/api/services");
            if ($servicesResponse->successful()) {
                $services = $servicesResponse->json();
                if (is_array($services) && in_array('credit-score-rating', $services)) {
                    $this->addSuccess('Services Endpoint', 'credit-score-rating service found');
                } else {
                    $this->addFailure('Services Endpoint', 'credit-score-rating service not found');
                }
            } else {
                $this->addFailure('Services Endpoint', 'Services endpoint not responding');
            }

        } catch (\Exception $e) {
            $this->addFailure('Local API Server', $e->getMessage());
        }
    }

    private function testQueueSystem()
    {
        $this->info("5️⃣ Testing Queue System...");

        try {
            // Check queue configuration
            $queueConfig = config('queue.default');
            if ($queueConfig === 'redis') {
                $this->addSuccess('Queue Config', 'Queue using Redis driver');
            } else {
                $this->addWarning('Queue Config', "Queue using {$queueConfig} driver, Redis recommended");
            }

            // Test job dispatch (without actually processing)
            $testHash = 'req_TEST' . strtoupper(substr(md5(time()), 0, 12));
            
            try {
                \App\Jobs\ProcessLocalRequestJob::dispatch($testHash);
                $this->addSuccess('Job Dispatch', 'ProcessLocalRequestJob dispatched successfully');
            } catch (\Exception $e) {
                $this->addFailure('Job Dispatch', $e->getMessage());
            }

        } catch (\Exception $e) {
            $this->addFailure('Queue System', $e->getMessage());
        }
    }

    private function testRedisUpdaterBot()
    {
        $this->info("6️⃣ Testing Redis Updater Bot...");

        $botPath = base_path('bots/inquiry-provider/services/redis-updater.js');
        
        if (file_exists($botPath)) {
            $this->addSuccess('Redis Updater File', 'redis-updater.js exists');
            
            // Check if it imports Redis
            $content = file_get_contents($botPath);
            if (str_contains($content, 'ioredis')) {
                $this->addSuccess('Redis Import', 'ioredis dependency found');
            } else {
                $this->addFailure('Redis Import', 'ioredis dependency missing');
            }

            if (str_contains($content, 'updateProgress')) {
                $this->addSuccess('Update Methods', 'updateProgress method found');
            } else {
                $this->addFailure('Update Methods', 'updateProgress method missing');
            }

        } else {
            $this->addFailure('Redis Updater File', 'redis-updater.js not found');
        }

        // Check credit score rating integration
        $creditScorePath = base_path('bots/inquiry-provider/services/credit-score-rating/index.js');
        if (file_exists($creditScorePath)) {
            $content = file_get_contents($creditScorePath);
            if (str_contains($content, 'redisUpdater')) {
                $this->addSuccess('Credit Score Integration', 'redisUpdater imported in credit-score-rating');
            } else {
                $this->addFailure('Credit Score Integration', 'redisUpdater not imported');
            }

            if (str_contains($content, 'requestHash')) {
                $this->addSuccess('Hash Parameter', 'requestHash parameter handled');
            } else {
                $this->addFailure('Hash Parameter', 'requestHash parameter missing');
            }
        }
    }

    private function testCompleteFlow(string $serviceSlug)
    {
        $this->info("7️⃣ Testing Complete Flow...");

        try {
            // Create a test request through the service
            $service = Service::where('slug', $serviceSlug)->first();
            $controller = ServiceControllerFactory::getController($service);

            if ($controller instanceof \App\Http\Controllers\Services\LocalApiController) {
                // Test data
                $testData = [
                    'mobile' => '09123456789',
                    'national_code' => '1234567890'
                ];

                // Test process method
                $result = $controller->process($testData, $service);
                
                if ($result['success'] && $result['type'] === 'background_processing') {
                    $this->addSuccess('Complete Flow', "Background processing initiated with hash: {$result['hash']}");
                    
                    // Verify the request exists in Redis
                    $requestStatus = $this->localRequestService->getRequestStatus($result['hash']);
                    if ($requestStatus) {
                        $this->addSuccess('Flow Verification', 'Request properly stored in Redis');
                    } else {
                        $this->addFailure('Flow Verification', 'Request not found in Redis');
                    }

                    // Cleanup
                    Redis::del('local_request:' . $result['hash']);
                    
                } else {
                    $this->addFailure('Complete Flow', 'Process method failed');
                }
            } else {
                $this->addFailure('Complete Flow', 'Controller not using background processing');
            }

        } catch (\Exception $e) {
            $this->addFailure('Complete Flow', $e->getMessage());
        }
    }

    private function addSuccess(string $test, string $message)
    {
        $this->testResults[] = ['type' => 'success', 'test' => $test, 'message' => $message];
        $this->line("  ✅ {$test}: {$message}");
    }

    private function addFailure(string $test, string $message)
    {
        $this->testResults[] = ['type' => 'failure', 'test' => $test, 'message' => $message];
        $this->line("  ❌ {$test}: {$message}");
    }

    private function addWarning(string $test, string $message)
    {
        $this->testResults[] = ['type' => 'warning', 'test' => $test, 'message' => $message];
        $this->line("  ⚠️  {$test}: {$message}");
    }

    private function showTestResults()
    {
        $this->newLine();
        $this->info("📊 Test Results Summary");
        $this->line(str_repeat('=', 50));

        $successes = collect($this->testResults)->where('type', 'success')->count();
        $failures = collect($this->testResults)->where('type', 'failure')->count();
        $warnings = collect($this->testResults)->where('type', 'warning')->count();

        $this->line("✅ Successes: {$successes}");
        $this->line("❌ Failures: {$failures}");
        $this->line("⚠️  Warnings: {$warnings}");

        if ($failures === 0) {
            $this->newLine();
            $this->info("🎉 All tests passed! The background processing system is working correctly.");
            $this->info("Your credit-score-rating service is properly integrated!");
        } else {
            $this->newLine();
            $this->error("💥 Some tests failed. Please fix the issues above before using the system.");
        }
    }

    private function hasFailures(): bool
    {
        return collect($this->testResults)->where('type', 'failure')->count() > 0;
    }
} 