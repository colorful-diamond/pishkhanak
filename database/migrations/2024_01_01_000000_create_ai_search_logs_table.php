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
        Schema::create('ai_search_logs', function (Blueprint $table) {
            $table->id();
            $table->text('query');
            $table->enum('type', ['text', 'voice', 'image', 'conversational'])->default('text');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('session_id')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->integer('results_count')->default(0);
            $table->string('intent')->nullable();
            $table->decimal('confidence', 3, 2)->nullable();
            $table->boolean('cached')->default(false);
            $table->integer('response_time_ms')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['ip_address', 'created_at']);
            $table->index(['type', 'created_at']);
            $table->index(['intent', 'created_at']);
            $table->index('session_id');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_search_logs');
    }
}; 