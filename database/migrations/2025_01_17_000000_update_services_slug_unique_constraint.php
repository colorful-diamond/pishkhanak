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
        Schema::table('services', function (Blueprint $table) {
            // First, drop the existing unique constraint on slug
            $table->dropUnique(['slug']);
            
            // Add a composite unique constraint on parent_id and slug
            // This allows the same slug for different parent services
            $table->unique(['parent_id', 'slug'], 'services_parent_slug_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('services_parent_slug_unique');
            
            // Restore the original unique constraint on slug
            // Note: This might fail if there are duplicate slugs when rolling back
            $table->unique('slug');
        });
    }
}; 