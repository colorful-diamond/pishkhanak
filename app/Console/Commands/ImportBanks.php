<?php

namespace App\Console\Commands;

use App\Models\Bank;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportBanks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:banks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import banks from JS file to the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Importing banks...');

        $jsFilePath = resource_path('js/bank-data.js');

        if (!File::exists($jsFilePath)) {
            $this->error('bank-data.js file not found!');
            return 1;
        }

        $jsContent = File::get($jsFilePath);
        
        if (!preg_match('/export const banks = (\[.*?\]);/s', $jsContent, $matches)) {
            $this->error('Could not find or parse the banks array in bank-data.js.');
            return 1;
        }

        $jsonContent = $matches[1];
        $banks = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Failed to decode JSON from bank-data.js: ' . json_last_error_msg());
            return 1;
        }

        foreach ($banks as $bankData) {
            Bank::updateOrCreate(
                ['bank_id' => $bankData['id']],
                [
                    'name' => $bankData['name'],
                    'en_name' => $bankData['en_name'],
                    'logo' => $bankData['logo'],
                    'card_prefixes' => $bankData['card_prefixes'],
                    'color' => $bankData['color'],
                ]
            );
        }

        $this->info('Banks imported successfully!');
        return 0;
    }
} 