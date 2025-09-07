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
            // Add reference to wallet transaction for confirmation-based payments
            $table->unsignedBigInteger('wallet_transaction_id')->nullable()->after('user_id');
            
            // Add foreign key constraint
            $table->foreign('wallet_transaction_id')->references('id')->on('transactions')->onDelete('set null');
            
            // Add index for better performance
            $table->index('wallet_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_results', function (Blueprint $table) {
            $table->dropForeign(['wallet_transaction_id']);
            $table->dropIndex(['wallet_transaction_id']);
            $table->dropColumn('wallet_transaction_id');
        });
    }
}; 