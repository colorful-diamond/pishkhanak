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
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->string('mobile', 15)->index();
            $table->string('code', 6);
            $table->enum('type', ['login', 'register', 'password_reset'])->default('login');
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->boolean('is_used')->default(false);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->integer('attempts')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['mobile', 'type', 'is_used']);
            $table->index(['expires_at']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
}; 