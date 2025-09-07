<?php

namespace App\Console\Commands;

use App\Http\Controllers\Services\ServiceControllerFactory;
use Illuminate\Console\Command;

class ListServiceControllers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:list-controllers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all available service controllers and their mappings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Available Service Controllers:');
        $this->newLine();

        $controllers = ServiceControllerFactory::getAvailableControllers();
        
        if (empty($controllers)) {
            $this->warn('No service controllers found.');
            return;
        }

        $tableData = [];
        foreach ($controllers as $slug) {
            $tableData[] = [
                'Slug' => $slug,
                'Controller' => $this->getControllerClass($slug),
                'Status' => $this->getControllerStatus($slug),
            ];
        }

        $this->table(['Slug', 'Controller', 'Status'], $tableData);
        
        $this->newLine();
        $this->info('To add a new service controller:');
        $this->line('1. Create a new controller in app/Http/Controllers/Services/');
        $this->line('2. Implement the BaseServiceController interface');
        $this->line('3. Add the mapping to ServiceControllerFactory');
        $this->line('4. Create the corresponding view');
    }

    /**
     * Get the controller class name for a slug
     *
     * @param string $slug
     * @return string
     */
    private function getControllerClass(string $slug): string
    {
        $reflection = new \ReflectionClass(ServiceControllerFactory::class);
        $property = $reflection->getProperty('serviceMapping');
        $property->setAccessible(true);
        $mapping = $property->getValue();
        
        return $mapping[$slug] ?? 'Not Found';
    }

    /**
     * Get the status of a controller
     *
     * @param string $slug
     * @return string
     */
    private function getControllerStatus(string $slug): string
    {
        $controllerClass = $this->getControllerClass($slug);
        
        if ($controllerClass === 'Not Found') {
            return '❌ Not Mapped';
        }
        
        if (class_exists($controllerClass)) {
            return '✅ Active';
        }
        
        return '❌ Class Not Found';
    }
} 