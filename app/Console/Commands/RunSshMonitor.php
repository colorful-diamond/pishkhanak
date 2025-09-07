<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RunSshMonitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ssh:monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run SSH monitor script to check and maintain SSH connections';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $scriptPath = '/home/pishkhanak/htdocs/simple_ssh_monitor.sh';
        
        if (!file_exists($scriptPath)) {
            $this->error("SSH monitor script not found at: $scriptPath");
            Log::error("SSH monitor script not found at: $scriptPath");
            return 1;
        }
        
        // Make sure the script is executable
        if (!is_executable($scriptPath)) {
            exec("chmod +x $scriptPath");
        }
        
        $this->info('Running SSH monitor...');
        
        // Execute the script
        $output = [];
        $returnCode = 0;
        exec("bash $scriptPath 2>&1", $output, $returnCode);
        
        if ($returnCode === 0) {
            $this->info('SSH monitor completed successfully');
            if (!empty($output)) {
                $this->line(implode("\n", $output));
                Log::info('SSH monitor output', ['output' => implode("\n", $output)]);
            }
        } else {
            $this->error('SSH monitor failed with code: ' . $returnCode);
            Log::error('SSH monitor failed', [
                'return_code' => $returnCode,
                'output' => implode("\n", $output)
            ]);
        }
        
        return $returnCode;
    }
}