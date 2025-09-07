<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique(); // USD, EUR, IRT
            $table->string('name'); // US Dollar, Euro, Iranian Rial
            $table->string('symbol', 10); // $, €, ﷼
            $table->decimal('exchange_rate', 20, 8)->default(1.0000); // Rate to base currency
            $table->boolean('is_base_currency')->default(false); // Only one can be true
            $table->boolean('is_active')->default(true);
            $table->integer('decimal_places')->default(2); // 2 for USD, 0 for IRT
            $table->string('position', 10)->default('before'); // before or after amount
            $table->timestamps();
            
            $table->index(['is_active', 'is_base_currency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
}; 