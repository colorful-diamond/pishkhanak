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
        Schema::create('telegram_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->index();
            $table->string('user_name');
            $table->string('subject');
            $table->enum('status', ['open', 'waiting_admin', 'waiting_user', 'closed'])->default('open')->index();
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal')->index();
            $table->string('assigned_to')->nullable()->index();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['status', 'updated_at']);
            $table->index('created_at');
        });

        Schema::create('telegram_ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('telegram_tickets')->onDelete('cascade');
            $table->string('user_id')->index();
            $table->text('message');
            $table->boolean('is_admin')->default(false)->index();
            $table->timestamp('created_at');
            
            // Indexes for performance
            $table->index(['ticket_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_ticket_messages');
        Schema::dropIfExists('telegram_tickets');
    }
};
