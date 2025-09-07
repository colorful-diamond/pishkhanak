<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Service;
use App\Services\GeminiService;

class MatchServiceHtmlFiles extends Command
{
    protected $signature = 'services:match-html {--dry-run : Test run without making changes} {--threshold=80 : Confidence threshold for auto-rename}';
    
    protected $description = 'Match HTML files to correct service IDs using AI analysis';
    
    private $services;
    private $backupDir;
    private $logFile;
    
    public function handle()
    {
        $this->info('🤖 AI Service HTML Matcher Started');
        $this->info('=====================================');
        
        $dryRun = $this->option('dry-run');
        $threshold = (int) $this->option('threshold');
        
        if ($dryRun) {
            $this->warn('🧪 DRY RUN MODE - No changes will be made');
        } else {
            $this->error('⚠️  LIVE MODE - Files will be renamed!');
        }
        
        $this->line("Confidence threshold: {$threshold}%");
        $this->line('');
        
        // Setup
        $this->setupDirectories();
        $this->loadServices();
        
        // Process HTML files
        $results = $this->processHtmlFiles($dryRun, $threshold);
        
        // Generate report
        $this->generateReport($results);
        
        return 0;
    }
    
    private function setupDirectories()
    {
        $this->backupDir = base_path('html_backup_' . now()->format('Y_m_d_H_i_s'));
        $this->logFile = base_path('ai_service_matching_' . now()->format('Y_m_d_H_i_s') . '.log');
        
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
        
        $this->logMessage("Backup directory: {$this->backupDir}");
    }
    
    private function loadServices()
    {
        $this->services = Service::where('status', 'active')
            ->select('id', 'title', 'short_title', 'slug', 'summary', 'content')
            ->get();
            
        $this->info("📚 Loaded {$this->services->count()} active services");
        $this->logMessage("Loaded {$this->services->count()} active services");
    }
    
    private function processHtmlFiles($dryRun, $threshold)
    {
        $htmlDirs = [
            storage_path('app/generated_service_content'),
            base_path('generated_content_html')
        ];
        
        $results = [
            'processed' => 0,
            'matched' => 0,
            'renamed' => 0,
            'errors' => 0,
            'manual_review' => []
        ];
        
        foreach ($htmlDirs as $dir) {
            if (!is_dir($dir)) {
                $this->warn("📁 Directory not found: $dir");
                continue;
            }
            
            $files = glob($dir . '/*.html');
            $this->info("📁 Processing " . count($files) . " files in $dir");
            
            $progressBar = $this->output->createProgressBar(count($files));
            $progressBar->start();
            
            foreach ($files as $file) {
                $this->processFile($file, $results, $dryRun, $threshold);
                $progressBar->advance();
                
                // Small delay to avoid overwhelming services
                usleep(100000); // 0.1 second
            }
            
            $progressBar->finish();
            $this->line('');
        }
        
        return $results;
    }
    
    private function processFile($filePath, &$results, $dryRun, $threshold)
    {
        try {
            $fileName = basename($filePath);
            $currentId = pathinfo($fileName, PATHINFO_FILENAME);
            
            $results['processed']++;
            
            // Extract content from HTML
            $extractedData = $this->extractContentFromHtml($filePath);
            if (!$extractedData) {
                $this->logMessage("ERROR: Could not extract content from $fileName");
                $results['errors']++;
                return;
            }
            
            // Find best match using AI
            $match = $this->findBestServiceMatch($extractedData);
            
            if ($match) {
                $results['matched']++;
                $confidence = $match['confidence'];
                $targetServiceId = $match['service_id'];
                $reasoning = $match['reasoning'];
                
                $this->logMessage("MATCH: $fileName -> Service ID $targetServiceId (Confidence: {$confidence}%)");
                $this->logMessage("Reasoning: $reasoning");
                
                if ($confidence >= $threshold) {
                    // High confidence - proceed with rename
                    if ($this->renameFile($filePath, $targetServiceId, $dryRun)) {
                        $results['renamed']++;
                        $this->logMessage("SUCCESS: Renamed $fileName to $targetServiceId.html");
                    } else {
                        $this->logMessage("ERROR: Failed to rename $fileName");
                        $results['errors']++;
                    }
                } else {
                    // Low confidence - add to manual review
                    $results['manual_review'][] = [
                        'file' => $fileName,
                        'current_id' => $currentId,
                        'suggested_id' => $targetServiceId,
                        'confidence' => $confidence,
                        'reasoning' => $reasoning,
                        'title' => $extractedData['title'],
                        'service_title' => $match['service_title']
                    ];
                }
            } else {
                $this->logMessage("NO MATCH: Could not find matching service for $fileName");
                $results['manual_review'][] = [
                    'file' => $fileName,
                    'current_id' => $currentId,
                    'suggested_id' => null,
                    'confidence' => 0,
                    'reasoning' => 'No suitable match found',
                    'title' => $extractedData['title'],
                    'service_title' => null
                ];
            }
            
        } catch (\Exception $e) {
            $this->logMessage("EXCEPTION in processFile: " . $e->getMessage());
            $results['errors']++;
        }
    }
    
