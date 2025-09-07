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
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from_url', 500)->index(); // Source URL
            $table->string('to_url', 500); // Destination URL
            $table->integer('status_code')->default(301); // HTTP status code (301, 302, etc.)
            $table->boolean('is_active')->default(true); // Enable/disable redirect
            $table->boolean('is_exact_match')->default(true); // Exact match or wildcard
            $table->string('description')->nullable(); // Description for admin
            $table->integer('hit_count')->default(0); // Track how many times used
            $table->timestamp('last_hit_at')->nullable(); // Last time redirect was used
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Unique constraint on from_url
            $table->unique('from_url');
            
            // Index for performance
            $table->index(['is_active', 'from_url']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redirects');
    }
};