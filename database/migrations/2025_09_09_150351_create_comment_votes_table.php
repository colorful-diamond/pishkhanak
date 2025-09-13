<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comment_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained('service_comments')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('ip_address')->nullable(); // For guest votes
            $table->enum('vote_type', ['helpful', 'unhelpful']);
            $table->timestamps();
            
            // Ensure one vote per user/IP per comment
            $table->unique(['comment_id', 'user_id']);
            $table->unique(['comment_id', 'ip_address']);
            
            // Indexes
            $table->index(['comment_id', 'vote_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_votes');
    }
};