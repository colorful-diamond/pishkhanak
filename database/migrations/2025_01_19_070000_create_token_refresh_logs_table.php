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
        Schema::create('token_refresh_logs', function (Blueprint $table) {
            $table->id();
            $table->string('provider'); // jibit, finnotech
            $table->string('token_name'); // jibit, fino
            $table->enum('status', ['success', 'failed', 'skipped']);
            $table->enum('trigger_type', ['automatic', 'manual', 'forced'])->default('automatic');
            $table->text('message')->nullable(); // Success/error message
            $table->json('metadata')->nullable(); // Additional data (old expiry, new expiry, etc.)
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration_ms')->nullable(); // Duration in milliseconds
            $table->string('error_code')->nullable(); // Error code if failed
            $table->text('error_details')->nullable(); // Detailed error info
            $table->timestamps();

            // Indexes for efficient querying
            $table->index(['provider', 'status']);
            $table->index(['status', 'trigger_type']);
            $table->index('started_at');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('token_refresh_logs');
    }
}; 