    private function extractContentFromHtml($filePath)
    {
        $htmlContent = file_get_contents($filePath);
        if (!$htmlContent) {
            return null;
        }
        
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">' . $htmlContent);
        libxml_clear_errors();
        
        $xpath = new \DOMXPath($dom);
        
        // Extract key content
        $titleNodes = $xpath->query('//title');
        $title = $titleNodes->length > 0 ? trim($titleNodes->item(0)->textContent) : '';
        
        $descNodes = $xpath->query('//meta[@name="description"]/@content');
        $description = $descNodes->length > 0 ? trim($descNodes->item(0)->textContent) : '';
        
        $keywordNodes = $xpath->query('//meta[@name="keywords"]/@content');
        $keywords = $keywordNodes->length > 0 ? trim($keywordNodes->item(0)->textContent) : '';
        
        // Get headings
        $headings = [];
        for ($i = 1; $i <= 3; $i++) {
            $headingNodes = $xpath->query("//h$i");
            if ($headingNodes) {
                foreach ($headingNodes as $node) {
                    $headings[] = $node->textContent;
                }
            }
        }
        
        $extractedData = [
            'title' => $title,
            'description' => $description, 
            'keywords' => $keywords,
            'headings' => $headings
        ];
        
        // Try AI analysis first
        try {
            return $this->useGeminiAI($extractedData);
        } catch (\Exception $e) {
            \Log::warning("0c66acc4" . "AI Analysis failed: " . $e->getMessage());
        }
        
        // Fallback to rule-based matching
        return $this->useRuleBasedMatching($extractedData);
    }
    
    private function useGeminiAI($extractedData)
    {
        try {
            $gemini = app(GeminiService::class);
            
            // Prepare services data for AI
            $servicesData = $this->services->map(function($service) {
                return [
                    'id' => $service->id,
                    'title' => $service->title,
                    'slug' => $service->slug,
                    'summary' => substr($service->summary ?? '', 0, 200)
                ];
            })->toArray();
            
            $prompt = $this->buildAIPrompt($extractedData, $servicesData);
            
            $this->logMessage("🤖 Analyzing with Gemini AI...");
            $response = $gemini->generateContent($prompt);
            
            // Parse JSON response
            $decoded = json_decode($response, true);
            if ($decoded && isset($decoded['service_id'])) {
                $service = $this->services->firstWhere('id', $decoded['service_id']);
                if ($service) {
                    return [
                        'service_id' => $decoded['service_id'],
                        'confidence' => $decoded['confidence'] ?? 70,
                        'reasoning' => $decoded['reasoning'] ?? 'Gemini AI analysis',
                        'service_title' => $service->title
                    ];
                }
            }
            
        } catch (\Exception $e) {
            $this->logMessage("Gemini AI error: " . $e->getMessage());
        }
        
        return null;
    }
    
    private function buildAIPrompt($data, $servicesData)
    {
        $servicesJson = json_encode($servicesData, JSON_UNESCAPED_UNICODE);
        
        return "59056b09" . implode(', ', $data['headings']) . "PERSIAN_TEXT_d4bbbf01";
    }
    
    private function useRuleBasedMatching($extractedData)
    {
        $bestMatch = null;
        $bestScore = 0;
        
        $searchText = $extractedData['title'] . ' ' . $extractedData['description'] . ' ' . implode(' ', $extractedData['headings']);
        $searchText = $this->normalizeText($searchText);
        
        foreach ($this->services as $service) {
            $serviceText = $service->title . ' ' . ($service->short_title ?? '') . ' ' . ($service->summary ?? '');
            $serviceText = $this->normalizeText($serviceText);
            
            $score = $this->calculateSimilarity($searchText, $serviceText);
            
            if ($score > $bestScore && $score > 0.3) {
                $bestScore = $score;
                $bestMatch = [
                    'service_id' => $service->id,
                    'confidence' => round($score * 100),
                    'reasoning' => "Rule-based similarity: PERSIAN_TEXT_698d604e"
                ];
            }
        }
        
        return $bestMatch;
    }
        
    private function renameHtmlFile($filePath, $newPath)
    {
        if ($this->option('dry-run')) {
            $this->info("DRY RUN: Would rename $filePath to $newPath");
            return true;
        }
        
        if (file_exists($newPath)) {
            copy($newPath, $newPath . '.backup.' . time());
        }
        
        return rename($filePath, $newPath);
    }
    
    private function generateReport($results)
    {
        $this->line('');
        $this->info('📊 AI SERVICE MATCHING REPORT');
        $this->info('===============================');
        
        $this->line("📁 Files processed: {$results['processed']}");
        $this->line("✅ Matches found: {$results['matched']}");
        $this->line("🔄 Files renamed: {$results['renamed']}");
        $this->line("❌ Errors: {$results['errors']}");
        $this->line("👤 Manual review needed: " . count($results['manual_review']));
        
        if (!empty($results['manual_review'])) {
            $this->line('');
            $this->warn('📋 Files needing manual review:');
            
            foreach (array_slice($results['manual_review'], 0, 10) as $item) {
                $this->line("  • {$item['file']} -> " . 
                    ($item['suggested_id'] ? "Service ID {$item['suggested_id']}" : "No match") . 
                    " (Confidence: {$item['confidence']}%)");
            }
            
            if (count($results['manual_review']) > 10) {
                $this->line("  ... and " . (count($results['manual_review']) - 10) . " more");
            }
            
            // Save manual review data
            $reviewFile = base_path('manual_review_' . now()->format('Y_m_d_H_i_s') . '.json');
            file_put_contents($reviewFile, json_encode($results['manual_review'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->line("💾 Manual review data saved to: $reviewFile");
        }
        
        $this->line('');
        $this->info("📂 Backup created at: {$this->backupDir}");
        $this->info("📝 Log file: {$this->logFile}");
    }
    
    private function logMessage($message)
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";
        file_put_contents($this->logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
}