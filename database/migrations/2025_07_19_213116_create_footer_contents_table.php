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
        Schema::create('footer_contents', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // company_name, description, address, phone, email, etc.
            $table->text('value');
            $table->string('type')->default('text'); // text, html, image, json
            $table->string('section')->default('general'); // general, contact, social, legal
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // Additional settings
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footer_contents');
    }
}; 