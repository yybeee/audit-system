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
            ['name' => 'HRGA', 'code' => 'HRGA', 'description' => 'Human Resources & General Affairs', 'is_active' => true],
            ['name' => 'Accounting', 'code' => 'ACC', 'description' => 'Accounting & Finance', 'is_active' => true],
            ['name' => 'Maintenance', 'code' => 'MTC', 'description' => 'Maintenance & Engineering', 'is_active' => true],
            ['name' => 'IT', 'code' => 'IT', 'description' => 'Information Technology', 'is_active' => true],
            ['name' => 'Production', 'code' => 'PROD', 'description' => 'Production Department', 'is_active' => true],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['code' => $dept['code']], $dept);
        }

        // Create Audit Types
        $auditTypes = [
            ['name' => 'HSE (Health Safety Environment)', 'code' => 'HSE', 'description' => 'Health, Safety & Environment Audit', 'is_active' => true],
            ['name' => 'K3 (Keselamatan dan Kesehatan Kerja)', 'code' => 'K3', 'description' => 'Work Safety & Health Audit', 'is_active' => true],
            ['name' => '5R (Ringkas, Rapi, Resik, Rawat, Rajin)', 'code' => '5R', 'description' => '5R Workplace Audit', 'is_active' => true],
            ['name' => 'ISO Audit', 'code' => 'ISO', 'description' => 'ISO Standard Compliance Audit', 'is_active' => true],
            ['name' => 'Internal Audit', 'code' => 'IA', 'description' => 'Internal Process Audit', 'is_active' => true],
        ];

        foreach ($auditTypes as $type) {
            AuditType::firstOrCreate(['code' => $type['code']], $type);
        }

        // Create Super Admin
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin HRGA',
                'email' => 'admin@company.com',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin',
                'department_id' => 1,
                'is_active' => true,
            ]
        );

        // Create HSE Auditor
        User::firstOrCreate(
            ['username' => 'auditor'],
            [
                'name' => 'Auditor HSE',
                'email' => 'auditor@company.com',
                'password' => Hash::make('auditor123'),
                'role' => 'auditor',
                'department_id' => null,
                'is_active' => true,
            ]
        );

        // Create Staff Departemen
        User::firstOrCreate(
            ['username' => 'hrga'],
            [
                'name' => 'Staff HRGA',
                'email' => 'hrga@company.com',
                'password' => Hash::make('hrga123'),
                'role' => 'staff_departemen',
                'department_id' => 1,
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['username' => 'maintenance'],
            [
                'name' => 'Staff Maintenance',
                'email' => 'maintenance@company.com',
                'password' => Hash::make('maintenance123'),
                'role' => 'staff_departemen',
                'department_id' => 3,
                'is_active' => true,
            ]
        );

        $this->command->info('✅ Database seeded successfully!');
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