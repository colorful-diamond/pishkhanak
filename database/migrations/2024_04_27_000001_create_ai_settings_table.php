<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('model_config')->nullable();
            $table->json('generation_settings')->nullable();
            $table->json('prompt_templates')->nullable();
            $table->json('language_settings')->nullable();
            $table->json('tone_settings')->nullable();
            $table->json('content_formats')->nullable();
            $table->json('target_audiences')->nullable();
            $table->json('custom_instructions')->nullable();
            $table->integer('max_tokens')->default(2048);
            $table->float('temperature')->default(0.7);
            $table->float('frequency_penalty')->default(0.0);
            $table->float('presence_penalty')->default(0.0);
            $table->json('stop_sequences')->nullable();
            $table->integer('ordering')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_settings');
    }
};
