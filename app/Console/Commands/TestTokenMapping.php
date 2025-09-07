<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FinnotechTokenMapper;

class TestTokenMapping extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'finnotech:test-tokens {service?} {--all}';

    /**
     * The console command description.
     */
    protected $description = 'Test and verify which tokens are used for Finnotech services';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $service = $this->argument('service');
        $showAll = $this->option('all');

        if ($showAll) {
            $this->showAllMappings();
            return;
        }

        if ($service) {
            $this->testSpecificService($service);
            return;
        }

        // Test common services
        $this->testCommonServices();
    }

    private function testSpecificService(string $service)
    {
        $endpoints = $this->getServiceEndpoints();
        
        if (!isset($endpoints[$service])) {
            $this->error("Service '{$service}' not found!");
            $this->info("Available services: " . implode(', ', array_keys($endpoints)));
            return;
        }

        $endpoint = $endpoints[$service];
        $analysis = FinnotechTokenMapper::analyzeEndpoint($endpoint);

        $this->info("=== Token Analysis for '{$service}' ===");
        $this->table(
            ['Property', 'Value'],
            [
                ['Service', $service],
                ['Endpoint', $analysis['endpoint']],
                ['Token Category', $analysis['token_category']],
                ['Token Label', $analysis['token_label']],
                ['Matched By', $analysis['matched_by']],
                ['Matched Pattern', $analysis['matched_pattern'] ?? 'N/A'],
                ['Is Category Token', $analysis['is_category_token'] ? 'Yes' : 'No'],
            ]
        );
    }

    private function testCommonServices()
    {
        $this->info("=== Common Service Token Mappings ===");

        $commonServices = [
            'loan-inquiry' => '/credit/v2/clients/pishkhanak/users/1234567890/sms/facilityInquiry',
            'iban-inquiry' => '/oak/v2/clients/pishkhanak/ibanInquiry',
            'card-information' => '/card/v2/clients/pishkhanak/cards/1234567890123456',
            'cheque-inquiry' => '/credit/v2/clients/pishkhanak/chequeColorInquiry',
            'kyc-verification' => '/kyc/v2/clients/pishkhanak/identification',
            'vehicle-violations' => '/billing/v2/clients/pishkhanak/drivingOffenseInquiry',
            'insurance-inquiry' => '/kyc/v2/clients/pishkhanak/thirdPartyInsuranceInquiry',
            'sms-service' => '/facility/v2/clients/pishkhanak/finnotext',
        ];

        $results = [];
        foreach ($commonServices as $service => $endpoint) {
            $analysis = FinnotechTokenMapper::analyzeEndpoint($endpoint);
            $results[] = [
                $service,
                $analysis['token_category'],
                $analysis['token_label'],
                $analysis['matched_by'],
            ];
        }

        $this->table(
            ['Service', 'Token Category', 'Token Label', 'Matched By'],
            $results
        );
    }

    private function showAllMappings()
    {
        $this->info("=== All Token Category Mappings ===");

        $categories = FinnotechTokenMapper::getAllTokenCategories();
        
        foreach ($categories as $tokenName) {
            $patterns = FinnotechTokenMapper::getServicePatternsForToken($tokenName);
            $label = FinnotechTokenMapper::getTokenCategoryLabels()[$tokenName] ?? $tokenName;
            
            $this->info("\n🔹 {$label} ({$tokenName}):");
            
            if (isset($patterns['service_patterns'])) {
                $this->line("  Service Patterns:");
                foreach ($patterns['service_patterns'] as $pattern) {
                    $this->line("    - {$pattern}");
                }
            }
            
            if (isset($patterns['url_patterns'])) {
                $this->line("  URL Patterns:");
                foreach ($patterns['url_patterns'] as $pattern) {
                    $this->line("    - {$pattern}");
                }
            }
        }
    }

    private function getServiceEndpoints(): array
    {
        return [
            'loan-inquiry' => '/credit/v2/clients/pishkhanak/users/1234567890/sms/facilityInquiry',
            'loan-guarantee' => '/credit/v2/clients/pishkhanak/guaranteeDetails',
            'iban-inquiry' => '/oak/v2/clients/pishkhanak/ibanInquiry',
            'card-information' => '/card/v2/clients/pishkhanak/cards/1234567890123456',
            'card-to-iban' => '/facility/v2/clients/pishkhanak/cardToIban',
            'cheque-inquiry' => '/credit/v2/clients/pishkhanak/chequeColorInquiry',
            'cheque-guarantee' => '/credit/v2/clients/pishkhanak/guaranteeDetails',
            'kyc-verification' => '/kyc/v2/clients/pishkhanak/identification',
            'kyc-shahkar' => '/kyc/v2/clients/pishkhanak/smsShahkarSend',
            'military-inquiry' => '/kyc/v2/clients/pishkhanak/militaryInquiry',
            'vehicle-violations' => '/billing/v2/clients/pishkhanak/drivingOffenseInquiry',
            'vehicle-info' => '/vehicle/v2/clients/pishkhanak/vehicleInfo',
            'insurance-inquiry' => '/kyc/v2/clients/pishkhanak/thirdPartyInsuranceInquiry',
            'travel-insurance' => '/insurance/v2/clients/pishkhanak/issueTravelInsurance',
            'sms-service' => '/facility/v2/clients/pishkhanak/finnotext',
            'promissory-publish' => '/promissory/v2/clients/pishkhanak/publishRequest',
        ];
    }
}