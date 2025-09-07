<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAiSummaryToAiContentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ai_contents', function (Blueprint $table) {
            $table->longText('ai_summary')->nullable()->after('ai_sections');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_contents', function (Blueprint $table) {
            $table->dropColumn('ai_summary');
        });
    }
} 