<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            
            // New fields from Post.php
            $table->text('summary')->nullable();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->json('images')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->boolean('featured')->default(false);
            $table->unsignedBigInteger('views')->default(0);
            $table->unsignedBigInteger('likes')->default(0);
            $table->unsignedBigInteger('shares')->default(0);
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');

            // AI Generated Fields
            $table->string('ai_title')->nullable();
            $table->text('ai_summary')->nullable();
            $table->text('ai_description')->nullable();
            $table->string('ai_thumbnail')->nullable();
            $table->json('ai_images')->nullable();
            $table->json('ai_headings')->nullable();
            $table->json('ai_sections')->nullable();
            $table->text('ai_content')->nullable();

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
            $table->json('json_ld')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}