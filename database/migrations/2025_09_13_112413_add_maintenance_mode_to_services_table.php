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
            $table->boolean('is_maintenance')->default(false)->comment('Whether the service is in maintenance mode');
            $table->text('maintenance_message')->nullable()->comment('Custom error message to show during maintenance');
            $table->timestamp('maintenance_started_at')->nullable()->comment('When maintenance mode was enabled');
            $table->timestamp('maintenance_ends_at')->nullable()->comment('Expected end time for maintenance');
            $table->boolean('maintenance_affects_children')->default(true)->comment('Whether maintenance mode affects sub-services');
            $table->index('is_maintenance');
            $table->index('maintenance_ends_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'is_maintenance',
                'maintenance_message',
                'maintenance_started_at',
                'maintenance_ends_at',
                'maintenance_affects_children'
            ]);
        });
    }
};