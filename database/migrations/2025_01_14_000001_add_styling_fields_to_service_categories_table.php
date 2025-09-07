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
        Schema::table('service_categories', function (Blueprint $table) {
            $table->string('background_color')->default('#f0f9ff'); // Default sky-50
            $table->string('border_color')->default('#bbf7d0'); // Default green-200
            $table->string('icon_color')->default('#10b981'); // Default emerald-500
            $table->string('hover_border_color')->default('#4ade80'); // Default green-400
            $table->string('hover_background_color')->default('#f0fdf4'); // Default green-50
            $table->text('background_icon')->nullable(); // SVG icon for background
            $table->integer('display_order')->default(0); // For ordering categories
            $table->boolean('is_active')->default(true); // To show/hide categories
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_categories', function (Blueprint $table) {
            $table->dropColumn([
                'background_color',
                'border_color', 
                'icon_color',
                'hover_border_color',
                'hover_background_color',
                'background_icon',
                'display_order',
                'is_active'
            ]);
        });
    }
}; 