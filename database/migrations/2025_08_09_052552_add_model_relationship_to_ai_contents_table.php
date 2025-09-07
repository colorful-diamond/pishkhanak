<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ai_contents', function (Blueprint $table) {
            // Add model relationship fields only if they don't exist
            if (!Schema::hasColumn('ai_contents', 'model_type')) {
                $table->string('model_type')->nullable()->after('id');
            }
            if (!Schema::hasColumn('ai_contents', 'model_id')) {
                $table->unsignedBigInteger('model_id')->nullable()->after('model_type');
            }
            
            // Add generation tracking fields
            if (!Schema::hasColumn('ai_contents', 'generation_settings')) {
                $table->json('generation_settings')->nullable()->after('status');
            }
            if (!Schema::hasColumn('ai_contents', 'generation_progress')) {
                $table->integer('generation_progress')->default(0)->after('generation_settings');
            }
            if (!Schema::hasColumn('ai_contents', 'current_generation_step')) {
                $table->string('current_generation_step')->nullable()->after('generation_progress');
            }
            if (!Schema::hasColumn('ai_contents', 'section_generation_status')) {
                $table->json('section_generation_status')->nullable()->after('current_generation_step');
            }
            if (!Schema::hasColumn('ai_contents', 'generation_started_at')) {
                $table->timestamp('generation_started_at')->nullable()->after('section_generation_status');
            }
            if (!Schema::hasColumn('ai_contents', 'generation_completed_at')) {
                $table->timestamp('generation_completed_at')->nullable()->after('generation_started_at');
            }
            
            // Add author tracking
            if (!Schema::hasColumn('ai_contents', 'author_id')) {
                $table->unsignedBigInteger('author_id')->nullable()->after('generation_completed_at');
            }
            if (!Schema::hasColumn('ai_contents', 'last_edited_by')) {
                $table->unsignedBigInteger('last_edited_by')->nullable()->after('author_id');
            }
        });
        
        // Add indexes separately
        Schema::table('ai_contents', function (Blueprint $table) {
            // For PostgreSQL, we need to check indexes differently
            $connection = config('database.default');
            
            if ($connection === 'pgsql') {
                // Check PostgreSQL indexes
                $indexes = DB::select("
                    SELECT indexname 
                    FROM pg_indexes 
                    WHERE tablename = 'ai_contents' 
                    AND schemaname = current_schema()
                ");
                $indexNames = array_column($indexes, 'indexname');
                
                if (!in_array('ai_contents_model_type_model_id_index', $indexNames)) {
                    $table->index(['model_type', 'model_id']);
                }
                if (!in_array('ai_contents_status_index', $indexNames)) {
                    $table->index('status');
                }
                if (!in_array('ai_contents_generation_progress_index', $indexNames)) {
                    $table->index('generation_progress');
                }
            } else {
                // For MySQL and other databases
                try {
                    $table->index(['model_type', 'model_id']);
                } catch (\Exception $e) {
                    // Index already exists
                }
                try {
                    $table->index('status');
                } catch (\Exception $e) {
                    // Index already exists
                }
                try {
                    $table->index('generation_progress');
                } catch (\Exception $e) {
                    // Index already exists
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_contents', function (Blueprint $table) {
            // Drop indexes first
            try {
                $table->dropIndex(['model_type', 'model_id']);
            } catch (\Exception $e) {
                // Index doesn't exist
            }
            try {
                $table->dropIndex(['status']);
            } catch (\Exception $e) {
                // Index doesn't exist
            }
            try {
                $table->dropIndex(['generation_progress']);
            } catch (\Exception $e) {
                // Index doesn't exist
            }
        });
        
        Schema::table('ai_contents', function (Blueprint $table) {
            // Drop columns if they exist
            $columnsToRemove = [
                'model_type',
                'model_id',
                'generation_settings',
                'generation_progress',
                'current_generation_step',
                'section_generation_status',
                'generation_started_at',
                'generation_completed_at',
                'author_id',
                'last_edited_by'
            ];
            
            $existingColumns = Schema::getColumnListing('ai_contents');
            
            foreach ($columnsToRemove as $column) {
                if (in_array($column, $existingColumns)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};