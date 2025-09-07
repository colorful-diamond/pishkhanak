<?php

namespace App\Console\Commands;

use App\Services\LocalRequestService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class DebugLocalRequest extends Command
{
    protected $signature = 'debug:local-request {hash?} {--list : List all active requests} {--redis : Show raw Redis data}';
    protected $description = 'Debug local requests in Redis';

    private LocalRequestService $localRequestService;

    public function handle()
    {
        $this->localRequestService = app(LocalRequestService::class);

        if ($this->option('list')) {
            $this->listAllRequests();
            return;
        }

        $hash = $this->argument('hash');
        if (!$hash) {
            $this->error('Please provide a request hash or use --list to see all requests');
            return;
        }

        $this->debugSpecificRequest($hash);
    }

    private function listAllRequests()
    {
        $this->info('📋 All Active Local Requests');
        $this->line(str_repeat('=', 50));

        try {
            // Get all request hashes
            $allHashes = Redis::lrange('local_requests_list', 0, -1);

            if (empty($allHashes)) {
                $this->warn('No active requests found');
                return;
            }

            $this->table(
                ['Hash', 'Service', 'Status', 'Progress', 'Step', 'Started'],
                collect($allHashes)->map(function ($hash) {
                    $request = $this->localRequestService->getRequest($hash);
                    if (!$request) {
                        return [$hash, 'NOT FOUND', 'ERROR', '0%', 'unknown', 'unknown'];
                    }

                    return [
                        substr($hash, 0, 16) . '...',
                        $request['service_slug'] ?? 'unknown',
                        $request['status'] ?? 'unknown',
                        ($request['progress'] ?? 0) . '%',
                        $request['step'] ?? 'unknown',
                        isset($request['started_at']) 
                            ? \Carbon\Carbon::parse($request['started_at'])->format('H:i:s')
                            : 'unknown'
                    ];
                })->toArray()
            );

        } catch (\Exception $e) {
            $this->error('Error listing requests: ' . $e->getMessage());
        }
    }

    private function debugSpecificRequest(string $hash)
    {
        $this->info("🔍 Debugging Request: {$hash}");
        $this->line(str_repeat('=', 50));

        try {
            // Get formatted request data
            $requestStatus = $this->localRequestService->getRequestStatus($hash);
            $rawRequest = $this->localRequestService->getRequest($hash);

            if (!$requestStatus || !$rawRequest) {
                $this->error('Request not found in Redis');
                
                // Check if it exists in Redis with any variation
                $this->checkRedisVariations($hash);
                return;
            }

            // Show formatted status
            $this->info('📊 Formatted Status Data:');
            $this->displayKeyValue($requestStatus);

            $this->newLine();

            // Show raw Redis data if requested
            if ($this->option('redis')) {
                $this->info('🔴 Raw Redis Data:');
                $this->displayKeyValue($rawRequest);
            }

            $this->newLine();

            // Show Redis key info
            $redisKey = 'local_request:' . $hash;
            $ttl = Redis::ttl($redisKey);
            $this->info("🔑 Redis Key Info:");
            $this->line("Key: {$redisKey}");
            $this->line("TTL: {$ttl} seconds (" . ($ttl > 0 ? gmdate('H:i:s', $ttl) : 'expired') . ")");

            // Show progress timeline
            $this->showProgressTimeline($rawRequest);

        } catch (\Exception $e) {
            $this->error('Error debugging request: ' . $e->getMessage());
        }
    }

    private function checkRedisVariations(string $hash)
    {
        $this->warn('Checking for variations...');

        // Check if hash exists without prefix
        if (!str_starts_with($hash, 'req_')) {
            $fullHash = 'req_' . $hash;
            if (Redis::exists('local_request:' . $fullHash)) {
                $this->info("Found with req_ prefix: {$fullHash}");
                return;
            }
        }

        // Search for partial matches
        $keys = Redis::keys('local_request:*');
        $matches = array_filter($keys, function ($key) use ($hash) {
            return str_contains($key, $hash);
        });

        if (!empty($matches)) {
            $this->info('Found similar keys:');
            foreach ($matches as $key) {
                $this->line("  - {$key}");
            }
        } else {
            $this->warn('No similar keys found in Redis');
        }
    }

    private function displayKeyValue(array $data, int $indent = 0)
    {
        $prefix = str_repeat('  ', $indent);
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $this->line("{$prefix}{$key}:");
                $this->displayKeyValue($value, $indent + 1);
            } elseif (is_null($value)) {
                $this->line("{$prefix}{$key}: <fg=gray>null</>");
            } elseif (is_bool($value)) {
                $this->line("{$prefix}{$key}: <fg=yellow>" . ($value ? 'true' : 'false') . "</>");
            } else {
                $color = $this->getValueColor($key, $value);
                $this->line("{$prefix}{$key}: <fg={$color}>{$value}</>");
            }
        }
    }

    private function getValueColor(string $key, $value): string
    {
        // Color coding for better readability
        if ($key === 'status') {
            return match($value) {
                'completed' => 'green',
                'failed' => 'red',
                'otp_required' => 'yellow',
                'processing' => 'blue',
                default => 'white'
            };
        }

        if ($key === 'progress') {
            return $value >= 100 ? 'green' : 'cyan';
        }

        if (str_contains($key, 'hash')) {
            return 'magenta';
        }

        if (str_contains($key, 'time') || str_contains($key, 'at')) {
            return 'gray';
        }

        return 'white';
    }

    private function showProgressTimeline(array $request)
    {
        $this->info('📈 Progress Timeline:');
        
        $progress = $request['progress'] ?? 0;
        $step = $request['step'] ?? 'unknown';
        
        $steps = [
            'initializing' => ['name' => 'Initializing', 'progress' => 0],
            'authentication' => ['name' => 'Authentication', 'progress' => 50],
            'sending_otp' => ['name' => 'Sending OTP', 'progress' => 70],
            'waiting_otp' => ['name' => 'Waiting OTP', 'progress' => 70],
            'verifying_otp' => ['name' => 'Verifying OTP', 'progress' => 80],
            'processing_result' => ['name' => 'Processing Result', 'progress' => 90],
            'completed' => ['name' => 'Completed', 'progress' => 100]
        ];

        foreach ($steps as $stepKey => $stepData) {
            $symbol = '○'; // Empty circle
            $color = 'gray';
            
            if ($stepKey === $step) {
                $symbol = '●'; // Filled circle (current)
                $color = 'yellow';
            } elseif ($stepData['progress'] < $progress) {
                $symbol = '✓'; // Checkmark (completed)
                $color = 'green';
            }

            $this->line("  <fg={$color}>{$symbol}</> {$stepData['name']} ({$stepData['progress']}%)");
        }
    }
} 