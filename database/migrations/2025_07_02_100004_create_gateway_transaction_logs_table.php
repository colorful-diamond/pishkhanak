<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gateway_transaction_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gateway_transaction_id')->constrained()->onDelete('cascade');
            $table->string('action'); // created, processing, gateway_called, webhook_received, completed, failed, etc.
            $table->string('source'); // web, api, webhook, cron, admin
            $table->text('message')->nullable(); // Human readable message
            $table->json('data')->nullable(); // Additional data for this log entry
            $table->json('request_data')->nullable(); // Request data if applicable
            $table->json('response_data')->nullable(); // Response data if applicable
            
            // Request details
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('method', 10)->nullable(); // GET, POST, etc.
            $table->string('url')->nullable();
            $table->json('headers')->nullable();
            
            // Error tracking
            $table->string('error_code')->nullable();
            $table->text('error_message')->nullable();
            $table->text('stack_trace')->nullable();
            
            // Performance tracking
            $table->integer('response_time_ms')->nullable(); // Response time in milliseconds
            $table->integer('memory_usage_mb')->nullable(); // Memory usage in MB
            
            $table->timestamps();
            
            // Indexes
            $table->index(['gateway_transaction_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['source']);
            $table->index(['ip_address']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gateway_transaction_logs');
    }
}; 