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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->foreignId('category_id')->constrained('service_categories')->onDelete('cascade');
            $table->text('summary')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('draft');
            $table->boolean('featured')->default(false);
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->decimal('price', 10, 2)->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('services')->onDelete('set null');

            // Stats
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('likes')->default(0);
            $table->unsignedInteger('shares')->default(0);

            // SEO Fields
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('twitter_image')->nullable();

            // Schema Fields
            $table->json('schema')->nullable();
            $table->json('faqs')->nullable();
            $table->json('related_articles')->nullable();
            $table->boolean('comment_status')->default(true);

            // Media
            $table->string('icon')->nullable();

            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};