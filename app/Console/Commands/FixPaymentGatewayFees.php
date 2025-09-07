<?php

namespace App\Console\Commands;

use App\Models\PaymentGateway;
use Illuminate\Console\Command;

class FixPaymentGatewayFees extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'payment:fix-fees {--reset-all : Reset all gateway fees to 0%}';

    /**
     * The console command description.
     */
    protected $description = 'Fix payment gateway fee configurations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking payment gateway fees...');

        $gateways = PaymentGateway::all();
        
        if ($gateways->isEmpty()) {
            $this->error('❌ No payment gateways found. Run php artisan payment:seed-gateways first.');
            return Command::FAILURE;
        }

        $this->table(
            ['ID', 'Name', 'Fee %', 'Fee Fixed', 'Fee for 100,000'],
            $gateways->map(function ($gateway) {
                return [
                    $gateway->id,
                    $gateway->name,
                    $gateway->fee_percentage . '%',
                    number_format($gateway->fee_fixed) . ' IRT',
                    number_format($gateway->calculateFee(100000)) . ' IRT'
                ];
            })
        );

        // Check for problematic fees
        $highFeeGateways = $gateways->filter(function ($gateway) {
            return $gateway->fee_percentage > 5.0 || $gateway->calculateFee(100000) > 5000;
        });

        if ($highFeeGateways->isNotEmpty()) {
            $this->warn('⚠️  Found gateways with high fees:');
            foreach ($highFeeGateways as $gateway) {
                $this->warn("   - {$gateway->name}: {$gateway->fee_percentage}% + {$gateway->fee_fixed} IRT");
            }
        }

        if ($this->option('reset-all')) {
            $this->info('🔧 Resetting all gateway fees to 0%...');
            
            foreach ($gateways as $gateway) {
                $gateway->update([
                    'fee_percentage' => 0.0,
                    'fee_fixed' => 0
                ]);
                $this->info("   ✅ Reset fees for {$gateway->name}");
            }
            
            $this->info('🎉 All gateway fees reset to 0%');
        } else {
            // Apply recommended fee structure
            $this->info('🔧 Applying recommended fee structure...');
            
            $feeStructure = [
                'sepehr' => ['percentage' => 0.0, 'fixed' => 0],
                'jibit' => ['percentage' => 0.0, 'fixed' => 0],
                'asanpardakht' => ['percentage' => 1.5, 'fixed' => 0], // Reasonable merchant fee
            ];

            foreach ($gateways as $gateway) {
                if (isset($feeStructure[$gateway->slug])) {
                    $fees = $feeStructure[$gateway->slug];
                    $gateway->update([
                        'fee_percentage' => $fees['percentage'],
                        'fee_fixed' => $fees['fixed']
                    ]);
                    $this->info("   ✅ Updated {$gateway->name}: {$fees['percentage']}% + {$fees['fixed']} IRT");
                } else {
                    // Reset unknown gateways to 0
                    $gateway->update([
                        'fee_percentage' => 0.0,
                        'fee_fixed' => 0
                    ]);
                    $this->info("   ✅ Reset {$gateway->name} to 0%");
                }
            }
        }

        $this->newLine();
        $this->info('🔍 Updated fee structure:');
        
        $updatedGateways = PaymentGateway::all();
        $this->table(
            ['ID', 'Name', 'Fee %', 'Fee Fixed', 'Fee for 100,000'],
            $updatedGateways->map(function ($gateway) {
                return [
                    $gateway->id,
                    $gateway->name,
                    $gateway->fee_percentage . '%',
                    number_format($gateway->fee_fixed) . ' IRT',
                    number_format($gateway->calculateFee(100000)) . ' IRT'
                ];
            })
        );

        $this->info('💡 Notes:');
        $this->info('   - Gateway fees are typically handled by the payment provider');
        $this->info('   - For wallet charges, fees should usually be 0%');
        $this->info('   - High fees (>5%) can confuse users and inflate payment amounts');

        return Command::SUCCESS;
    }
} 