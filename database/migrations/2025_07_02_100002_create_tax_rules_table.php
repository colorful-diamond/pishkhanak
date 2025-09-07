<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // VAT, Service Tax, etc.
            $table->string('type'); // percentage, fixed
            $table->decimal('rate', 8, 4); // Tax rate (e.g., 9.0000 for 9% or 1000.0000 for 1000 IRT)
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->json('applicable_currencies')->nullable(); // Specific currencies or null for all
            $table->bigInteger('min_amount')->default(0); // Minimum amount to apply tax
            $table->bigInteger('max_amount')->nullable(); // Maximum amount to apply tax
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['is_active', 'is_default']);
            $table->index(['type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_rules');
    }
}; 