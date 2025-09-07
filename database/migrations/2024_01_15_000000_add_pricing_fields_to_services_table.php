<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('is_paid')->default(false)->after('parent_id');
            $table->integer('cost')->default(0)->after('is_paid');
            $table->string('currency', 3)->default('IRT')->after('cost');
        });

        // Convert existing price column from decimal to integer
        Schema::table('services', function (Blueprint $table) {
            // First, add a temporary column
            $table->integer('price_new')->default(0)->after('price');
        });

        // Update the temporary column with converted values
        DB::statement('UPDATE services SET price_new = COALESCE(price * 100, 0)');

        // Drop the old price column and rename the new one
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('price');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->renameColumn('price_new', 'price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert price back to decimal
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('price_decimal', 10, 2)->nullable()->after('price');
        });

        // Update the decimal column with converted values
        DB::statement('UPDATE services SET price_decimal = price / 100.0');

        // Drop the integer price column and rename the decimal one
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('price');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->renameColumn('price_decimal', 'price');
        });

        // Drop the other new columns
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['is_paid', 'cost', 'currency']);
        });
    }
}; 