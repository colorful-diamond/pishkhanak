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
        // Table for auto-response contexts/categories
        Schema::create('auto_response_contexts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Name of the context/category');
            $table->text('description')->nullable()->comment('Description of what this context covers');
            $table->text('keywords')->nullable()->comment('Keywords that help identify this context');
            $table->text('example_queries')->nullable()->comment('Example user queries for this context');
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0)->comment('Higher priority contexts are checked first');
            $table->float('confidence_threshold')->default(0.7)->comment('Minimum confidence score for matching (0-1)');
            $table->timestamps();
            
            $table->index('is_active');
            $table->index('priority');
        });

        // Table for auto-responses linked to contexts
        Schema::create('auto_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('context_id')->constrained('auto_response_contexts')->onDelete('cascade');
            $table->string('title')->comment('Title of the response');
            $table->text('response_text')->comment('The actual response to send');
            $table->json('attachments')->nullable()->comment('File paths of attachments to include');
            $table->json('links')->nullable()->comment('Helpful links to include');
            $table->boolean('is_active')->default(true);
            $table->boolean('mark_as_resolved')->default(false)->comment('Should ticket be marked as resolved after this response');
            $table->string('language')->default('fa')->comment('Language of the response (fa/en)');
            $table->integer('usage_count')->default(0)->comment('How many times this response has been used');
            $table->float('satisfaction_score')->nullable()->comment('Average satisfaction score for this response');
            $table->timestamps();
            
            $table->index(['context_id', 'is_active']);
            $table->index('language');
        });

        // Table to track auto-response usage and effectiveness
        Schema::create('auto_response_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('context_id')->nullable()->constrained('auto_response_contexts')->onDelete('set null');
            $table->foreignId('response_id')->nullable()->constrained('auto_responses')->onDelete('set null');
            $table->text('user_query')->comment('The original user query');
            $table->json('ai_analysis')->nullable()->comment('AI analysis results including confidence scores');
            $table->float('confidence_score')->nullable()->comment('Confidence score of the match');
            $table->boolean('was_helpful')->nullable()->comment('User feedback on whether response was helpful');
            $table->text('user_feedback')->nullable()->comment('Additional user feedback');
            $table->boolean('escalated_to_support')->default(false)->comment('Whether ticket was escalated to support');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
            
            $table->index('ticket_id');
            $table->index('was_helpful');
            $table->index('escalated_to_support');
        });

        // Add auto_response fields to tickets table
        Schema::table('tickets', function (Blueprint $table) {
            $table->boolean('is_auto_responded')->default(false)->after('status');
            $table->foreignId('auto_response_id')->nullable()->after('is_auto_responded')
                ->constrained('auto_responses')->onDelete('set null');
            $table->timestamp('auto_responded_at')->nullable()->after('auto_response_id');
            
            $table->index('is_auto_responded');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['auto_response_id']);
            $table->dropColumn(['is_auto_responded', 'auto_response_id', 'auto_responded_at']);
        });
        
        Schema::dropIfExists('auto_response_logs');
        Schema::dropIfExists('auto_responses');
        Schema::dropIfExists('auto_response_contexts');
    }
};
