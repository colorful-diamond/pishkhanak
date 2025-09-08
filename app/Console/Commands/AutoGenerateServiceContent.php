<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Jobs\AutoGenerateServiceContentJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;

class AutoGenerateServiceContent extends Command
{
    protected $signature = 'services:auto-generate-content {--force} {--test-mode}';
    protected $description = 'Automatically generate content for the next service in sequence';

    public function handle()
    {
        $force = $this->option('force');
        $testMode = $this->option('test-mode');
        
        if ($testMode) {
            $this->info('Running in test mode - no actual jobs will be dispatched');
        }

        // Get the next service to process
        $service = $this->getNextService($force, $testMode);
        
        if (!$service) {
            $this->info('No services found that need content generation.');
            return 0;
        }

        $this->info("Processing service: {$service->title} (ID: {$service->id})");
        
        if (!$testMode) {
            // Dispatch the job
            AutoGenerateServiceContentJob::dispatch($service);
            $this->info("Job dispatched for service: {$service->title}");
        } else {
            $this->info("TEST MODE: Would dispatch job for service: {$service->title}");
        }

        return 0;
    }

    protected function getNextService(bool $force = false, bool $testMode = false)
    {
        // FIRST PRIORITY: Check for any services with empty/removed content
        $emptyContentQuery = Service::where('status', 'active');
        
        if (!$force) {
            $emptyContentQuery->where(function($q) {
                $q->whereNull('content')
                    ->orWhere('content', '')
                    ->orWhere(function($sq) {
                        // Content is not numeric (not an AI content ID)
                        // Check if content exists but is not a valid AI content ID
                        $sq->whereNotNull('content')
                           ->where('content', '!=', '')
                           ->where(function($q) {
                               // Content is not numeric OR doesn't exist in ai_contents
                               // We check if content is not a valid AI content reference
                               $q->whereNull('content')
                                 ->orWhere('content', '=', '')
                                 ->orWhere('content', 'LIKE', '%<%')  // Contains HTML
                                 ->orWhere('content', 'LIKE', '%>%')  // Contains HTML
                                 ->orWhereNotExists(function($query) {
                                     // Check if the numeric content exists in ai_contents
                                     $query->select(\DB::raw(1))
                                           ->from('ai_contents')
                                           ->whereRaw('CAST(ai_contents.id AS TEXT) = services.content')
                                           ->whereRaw('services.content ~ ?', ['^[0-9]+$']);
                                 });
                           });
                    });
            });
        }
        
        $emptyContentService = $emptyContentQuery->orderBy('id')->first();
        
        if ($emptyContentService) {
            $this->info("Found service with empty/removed content to regenerate: {$emptyContentService->title} (ID: {$emptyContentService->id})");
            return $emptyContentService;
        }

        // SECOND PRIORITY: Sequential processing from where we left off
        
        // Check for any active services without AI content
        $query = Service::where('status', 'active');
        
        if (!$force) {
            $query->where(function($q) {
                $q->whereNull('content')
                    ->orWhere('content', '')
                    ->orWhere(function($sq) {
                        // Content is not numeric (not an AI content ID)
                        // Check if content exists but is not a valid AI content ID
                        $sq->whereNotNull('content')
                           ->where('content', '!=', '')
                           ->where(function($q) {
                               // Content is not numeric OR doesn't exist in ai_contents
                               // We check if content is not a valid AI content reference
                               $q->whereNull('content')
                                 ->orWhere('content', '=', '')
                                 ->orWhere('content', 'LIKE', '%<%')  // Contains HTML
                                 ->orWhere('content', 'LIKE', '%>%')  // Contains HTML
                                 ->orWhereNotExists(function($query) {
                                     // Check if the numeric content exists in ai_contents
                                     $query->select(\DB::raw(1))
                                           ->from('ai_contents')
                                           ->whereRaw('CAST(ai_contents.id AS TEXT) = services.content')
                                           ->whereRaw('services.content ~ ?', ['^[0-9]+$']);
                                 });
                           });
                    });
            });
        }
        
        // Get the next service in sequence
        $service = $query->orderBy('id')->first();
        
        if ($service && $testMode) {
            $this->info("Found service for sequential processing: {$service->title} (ID: {$service->id})");
            $this->info("Service content: " . ($service->content ?: 'NULL/EMPTY'));
        }
        
        return $service;
    }
}