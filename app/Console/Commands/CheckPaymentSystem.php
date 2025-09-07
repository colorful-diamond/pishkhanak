<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PaymentGateway;
use App\Models\Currency;
use App\Models\TaxRule;
use Database\Seeders\PaymentSystemSeeder;

class CheckPaymentSystem extends Command
{
    protected $signature = 'payment:check {--fix : Fix missing data by running seeder}';
    protected $description = 'Check payment system setup and optionally fix missing data';

    public function handle()
    {
        $this->info('🔍 Checking Payment System Setup...');
        
        // Check currencies
        $currencies = Currency::count();
        $this->line("📊 Currencies: {$currencies}");
        
        if ($currencies === 0) {
            $this->warn('❌ No currencies found in database');
        } else {
            $this->info('✅ Currencies found');
        }
        
        // Check payment gateways
        $gateways = PaymentGateway::count();
        $activeGateways = PaymentGateway::where('is_active', true)->count();
        $this->line("🏦 Payment Gateways: {$gateways} (Active: {$activeGateways})");
        
        if ($gateways === 0) {
            $this->warn('❌ No payment gateways found in database');
        } else {
            $this->info('✅ Payment gateways found');
            
            // Show gateway details
            PaymentGateway::all()->each(function($gateway) {
                $status = $gateway->is_active ? '🟢' : '🔴';
                $this->line("  {$status} {$gateway->name} ({$gateway->slug}) - Driver: {$gateway->driver}");
            });
        }
        
        // Check tax rules
        $taxRules = TaxRule::count();
        $activeTaxRules = TaxRule::where('is_active', true)->count();
        $this->line("💰 Tax Rules: {$taxRules} (Active: {$activeTaxRules})");
        
        if ($taxRules === 0) {
            $this->warn('❌ No tax rules found in database');
        } else {
            $this->info('✅ Tax rules found');
        }
        
        // Check if any data is missing
        $missingData = ($currencies === 0 || $gateways === 0 || $taxRules === 0);
        
        if ($missingData) {
            $this->error('❌ Payment system is not properly set up!');
            
            if ($this->option('fix')) {
                $this->info('🔧 Fixing missing data...');
                $this->fixMissingData();
            } else {
                $this->line('');
                $this->line('To fix missing data, run:');
                $this->line('  php artisan payment:check --fix');
                $this->line('');
                $this->line('Or manually run the seeder:');
                $this->line('  php artisan db:seed --class=PaymentSystemSeeder');
            }
        } else {
            $this->info('✅ Payment system is properly set up!');
        }
        
        return $missingData ? 1 : 0;
    }
    
    protected function fixMissingData()
    {
        try {
            $seeder = new PaymentSystemSeeder();
            $seeder->run();
            
            $this->info('✅ Payment system data has been created successfully!');
            
            // Show updated counts
            $this->line('');
            $this->line('Updated counts:');
            $this->line("📊 Currencies: " . Currency::count());
            $this->line("🏦 Payment Gateways: " . PaymentGateway::count());
            $this->line("💰 Tax Rules: " . TaxRule::count());
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to fix payment system: ' . $e->getMessage());
            $this->line('');
            $this->line('Please check your database connection and try again.');
        }
    }
} 