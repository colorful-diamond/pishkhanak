<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_gateway_id')->constrained()->onDelete('cascade');
            $table->string('type'); // card, bank_account, wallet, etc.
            $table->string('name'); // User given name for this method
            
            // Card details (encrypted/tokenized)
            $table->string('last_four', 4)->nullable(); // Last 4 digits of card
            $table->string('card_type')->nullable(); // visa, mastercard, etc.
            $table->string('expiry_month', 2)->nullable();
            $table->string('expiry_year', 4)->nullable();
            
            // Gateway tokens
            $table->string('gateway_token')->nullable(); // Gateway's token for this method
            $table->json('gateway_data')->nullable(); // Additional gateway specific data
            
            // Status and settings
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'is_active']);
            $table->index(['user_id', 'is_default']);
            $table->index(['payment_gateway_id']);
            $table->index(['type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
}; 