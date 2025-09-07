<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create a temporary table first to check if we have existing data
        if (Schema::hasTable('tokens_legacy')) {
            // Migrate existing data if legacy table exists
            $legacyTokens = DB::table('tokens_legacy')->get();
            
            foreach ($legacyTokens as $token) {
                DB::table('tokens')->updateOrInsert(
                    ['name' => $token->name],
                    [
                        'provider' => $token->name === 'jibit' ? 'jibit' : 'finnotech',
                        'access_token' => $token->value ?? '',
                        'refresh_token' => $token->value2 ?? '',
                        'expires_at' => now()->addHours(24), // Default expiry
                        'refresh_expires_at' => now()->addHours(48), // Default refresh expiry
                        'is_active' => true,
                        'created_at' => $token->created_at ?? now(),
                        'updated_at' => $token->updated_at ?? now(),
                    ]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration doesn't need to be reversed as it's just a data migration
    }
}; 