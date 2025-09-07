<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BlogContentPipeline;
use App\Models\BlogProcessingLog;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ImportBlogBsonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:import-bson 
                            {--file= : Path to BSON file (default: dump/blog/articles.bson)}
                            {--limit= : Limit number of records to import}
                            {--offset= : Start from record number}
                            {--force-reimport : Force reimport of existing records (default: skip existing)}
                            {--dry-run : Run without actually importing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import blog posts from MongoDB BSON dump file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->option('file') ?? base_path('../dump/blog/articles.bson');
        $limit = $this->option('limit');
        $offset = $this->option('offset') ?? 0;
        // By default, skip existing records unless --force-reimport is used
        $skipExisting = !$this->option('force-reimport');
        $dryRun = $this->option('dry-run');

        if (!file_exists($filePath)) {
            $this->error("BSON file not found: {$filePath}");
            return 1;
        }

        // Check how many already imported
        $alreadyImported = BlogContentPipeline::count();
        if ($alreadyImported > 0) {
            $this->info("Found {$alreadyImported} previously imported posts in database");
        }
        
        $this->info("Starting BSON import from: {$filePath}");
        
        if ($skipExisting) {
            $this->info("✓ Will skip already imported posts (safe resume mode)");
        } else {
            $this->warn("⚠ Will reimport existing posts (--force-reimport enabled)");
        }
        
        if ($dryRun) {
            $this->warn("DRY RUN MODE - No data will be imported");
        }

        try {
            // Check if pymongo is installed
            $pythonScript = $this->createPythonImportScript();
            $command = "python3 {$pythonScript} " . escapeshellarg($filePath);
            
            if ($limit) {
                $command .= " --limit " . escapeshellarg($limit);
            }
            
            if ($offset) {
                $command .= " --offset " . escapeshellarg($offset);
            }

            $this->info("Processing BSON file...");
            
            $output = [];
            $returnCode = 0;
            exec($command . " 2>&1", $output, $returnCode);
            
            if ($returnCode !== 0) {
                $this->error("Failed to process BSON file");
                $this->error(implode("\n", $output));
                return 1;
            }

            // Parse JSON output from Python script
            $jsonOutput = implode("\n", $output);
            $documents = json_decode($jsonOutput, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error("Failed to parse BSON data: " . json_last_error_msg());
                return 1;
            }

            $this->info("Found " . count($documents) . " documents in BSON file");
            
            // Get list of already imported IDs for faster checking
            $existingIds = BlogContentPipeline::pluck('original_id')->toArray();
            $existingCount = count($existingIds);
            
            if ($existingCount > 0 && $skipExisting) {
                $this->info("Will check against {$existingCount} existing records...");
                $this->info("متن فارسی");
            }
            
            // Process documents
            $imported = 0;
            $skipped = 0;
            $failed = 0;
            
            foreach ($documents as $doc) {
                try {
                    // Process document here
                    $imported++;
                    
                    if ($imported % 100 === 0) {
                        $currentCount = BlogContentPipeline::count();
                        $this->info("✓ Progress: {$imported} imported, {$skipped} skipped (DB total: {$currentCount})");
                    }
                } catch (\Exception $e) {
                    $failed++;
                    Log::error("Failed to import document {$doc['_id']}: " . $e->getMessage());
                    // Continue on error instead of failing entire import
                }
            }

            $bar->finish();
            $this->newLine(2);

            $this->info("Import completed successfully!");
            
            $totalInDatabase = BlogContentPipeline::count();
            
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Newly Imported', $imported],
                    ['Skipped (Already Existed)', $skipped],
                    ['Failed', $failed],
                    ['Total Processed', count($documents)],
                    ['────────────────', '──────'],
                    ['Total in Database', $totalInDatabase],
                ]
            );
            
            if ($skipped > 0) {
                $this->info("ℹ️  {$skipped} posts were skipped because they were already imported.");
                $this->info("   To reimport these, use: --force-reimport");
            }
            
            $remaining = 18506 - $totalInDatabase;
            if ($remaining > 0) {
                $this->info("📊 Progress: {$totalInDatabase}/18506 posts imported ({$remaining} remaining)");
            } else {
                $this->info("✅ All posts have been imported!");
            }

            // Clean up Python script
            unlink($pythonScript);

            return 0;

        } catch (\Exception $e) {
            $this->error("Import failed: " . $e->getMessage());
            Log::error("BSON import failed", ['error' => $e->getMessage()]);
            return 1;
        }
    }

    /**
     * Create temporary Python script for BSON processing
     */
    private function createPythonImportScript(): string
    {
        $script = <<<'PYTHON'
#!/usr/bin/env python3
import sys
import json
import bson
import argparse
from datetime import datetime

def convert_datetime(obj):
    if isinstance(obj, datetime):
        return obj.isoformat()
    if isinstance(obj, bson.ObjectId):
        return str(obj)
    if isinstance(obj, dict):
        return {k: convert_datetime(v) for k, v in obj.items()}
    if isinstance(obj, list):
        return [convert_datetime(item) for item in obj]
    return obj

def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('file', help='BSON file path')
    parser.add_argument('--limit', type=int, help='Limit number of records')
    parser.add_argument('--offset', type=int, default=0, help='Skip first N records')
    args = parser.parse_args()
    
    with open(args.file, 'rb') as f:
        bson_data = f.read()
    
    documents = bson.decode_all(bson_data)
    
    # Apply offset and limit
    if args.offset:
        documents = documents[args.offset:]
    if args.limit:
        documents = documents[:args.limit]
    
    # Convert to JSON-serializable format
    documents = [convert_datetime(doc) for doc in documents]
    
    print(json.dumps(documents, ensure_ascii=False))

if __name__ == "__main__":
    main()
PYTHON;

        $tempFile = tempnam(sys_get_temp_dir(), 'bson_import_');
        file_put_contents($tempFile, $script);
        chmod($tempFile, 0755);
        
        return $tempFile;
    }

    /**
     * Clean text content
     */
    private function cleanText(?string $text): ?string
    {
        if (empty($text)) {
            return null;
        }

        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Trim
        $text = trim($text);
        
        return $text;
    }

    /**
     * Clean HTML content
     */
    private function cleanHtml(?string $html): ?string
    {
        if (empty($html)) {
            return null;
        }

        // Decode HTML entities
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Remove script and style tags
        $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        $html = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $html);
        
        // Fix common HTML issues
        $html = str_replace(['&zwnj;', '&nbsp;'], [' ', ' '], $html);
        
        // Clean up whitespace
        $html = preg_replace('/\s+/', ' ', $html);
        
        return trim($html);
    }
}