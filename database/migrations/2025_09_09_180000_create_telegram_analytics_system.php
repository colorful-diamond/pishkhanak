<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // User Interaction Events Table
        Schema::create('telegram_user_events', function (Blueprint $table) {
            $table->id();
            $table->string('telegram_user_id', 20);
            $table->string('event_type', 50); // command, message, callback, inline_query
            $table->string('event_action', 100); // specific command or action
            $table->json('event_data')->nullable(); // additional event parameters
            $table->string('session_id', 64)->nullable();
            $table->string('message_id', 20)->nullable();
            $table->integer('processing_time_ms')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->boolean('success')->default(true);
            $table->string('error_code', 50)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('language_code', 10)->default('fa');
            $table->timestamp('created_at');
            
            $table->index(['telegram_user_id', 'created_at']);
            $table->index(['event_type', 'event_action']);
            $table->index(['session_id']);
            $table->index(['success', 'error_code']);
        });

        // User Sessions Table
        Schema::create('telegram_user_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64)->unique();
            $table->string('telegram_user_id', 20);
            $table->timestamp('started_at');
            $table->timestamp('last_activity_at');
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->integer('total_events')->default(0);
            $table->integer('successful_events')->default(0);
            $table->string('entry_point', 100)->nullable(); // first command/action
            $table->string('exit_point', 100)->nullable(); // last command/action
            $table->json('conversation_flow')->nullable();
            $table->boolean('goal_achieved')->default(false);
            $table->string('goal_type', 50)->nullable(); // ticket_created, payment_completed, etc.
            
            $table->index(['telegram_user_id', 'started_at']);
            $table->index(['goal_achieved', 'goal_type']);
        });

        // Service Usage Analytics Table
        Schema::create('telegram_service_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('telegram_user_id', 20);
            $table->unsignedBigInteger('service_id');
            $table->string('action_type', 50); // view, start, complete, abandon
            $table->integer('step_number')->nullable();
            $table->string('step_name', 100)->nullable();
            $table->integer('time_spent_seconds')->nullable();
            $table->json('input_data')->nullable();
            $table->json('output_data')->nullable();
            $table->boolean('success')->default(true);
            $table->string('error_reason', 255)->nullable();
            $table->decimal('amount', 20, 8)->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->timestamp('created_at');
            
            $table->index(['service_id', 'action_type']);
            $table->index(['telegram_user_id', 'created_at']);
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });

        // Command Performance Metrics Table
        Schema::create('telegram_command_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('command_name', 100);
            $table->integer('execution_count')->default(1);
            $table->integer('success_count')->default(0);
            $table->integer('error_count')->default(0);
            $table->decimal('avg_processing_time_ms', 10, 2)->nullable();
            $table->decimal('avg_response_time_ms', 10, 2)->nullable();
            $table->decimal('success_rate', 5, 4)->default(0); // percentage
            $table->integer('unique_users_count')->default(0);
            $table->json('error_breakdown')->nullable(); // error_code => count
            $table->json('usage_patterns')->nullable(); // hourly/daily patterns
            $table->date('metric_date');
            $table->timestamps();
            
            $table->unique(['command_name', 'metric_date']);
            $table->index(['metric_date']);
        });

        // User Behavior Analytics Table
        Schema::create('telegram_user_behavior', function (Blueprint $table) {
            $table->id();
            $table->string('telegram_user_id', 20);
            $table->integer('total_sessions')->default(0);
            $table->integer('total_commands')->default(0);
            $table->decimal('avg_session_duration', 10, 2)->default(0);
            $table->timestamp('first_interaction_at');
            $table->timestamp('last_interaction_at');
            $table->integer('days_active')->default(1);
            $table->string('preferred_language', 10)->default('fa');
            $table->json('favorite_services')->nullable(); // service_id => usage_count
            $table->json('command_preferences')->nullable(); // command => usage_count
            $table->decimal('total_spent', 20, 8)->default(0);
            $table->integer('successful_transactions')->default(0);
            $table->string('user_segment', 50)->nullable(); // new, active, vip, dormant
            $table->decimal('engagement_score', 5, 2)->default(0);
            $table->timestamp('calculated_at');
            
            $table->unique(['telegram_user_id']);
            $table->index(['user_segment']);
            $table->index(['engagement_score']);
            $table->index(['last_interaction_at']);
        });

        // Payment Flow Analytics Table
        Schema::create('telegram_payment_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('telegram_user_id', 20);
            $table->string('transaction_id', 100)->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->string('payment_gateway', 50);
            $table->string('flow_step', 50); // initiated, redirect, callback, completed, failed
            $table->decimal('amount', 20, 8);
            $table->string('currency', 10)->default('IRR');
            $table->integer('step_duration_seconds')->nullable();
            $table->string('error_code', 50)->nullable();
            $table->string('error_message', 255)->nullable();
            $table->json('gateway_response')->nullable();
            $table->string('user_agent_hash', 64)->nullable();
            $table->timestamp('created_at');
            
            $table->index(['telegram_user_id', 'flow_step']);
            $table->index(['payment_gateway', 'flow_step']);
            $table->index(['created_at']);
            $table->foreign('service_id')->references('id')->on('services')->onDelete('set null');
        });

        // Bot Performance Metrics Table
        Schema::create('telegram_bot_metrics', function (Blueprint $table) {
            $table->id();
            $table->integer('total_updates')->default(0);
            $table->integer('successful_updates')->default(0);
            $table->integer('failed_updates')->default(0);
            $table->decimal('avg_processing_time_ms', 10, 2)->default(0);
            $table->decimal('memory_usage_mb', 10, 2)->default(0);
            $table->integer('active_users')->default(0);
            $table->integer('new_users')->default(0);
            $table->json('update_type_breakdown')->nullable(); // message, callback, etc.
            $table->json('error_breakdown')->nullable();
            $table->json('performance_alerts')->nullable();
            $table->datetime('metric_timestamp');
            
            $table->index(['metric_timestamp']);
        });

        // A/B Testing Framework Table
        Schema::create('telegram_ab_tests', function (Blueprint $table) {
            $table->id();
            $table->string('test_name', 100);
            $table->string('test_type', 50); // message_variant, flow_variant, ui_variant
            $table->json('variant_config'); // configuration for each variant
            $table->integer('traffic_allocation')->default(50); // percentage
            $table->string('target_metric', 100); // conversion_rate, engagement, completion_rate
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('results')->nullable();
            $table->string('winner_variant', 50)->nullable();
            $table->decimal('confidence_level', 5, 4)->nullable();
            $table->timestamps();
            
            $table->index(['is_active', 'start_date']);
        });

        // User A/B Test Assignments Table
        Schema::create('telegram_user_ab_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ab_test_id')->constrained('telegram_ab_tests')->onDelete('cascade');
            $table->string('telegram_user_id', 20);
            $table->string('variant', 50);
            $table->timestamp('assigned_at');
            $table->boolean('converted')->default(false);
            $table->timestamp('conversion_at')->nullable();
            $table->json('conversion_data')->nullable();
            
            $table->unique(['ab_test_id', 'telegram_user_id']);
            $table->index(['variant', 'converted']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_user_ab_assignments');
        Schema::dropIfExists('telegram_ab_tests');
        Schema::dropIfExists('telegram_bot_metrics');
        Schema::dropIfExists('telegram_payment_analytics');
        Schema::dropIfExists('telegram_user_behavior');
        Schema::dropIfExists('telegram_command_metrics');
        Schema::dropIfExists('telegram_service_analytics');
        Schema::dropIfExists('telegram_user_sessions');
        Schema::dropIfExists('telegram_user_events');
    }
};