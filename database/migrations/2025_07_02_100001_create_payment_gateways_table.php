<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Asanpardakht, Saman, etc.
            $table->string('slug')->unique(); // asanpardakht, saman
            $table->string('driver'); // Class name for gateway implementation
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->json('config'); // Gateway specific configuration
            $table->json('supported_currencies')->nullable(); // Array of currency codes
            $table->decimal('fee_percentage', 5, 2)->default(0); // Gateway fee %
            $table->bigInteger('fee_fixed')->default(0); // Fixed fee in smallest currency unit
            $table->integer('min_amount')->default(0); // Minimum transaction amount
            $table->integer('max_amount')->nullable(); // Maximum transaction amount
            $table->string('logo_url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['is_active', 'is_default']);
            $table->index(['slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
}; 