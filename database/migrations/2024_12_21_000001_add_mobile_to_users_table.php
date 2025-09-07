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
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile', 15)->nullable()->unique()->after('email');
            $table->timestamp('mobile_verified_at')->nullable()->after('email_verified_at');
            
            // Add index for mobile searches
            $table->index(['mobile']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['mobile']);
            $table->dropColumn(['mobile', 'mobile_verified_at']);
        });
    }
}; 