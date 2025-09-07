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
        Schema::table('service_requests', function (Blueprint $table) {
            // Add reference to wallet transaction for confirmation-based payments
            $table->unsignedBigInteger('wallet_transaction_id')->nullable()->after('payment_transaction_id');
            
            // Add error message field for failed requests
            $table->text('error_message')->nullable()->after('status');
            
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
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropForeign(['wallet_transaction_id']);
            $table->dropIndex(['wallet_transaction_id']);
            $table->dropColumn(['wallet_transaction_id', 'error_message']);
        });
    }
}; 