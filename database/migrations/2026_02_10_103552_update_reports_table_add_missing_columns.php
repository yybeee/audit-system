<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Drop kolom yang tidak diperlukan
            if (Schema::hasColumn('reports', 'audit_date')) {
                $table->dropColumn('audit_date');
            }
            if (Schema::hasColumn('reports', 'findings')) {
                $table->dropColumn('findings');
            }
            if (Schema::hasColumn('reports', 'recommendations')) {
                $table->dropColumn('recommendations');
            }
            
            // Tambah kolom yang diperlukan (jika belum ada)
            if (!Schema::hasColumn('reports', 'location')) {
                $table->string('location')->after('report_number');
            }
            if (!Schema::hasColumn('reports', 'issue_type')) {
                $table->string('issue_type')->after('location');
            }
            if (!Schema::hasColumn('reports', 'description')) {
                $table->text('description')->after('issue_type');
            }
            if (!Schema::hasColumn('reports', 'photos')) {
                $table->json('photos')->nullable()->after('description');
            }
            if (!Schema::hasColumn('reports', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('photos');
            }
            if (!Schema::hasColumn('reports', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('rejection_reason');
            }
            if (!Schema::hasColumn('reports', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('submitted_at');
            }
            if (!Schema::hasColumn('reports', 'deadline')) {
                $table->date('deadline')->nullable()->after('started_at');
            }
            if (!Schema::hasColumn('reports', 'deadline_reason')) {
                $table->text('deadline_reason')->nullable()->after('deadline');
            }
            if (!Schema::hasColumn('reports', 'fixed_at')) {
                $table->timestamp('fixed_at')->nullable()->after('deadline_reason');
            }
            if (!Schema::hasColumn('reports', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('fixed_at');
            }
            if (!Schema::hasColumn('reports', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->constrained('users')->after('approved_at');
            }
        });
        
        // Update status enum
        DB::statement("ALTER TABLE reports MODIFY COLUMN status ENUM('submitted', 'in_progress', 'fixed', 'approved') DEFAULT 'submitted'");
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Kembalikan seperti semula jika rollback
            $table->dropColumn([
                'location', 'issue_type', 'description', 'photos',
                'rejection_reason', 'submitted_at', 'started_at', 
                'deadline', 'deadline_reason', 'fixed_at', 
                'approved_at', 'approved_by'
            ]);
            
            $table->date('audit_date')->after('report_number');
            $table->text('findings')->nullable();
            $table->text('recommendations')->nullable();
        });
        
        DB::statement("ALTER TABLE reports MODIFY COLUMN status ENUM('draft', 'submitted', 'reviewed', 'approved') DEFAULT 'draft'");
    }
};