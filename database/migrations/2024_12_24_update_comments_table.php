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
        Schema::table('comments', function (Blueprint $table) {
            if (!Schema::hasColumn('comments', 'is_approved')) {
                $table->boolean('is_approved')->default(false)->after('content');
            }
            if (!Schema::hasColumn('comments', 'likes_count')) {
                $table->unsignedInteger('likes_count')->default(0)->after('is_approved');
            }
            if (!Schema::hasColumn('comments', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('post_id');
                $table->foreign('parent_id')->references('id')->on('comments')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            if (Schema::hasColumn('comments', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
            if (Schema::hasColumn('comments', 'likes_count')) {
                $table->dropColumn('likes_count');
            }
            if (Schema::hasColumn('comments', 'is_approved')) {
                $table->dropColumn('is_approved');
            }
        });
    }
};
