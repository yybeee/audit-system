<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop kolom lama yang tidak dipakai
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['audit_date', 'findings', 'recommendations']);
        });

        // Tambah kolom baru
        Schema::table('reports', function (Blueprint $table) {
            // Kolom location sudah ada dari migration sebelumnya, tapi pastikan ada
            if (!Schema::hasColumn('reports', 'location')) {
                $table->string('location')->after('auditor_id');
            }
            
            $table->string('issue_type')->after('location');
            $table->text('description')->after('issue_type');
            $table->json('photos')->nullable()->after('description');
            $table->text('rejection_reason')->nullable()->after('photos');
            $table->timestamp('submitted_at')->nullable()->after('rejection_reason');
            $table->timestamp('started_at')->nullable()->after('submitted_at');
            $table->timestamp('fixed_at')->nullable()->after('started_at');
            $table->timestamp('approved_at')->nullable()->after('fixed_at');
            $table->foreignId('approved_by')->nullable()->after('approved_at')
                ->constrained('users')->onDelete('set null');
            $table->date('deadline')->nullable()->after('approved_by');
        });

        // Update enum status
        DB::statement("ALTER TABLE reports MODIFY COLUMN status ENUM('submitted', 'in_progress', 'fixed', 'approved', 'rejected') DEFAULT 'submitted'");
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn([
                'issue_type', 'description', 'photos', 'rejection_reason',
                'submitted_at', 'started_at', 'fixed_at', 'approved_at',
                'approved_by', 'deadline'
            ]);
            
            $table->date('audit_date')->after('report_number');
            $table->text('findings')->nullable();
            $table->text('recommendations')->nullable();
        });

        DB::statement("ALTER TABLE reports MODIFY COLUMN status ENUM('draft', 'submitted', 'reviewed', 'approved') DEFAULT 'draft'");
    }
};