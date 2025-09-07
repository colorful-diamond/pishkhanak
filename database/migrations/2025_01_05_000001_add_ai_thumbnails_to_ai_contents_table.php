<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAiThumbnailsToAiContentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ai_contents', function (Blueprint $table) {
            $table->json('ai_thumbnails')->nullable()->after('ai_summary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_contents', function (Blueprint $table) {
            $table->dropColumn('ai_thumbnails');
        });
    }
} 