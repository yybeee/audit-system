<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // STEP 1: Ubah status ke VARCHAR dulu (agar bisa update data dengan bebas)
        try {
            DB::statement("ALTER TABLE reports MODIFY COLUMN status VARCHAR(50) DEFAULT 'submitted'");
        } catch (\Exception $e) {
            // Ignore jika sudah VARCHAR
        }

        // STEP 2: Bersihkan data yang tidak valid
        DB::statement("
            UPDATE reports 
            SET status = CASE 
                WHEN status IN ('draft', 'pending') THEN 'submitted'
                WHEN status IN ('reviewed', 'review', 'reviewing') THEN 'in_progress'
                WHEN status IN ('completed', 'done', 'resolved') THEN 'fixed'
                WHEN status IN ('approved', 'closed') THEN 'approved'
                WHEN status NOT IN ('submitted', 'in_progress', 'fixed', 'approved') THEN 'submitted'
                ELSE status
            END
        ");

        // STEP 3: Tambah/Update kolom-kolom
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
                $table->string('location')->nullable()->after('report_number');
            }
            if (!Schema::hasColumn('reports', 'issue_type')) {
                $table->string('issue_type')->nullable()->after('location');
            }
            if (!Schema::hasColumn('reports', 'description')) {
                $table->text('description')->nullable()->after('issue_type');
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
        
        // STEP 4: Ubah ke ENUM setelah data bersih
        DB::statement("ALTER TABLE reports MODIFY COLUMN status ENUM('submitted', 'in_progress', 'fixed', 'approved') DEFAULT 'submitted' NOT NULL");
    }

    public function down(): void
    {
        // Kembalikan status ke VARCHAR
        DB::statement("ALTER TABLE reports MODIFY COLUMN status VARCHAR(50) DEFAULT 'draft'");
        
        Schema::table('reports', function (Blueprint $table) {
            if (Schema::hasColumn('reports', 'approved_by')) {
                $table->dropForeign(['approved_by']);
                $table->dropColumn('approved_by');
            }
            
            $columns = [
                'location', 'issue_type', 'description', 'photos',
                'rejection_reason', 'submitted_at', 'started_at', 
                'deadline', 'deadline_reason', 'fixed_at', 'approved_at'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('reports', $column)) {
                    $table->dropColumn($column);
                }
            }
            
            // Tambah kembali kolom lama
            if (!Schema::hasColumn('reports', 'audit_date')) {
                $table->date('audit_date')->nullable()->after('report_number');
            }
            if (!Schema::hasColumn('reports', 'findings')) {
                $table->text('findings')->nullable();
            }
            if (!Schema::hasColumn('reports', 'recommendations')) {
                $table->text('recommendations')->nullable();
            }
        });
    }
};