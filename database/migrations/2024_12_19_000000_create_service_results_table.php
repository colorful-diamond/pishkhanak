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
        Schema::create('service_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('result_hash', 16)->unique();
            $table->json('input_data')->nullable();
            $table->json('output_data')->nullable();
            $table->enum('status', ['success', 'failed', 'processing'])->default('processing');
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->useCurrent();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['result_hash']);
            $table->index(['service_id', 'processed_at']);
            $table->index(['status', 'processed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_results');
    }
}; 