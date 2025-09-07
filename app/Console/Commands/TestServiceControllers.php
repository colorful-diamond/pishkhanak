<?php

namespace App\Console\Commands;

use App\Http\Controllers\Services\ServiceControllerFactory;
use App\Models\Service;
use App\Models\ServiceResult;
use Illuminate\Console\Command;

class TestServiceControllers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:test {service-slug} {--input=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test a service controller with sample data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $serviceSlug = $this->argument('service-slug');
        $input = $this->option('input');

        $this->info("Testing service controller for: {$serviceSlug}");

        // Find the service
        $service = Service::where('slug', $serviceSlug)->first();

        if (!$service) {
            $this->error("Service with slug '{$serviceSlug}' not found.");
            return 1;
        }

        $this->info("Found service: {$service->title}");

        // Get the controller
        $controller = ServiceControllerFactory::getController($service);

        if (!$controller) {
            $this->error("No controller found for service '{$serviceSlug}'");
            return 1;
        }

        $this->info("Found controller: " . get_class($controller));

        // Generate test input based on service type
        $testInput = $this->generateTestInput($serviceSlug, $input);

        $this->info("Test input: " . json_encode($testInput));

        // Create a mock request
        $request = new \Illuminate\Http\Request();
        $request->merge($testInput);

        try {
            // Call the handle method
            $response = $controller->handle($request, $service);

            $this->info("✅ Controller executed successfully!");

            // Get the latest result
            $latestResult = ServiceResult::where('service_id', $service->id)
                ->latest()
                ->first();

            if ($latestResult) {
                $this->info("Result stored with hash: {$latestResult->result_hash}");
                $this->info("Result URL: " . route('services.result', ['id' => $latestResult->result_hash]));
                
                $this->table(
                    ['Field', 'Value'],
                    [
                        ['Status', $latestResult->status],
                        ['Input Data', json_encode($latestResult->input_data)],
                        ['Output Data', json_encode($latestResult->output_data)],
                        ['Processed At', $latestResult->processed_at->format('Y/m/d H:i:s')],
                    ]
                );
            }

        } catch (\Exception $e) {
            $this->error("❌ Controller failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Generate test input based on service type
     *
     * @param string $serviceSlug
     * @param string|null $customInput
     * @return array
     */
    private function generateTestInput(string $serviceSlug, ?string $customInput): array
    {
        if ($customInput) {
            return ['input' => $customInput];
        }

        // Default test inputs for different service types
        $testInputs = [
            'card-iban' => ['card_number' => '6037991234567890'],
            'card-account' => ['card_number' => '6037991234567890'],
            'iban-account' => ['iban' => 'IR123456789012345678901234'],
            'sheba-account' => ['iban' => 'IR123456789012345678901234'],
            'iban-validator' => ['iban' => 'IR123456789012345678901234'],
            'sheba-validator' => ['iban' => 'IR123456789012345678901234'],
        ];

        return $testInputs[$serviceSlug] ?? ['test_input' => 'sample_data'];
    }
} 