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
        Schema::table('service_results', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('service_id')->constrained()->onDelete('cascade');
            
            // Add index for better performance on user queries
            $table->index(['user_id', 'processed_at']);
            $table->index(['user_id', 'service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_results', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id', 'processed_at']);
            $table->dropIndex(['user_id', 'service_id']);
            $table->dropColumn('user_id');
        });
    }
}; 