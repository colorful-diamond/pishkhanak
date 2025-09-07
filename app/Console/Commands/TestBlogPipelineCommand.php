<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BlogContentPipeline;
use App\Models\BlogProcessingQueue;
use App\Models\BlogPublicationQueue;
use App\Models\BlogPipelineSetting;

class TestBlogPipelineCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:test 
                            {--step= : Test specific step (import|process|publish|all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test blog pipeline system components';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $step = $this->option('step') ?? 'all';
        
        $this->info('🔍 Testing Blog Pipeline System');
        $this->line('════════════════════════════════════════');
        
        try {
            if ($step === 'all' || $step === 'import') {
                $this->testDatabaseTables();
            }
            
            if ($step === 'all' || $step === 'process') {
                $this->testSettings();
            }
            
            if ($step === 'all') {
                $this->testBsonFile();
            }
            
            if ($step === 'all') {
                $this->testPythonDependencies();
            }
            
            $this->newLine();
            $this->info('✅ All tests passed successfully!');
            
        } catch (\Exception $e) {
            $this->error('❌ Test failed: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    /**
     * Test database tables exist
     */
    protected function testDatabaseTables()
    {
        $this->info('Testing database tables...');
        
        $tables = [
            'blog_content_pipeline',
            'blog_processing_queue',
            'blog_publication_queue',
            'blog_processing_logs',
            'blog_pipeline_settings',
        ];
        
        foreach ($tables as $table) {
            if (!\Schema::hasTable($table)) {
                throw new \Exception("Table '{$table}' does not exist. Run: php artisan migrate");
            }
            $this->line("  ✓ Table '{$table}' exists");
        }
    }
    
    /**
     * Test settings are properly configured
     */
    protected function testSettings()
    {
        $this->info('Testing pipeline settings...');
        
        $dailyLimit = BlogPipelineSetting::getDailyPublishLimit();
        $this->line("  ✓ Daily publish limit: {$dailyLimit}");
        
        $aiConfig = BlogPipelineSetting::getAiProcessingConfig();
        $this->line("  ✓ AI model: {$aiConfig['model']}");
        
        $schedule = BlogPipelineSetting::getPublishingSchedule();
        $this->line("  ✓ Publishing enabled: " . ($schedule['enabled'] ? 'Yes' : 'No'));
        
        $thresholds = BlogPipelineSetting::getQualityThresholds();
        $this->line("  ✓ Min quality score: {$thresholds['min_score_for_auto_publish']}");
    }
    
    /**
     * Test BSON file exists and is readable
     */
    protected function testBsonFile()
    {
        $this->info('Testing BSON file...');
        
        $filePath = base_path('../dump/blog/articles.bson');
        
        if (!file_exists($filePath)) {
            throw new \Exception("BSON file not found at: {$filePath}");
        }
        
        $fileSize = filesize($filePath);
        $fileSizeMB = round($fileSize / 1024 / 1024, 2);
        
        $this->line("  ✓ BSON file found: {$filePath}");
        $this->line("  ✓ File size: {$fileSizeMB} MB");
        
        if (!is_readable($filePath)) {
            throw new \Exception("BSON file is not readable");
        }
        
        $this->line("  ✓ File is readable");
    }
    
    /**
     * Test Python dependencies
     */
    protected function testPythonDependencies()
    {
        $this->info('Testing Python dependencies...');
        
        $output = [];
        $returnCode = 0;
        
        exec('python3 -c "import bson; print(\'BSON module available\')" 2>&1', $output, $returnCode);
        
        if ($returnCode !== 0) {
            $this->warn("  ⚠ Python BSON module not installed");
            $this->line("  Run: pip3 install pymongo --user");
        } else {
            $this->line("  ✓ Python BSON module is installed");
        }
        
        exec('python3 --version 2>&1', $output, $returnCode);
        if ($returnCode === 0) {
            $this->line("  ✓ Python is available: " . $output[count($output) - 1]);
        }
    }
}