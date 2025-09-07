<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gateway_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique(); // Public transaction identifier
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_gateway_id')->constrained()->onDelete('restrict');
            $table->foreignId('currency_id')->constrained()->onDelete('restrict');
            
            // Transaction amounts (stored in smallest currency unit - cents, rials, etc.)
            $table->bigInteger('amount'); // Original amount
            $table->bigInteger('tax_amount')->default(0); // Tax calculated
            $table->bigInteger('gateway_fee')->default(0); // Gateway fee
            $table->bigInteger('total_amount'); // Final amount (amount + tax + fee)
            
            // Gateway related
            $table->string('gateway_transaction_id')->nullable(); // Gateway's transaction ID
            $table->string('gateway_reference')->nullable(); // Gateway reference number
            $table->json('gateway_response')->nullable(); // Raw gateway response
            
            // Transaction details
            $table->string('type')->default('payment'); // payment, refund, etc.
            $table->string('status'); // pending, processing, completed, failed, cancelled, refunded
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Additional data
            
            // User information
            $table->string('user_ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('user_country', 2)->nullable();
            $table->string('user_device')->nullable();
            
            // Timestamps
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['payment_gateway_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index(['gateway_transaction_id']);
            $table->index(['uuid']);
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gateway_transactions');
    }
}; 