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
        // Create ticket categories table
        Schema::create('ticket_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#3B82F6'); // hex color
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('auto_assign_to')->nullable()->constrained('users')->onDelete('set null');
            $table->json('required_fields')->nullable(); // Fields required when creating ticket in this category
            $table->integer('estimated_response_time')->nullable(); // in minutes
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        // Create ticket priorities table
        Schema::create('ticket_priorities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#10B981'); // hex color
            $table->integer('level')->default(5); // 1-10, higher = more urgent
            $table->boolean('is_active')->default(true);
            $table->integer('auto_escalate_after')->nullable(); // minutes
            $table->foreignId('escalate_to_priority_id')->nullable()->constrained('ticket_priorities')->onDelete('set null');
            $table->integer('sort_order')->default(0);
            $table->string('icon')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'level']);
        });

        // Create ticket statuses table
        Schema::create('ticket_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#3B82F6'); // hex color
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_closed')->default(false);
            $table->boolean('is_resolved')->default(false);
            $table->boolean('requires_user_action')->default(false);
            $table->integer('auto_close_after')->nullable(); // minutes
            $table->integer('sort_order')->default(0);
            $table->json('next_status_options')->nullable(); // Allowed next statuses
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
            $table->index(['is_default']);
        });

        // Create ticket templates table
        Schema::create('ticket_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('subject')->nullable();
            $table->text('content');
            $table->foreignId('category_id')->nullable()->constrained('ticket_categories')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true); // Can be used by all agents
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->json('variables')->nullable(); // Available template variables
            $table->integer('usage_count')->default(0);
            $table->integer('sort_order')->default(0);
            $table->boolean('auto_close_ticket')->default(false);
            $table->foreignId('auto_change_status_to')->nullable()->constrained('ticket_statuses')->onDelete('set null');
            $table->timestamps();

            $table->index(['is_active', 'category_id']);
            $table->index(['is_public']);
        });

        // Create support agents table
        Schema::create('support_agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('agent_code')->unique();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_online')->default(false);
            $table->boolean('auto_assign')->default(true);
            $table->integer('max_tickets')->default(10);
            $table->integer('current_tickets')->default(0);
            $table->json('specialties')->nullable(); // Array of specialty areas
            $table->json('languages')->nullable(); // Array of supported languages
            $table->json('working_hours')->nullable(); // Working schedule
            $table->string('timezone')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->integer('response_time_avg')->nullable(); // in minutes
            $table->integer('resolution_time_avg')->nullable(); // in minutes
            $table->decimal('satisfaction_rating', 3, 2)->nullable();
            $table->integer('total_tickets_handled')->default(0);
            $table->integer('total_tickets_resolved')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'auto_assign']);
            $table->index(['is_online']);
        });

        // Create support agent categories pivot table
        Schema::create('support_agent_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_agent_id')->constrained()->onDelete('cascade');
            $table->foreignId('ticket_category_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['support_agent_id', 'ticket_category_id']);
        });

        // Update existing tickets table to use the new foreign keys
        Schema::table('tickets', function (Blueprint $table) {
            // Add new foreign key columns
            $table->foreignId('category_id')->nullable()->after('category')->constrained('ticket_categories')->onDelete('set null');
            $table->foreignId('priority_id')->nullable()->after('priority')->constrained('ticket_priorities')->onDelete('set null');
            $table->foreignId('status_id')->nullable()->after('status')->constrained('ticket_statuses')->onDelete('set null');
            
            // Add new fields
            $table->json('custom_fields')->nullable()->after('description');
            $table->integer('escalation_count')->default(0)->after('resolution_time');
            $table->timestamp('escalated_at')->nullable()->after('escalation_count');
            $table->foreignId('escalated_from_priority_id')->nullable()->after('escalated_at')->constrained('ticket_priorities')->onDelete('set null');
            $table->timestamp('first_response_at')->nullable()->after('escalated_from_priority_id');
            $table->integer('customer_satisfaction_rating')->nullable()->after('first_response_at'); // 1-5 stars
            $table->text('customer_satisfaction_comment')->nullable()->after('customer_satisfaction_rating');
            $table->json('tags')->nullable()->after('customer_satisfaction_comment');

            // Add indexes
            $table->index(['category_id', 'status_id']);
            $table->index(['priority_id', 'status_id']);
            $table->index(['escalation_count']);
        });

        // Update ticket messages table
        Schema::table('ticket_messages', function (Blueprint $table) {
            // Add new fields
            $table->foreignId('template_id')->nullable()->after('user_id')->constrained('ticket_templates')->onDelete('set null');
            $table->boolean('is_system_message')->default(false)->after('is_internal');
            $table->json('message_data')->nullable()->after('attachments'); // For system messages
            $table->timestamp('read_at')->nullable()->after('message_data');
            $table->string('message_type')->default('text')->after('read_at'); // text, file, system, template
            
            // Add indexes
            $table->index(['is_internal', 'is_system_message']);
            $table->index(['message_type']);
        });

        // Create ticket activity log table
        Schema::create('ticket_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // created, status_changed, assigned, escalated, etc.
            $table->text('description');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->boolean('is_public')->default(true); // Visible to customer
            $table->timestamps();

            $table->index(['ticket_id', 'created_at']);
            $table->index(['action']);
        });

        // Create ticket escalation rules table
        Schema::create('ticket_escalation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->foreignId('category_id')->nullable()->constrained('ticket_categories')->onDelete('cascade');
            $table->foreignId('priority_id')->nullable()->constrained('ticket_priorities')->onDelete('cascade');
            $table->integer('trigger_after_minutes'); // Escalate after X minutes
            $table->string('trigger_condition'); // no_response, no_resolution, customer_waiting
            $table->foreignId('escalate_to_priority_id')->nullable()->constrained('ticket_priorities')->onDelete('set null');
            $table->foreignId('escalate_to_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('send_notification')->default(true);
            $table->text('notification_message')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        // Create ticket SLA settings table
        Schema::create('ticket_sla_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->foreignId('category_id')->nullable()->constrained('ticket_categories')->onDelete('cascade');
            $table->foreignId('priority_id')->nullable()->constrained('ticket_priorities')->onDelete('cascade');
            $table->integer('first_response_time'); // in minutes
            $table->integer('resolution_time'); // in minutes
            $table->json('working_hours')->nullable(); // Business hours definition
            $table->boolean('exclude_weekends')->default(true);
            $table->json('excluded_dates')->nullable(); // Holidays
            $table->timestamps();

            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_sla_settings');
        Schema::dropIfExists('ticket_escalation_rules');
        Schema::dropIfExists('ticket_activities');
        
        Schema::table('ticket_messages', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropColumn([
                'template_id', 'is_system_message', 'message_data', 
                'read_at', 'message_type'
            ]);
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['priority_id']);
            $table->dropForeign(['status_id']);
            $table->dropForeign(['escalated_from_priority_id']);
            $table->dropColumn([
                'category_id', 'priority_id', 'status_id', 'custom_fields',
                'escalation_count', 'escalated_at', 'escalated_from_priority_id',
                'first_response_at', 'customer_satisfaction_rating',
                'customer_satisfaction_comment', 'tags'
            ]);
        });

        Schema::dropIfExists('support_agent_categories');
        Schema::dropIfExists('support_agents');
        Schema::dropIfExists('ticket_templates');
        Schema::dropIfExists('ticket_statuses');
        Schema::dropIfExists('ticket_priorities');
        Schema::dropIfExists('ticket_categories');
    }
}; 