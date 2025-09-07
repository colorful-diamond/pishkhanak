<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AddPermissionsToResources extends Command
{
    protected $signature = 'filament:add-permissions';
    protected $description = 'Add permission checks to all Filament resources';

    public function handle()
    {
        $resourcesPath = app_path('Filament/Resources');
        $resourceFiles = File::glob($resourcesPath . '/*Resource.php');

        $this->info('Adding permission trait to Filament resources...');

        foreach ($resourceFiles as $file) {
            $this->processResourceFile($file);
        }

        $this->info('✅ Completed adding permissions to resources');
    }

    private function processResourceFile(string $filePath): void
    {
        $filename = basename($filePath);
        $content = File::get($filePath);

        // Check if trait is already added
        if (str_contains($content, 'use App\Traits\HasResourcePermissions;') || 
            str_contains($content, 'use HasResourcePermissions')) {
            $this->line("⏭️  Skipping {$filename} - already has permissions");
            return;
        }

        // Add the use statement
        if (!str_contains($content, 'use App\Traits\HasResourcePermissions;')) {
            $content = str_replace(
                'use Filament\Resources\Resource;',
                "use Filament\Resources\Resource;\nuse App\Traits\HasResourcePermissions;",
                $content
            );
        }

        // Add the trait to the class
        $pattern = '/class\s+\w+Resource\s+extends\s+Resource\s*\{([^}]*?)protected\s+static/';
        $replacement = function ($matches) {
            $classContent = $matches[1];
            if (!str_contains($classContent, 'use HasResourcePermissions')) {
                return str_replace(
                    $matches[0],
                    str_replace('{', "{\n    use HasResourcePermissions;\n", $matches[0]),
                    $matches[0]
                );
            }
            return $matches[0];
        };

        $newContent = preg_replace_callback($pattern, $replacement, $content);
        
        if ($newContent && $newContent !== $content) {
            File::put($filePath, $newContent);
            $this->info("✅ Updated {$filename}");
        } else {
            $this->line("⏭️  No changes needed for {$filename}");
        }
    }
}