<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Telegram Admins Table
        Schema::create('telegram_admins', function (Blueprint $table) {
            $table->id();
            $table->string('telegram_user_id', 20)->unique();
            $table->string('username')->nullable();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->enum('role', ['super_admin', 'admin', 'moderator', 'support', 'read_only'])->default('support');
            $table->json('permissions')->default('[]');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->integer('failed_login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->string('created_by', 20)->nullable();
            $table->timestamps();
            
            $table->index(['telegram_user_id']);
            $table->index(['role', 'is_active']);
        });

        // Admin Sessions Table
        Schema::create('telegram_admin_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('telegram_admins')->onDelete('cascade');
            $table->string('session_token', 64)->unique();
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent_hash', 64)->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('last_activity_at')->useCurrent();
            $table->timestamps();
            
            $table->index(['session_token']);
            $table->index(['expires_at']);
            $table->index(['admin_id', 'expires_at']);
        });

        // Audit Logs Table
        Schema::create('telegram_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('telegram_admins')->onDelete('set null');
            $table->string('action', 100);
            $table->string('resource_type', 50)->nullable();
            $table->string('resource_id', 50)->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent_hash', 64)->nullable();
            $table->boolean('success')->default(true);
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->index(['admin_id', 'action']);
            $table->index(['created_at']);
            $table->index(['action', 'success']);
        });

        // Security Events Table
        Schema::create('telegram_security_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type', 50);
            $table->foreignId('admin_id')->nullable()->constrained('telegram_admins')->onDelete('set null');
            $table->string('telegram_user_id', 20)->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->json('details')->default('{}');
            $table->enum('severity', ['info', 'warning', 'error', 'critical'])->default('info');
            $table->timestamps();
            
            $table->index(['event_type', 'severity']);
            $table->index(['created_at']);
            $table->index(['telegram_user_id']);
        });

        // API Tokens Table
        Schema::create('api_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('token_hash', 64)->unique();
            $table->json('permissions')->default('[]');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('telegram_admins')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['token_hash']);
            $table->index(['is_active', 'expires_at']);
        });

        // Telegram Posts Table
        Schema::create('telegram_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('status', ['draft', 'scheduled', 'published', 'archived'])->default('draft');
            $table->timestamp('scheduled_for')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('channel_id', 20)->nullable();
            $table->string('message_id', 20)->nullable();
            $table->foreignId('created_by')->constrained('telegram_admins')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('telegram_admins')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['status']);
            $table->index(['scheduled_for']);
            $table->index(['created_by']);
        });

        // AI Content Templates Table
        Schema::create('ai_content_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('prompt_template');
            $table->json('parameters')->default('{}');
            $table->string('category', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0);
            $table->foreignId('created_by')->constrained('telegram_admins')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['category', 'is_active']);
            $table->index(['usage_count']);
        });

        // Wallet Audit Logs Table
        Schema::create('wallet_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('wallet_id');
            $table->foreignId('admin_id')->nullable()->constrained('telegram_admins')->onDelete('set null');
            $table->string('action', 50);
            $table->decimal('amount', 20, 8)->nullable();
            $table->decimal('old_balance', 20, 8)->nullable();
            $table->decimal('new_balance', 20, 8)->nullable();
            $table->text('reason')->nullable();
            $table->string('reference_id', 100)->nullable();
            $table->timestamps();
            
            $table->index(['wallet_id', 'created_at']);
            $table->index(['admin_id', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_audit_logs');
        Schema::dropIfExists('ai_content_templates');
        Schema::dropIfExists('telegram_posts');
        Schema::dropIfExists('api_tokens');
        Schema::dropIfExists('telegram_security_events');
        Schema::dropIfExists('telegram_audit_logs');
        Schema::dropIfExists('telegram_admin_sessions');
        Schema::dropIfExists('telegram_admins');
    }
};