<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan tabel reports ada
        if (Schema::hasTable('reports')) {
            // Update semua status yang tidak valid
            DB::statement("
                UPDATE reports 
                SET status = CASE 
                    WHEN status = 'draft' THEN 'submitted'
                    WHEN status = 'reviewed' THEN 'in_progress'
                    WHEN status NOT IN ('submitted', 'in_progress', 'fixed', 'approved') THEN 'submitted'
                    ELSE status
                END
            ");
        }
    }

    public function down(): void
    {
        // Tidak perlu rollback
    }
};