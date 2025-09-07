<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tokens', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // 'jibit', 'finnotech'
            $table->string('provider'); // 'jibit', 'finnotech'
            $table->text('access_token');
            $table->text('refresh_token');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('refresh_expires_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->json('metadata')->nullable(); // Additional token metadata
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['provider', 'is_active']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tokens');
    }
}; 