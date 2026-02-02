<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\AuditType;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Departments
        $departments = [
            ['name' => 'HRGA', 'code' => 'HRGA', 'description' => 'Human Resources & General Affairs'],
            ['name' => 'Accounting', 'code' => 'ACC', 'description' => 'Accounting & Finance'],
            ['name' => 'Maintenance', 'code' => 'MTC', 'description' => 'Maintenance & Engineering'],
            ['name' => 'IT', 'code' => 'IT', 'description' => 'Information Technology'],
            ['name' => 'Production', 'code' => 'PROD', 'description' => 'Production Department'],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }

        // Create Audit Types
        $auditTypes = [
            ['name' => 'HSE (Health Safety Environment)', 'code' => 'HSE', 'description' => 'Health, Safety & Environment Audit'],
            ['name' => 'K3 (Keselamatan dan Kesehatan Kerja)', 'code' => 'K3', 'description' => 'Work Safety & Health Audit'],
            ['name' => '5R (Ringkas, Rapi, Resik, Rawat, Rajin)', 'code' => '5R', 'description' => '5R Workplace Audit'],
            ['name' => 'ISO Audit', 'code' => 'ISO', 'description' => 'ISO Standard Compliance Audit'],
            ['name' => 'Internal Audit', 'code' => 'IA', 'description' => 'Internal Process Audit'],
        ];

        foreach ($auditTypes as $type) {
            AuditType::create($type);
        }

        // Create Super Admin
        User::create([
            'name' => 'Admin HRGA',
            'email' => 'admin@company.com',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'super_admin',
            'department_id' => 1, // HRGA
        ]);

        // Create HSE Auditor
        User::create([
            'name' => 'Auditor HSE',
            'email' => 'auditor@company.com',
            'username' => 'auditor',
            'password' => Hash::make('auditor123'),
            'role' => 'auditor',
            'department_id' => null,
        ]);

        // Create Staff Departemen
        User::create([
            'name' => 'Staff HRGA',
            'email' => 'hrga@company.com',
            'username' => 'hrga',
            'password' => Hash::make('hrga123'),
            'role' => 'staff_departemen',
            'department_id' => 1, // HRGA
        ]);

        User::create([
            'name' => 'Staff Maintenance',
            'email' => 'maintenance@company.com',
            'username' => 'maintenance',
            'password' => Hash::make('maintenance123'),
            'role' => 'staff_departemen',
            'department_id' => 3, // Maintenance
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('=================================');
        $this->command->info('Login Credentials:');
        $this->command->info('=================================');
        $this->command->info('Super Admin: admin / admin123');
        $this->command->info('Auditor: auditor / auditor123');
        $this->command->info('Staff HRGA: hrga / hrga123');
        $this->command->info('Staff Maintenance: maintenance / maintenance123');
        $this->command->info('=================================');
    }
}